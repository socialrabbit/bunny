<?php

namespace Bunny\Services;

use Illuminate\Support\Facades\Log;
use Bunny\Models\Order;
use Bunny\Models\Payment;
use Bunny\Events\PaymentProcessed;
use Bunny\Events\PaymentFailed;
use Bunny\Events\RefundProcessed;

class PaymentService
{
    protected $gateways = [];
    protected $defaultGateway;

    public function __construct()
    {
        $this->registerGateways();
        $this->defaultGateway = config('bunny.ecommerce.default_payment_gateway');
    }

    protected function registerGateways()
    {
        $this->gateways = [
            'stripe' => new \Bunny\Payment\StripeGateway(),
            'paypal' => new \Bunny\Payment\PayPalGateway(),
            'razorpay' => new \Bunny\Payment\RazorpayGateway(),
        ];
    }

    public function processPayment(Order $order, array $paymentData)
    {
        try {
            $gateway = $this->getGateway($paymentData['gateway'] ?? $this->defaultGateway);
            
            $payment = $gateway->process([
                'amount' => $order->total,
                'currency' => $order->currency,
                'order_id' => $order->id,
                'customer' => $order->customer,
                'items' => $order->items,
            ]);

            $this->savePayment($order, $payment);
            event(new PaymentProcessed($payment));

            return $payment;
        } catch (\Exception $e) {
            Log::error('Payment processing failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            event(new PaymentFailed($order, $e->getMessage()));
            throw $e;
        }
    }

    public function processRefund(Payment $payment, array $refundData)
    {
        try {
            $gateway = $this->getGateway($payment->gateway);
            
            $refund = $gateway->refund([
                'payment_id' => $payment->gateway_payment_id,
                'amount' => $refundData['amount'] ?? $payment->amount,
                'reason' => $refundData['reason'] ?? null,
            ]);

            $this->saveRefund($payment, $refund);
            event(new RefundProcessed($refund));

            return $refund;
        } catch (\Exception $e) {
            Log::error('Refund processing failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function handleSubscription(array $subscriptionData)
    {
        $gateway = $this->getGateway($subscriptionData['gateway'] ?? $this->defaultGateway);
        
        return $gateway->createSubscription([
            'plan_id' => $subscriptionData['plan_id'],
            'customer_id' => $subscriptionData['customer_id'],
            'payment_method' => $subscriptionData['payment_method'],
            'trial_days' => $subscriptionData['trial_days'] ?? 0,
        ]);
    }

    public function cancelSubscription($subscriptionId, $gateway = null)
    {
        $gateway = $this->getGateway($gateway ?? $this->defaultGateway);
        return $gateway->cancelSubscription($subscriptionId);
    }

    public function handleWebhook($gateway, array $payload)
    {
        $gateway = $this->getGateway($gateway);
        return $gateway->handleWebhook($payload);
    }

    protected function getGateway($name)
    {
        if (!isset($this->gateways[$name])) {
            throw new \Exception("Payment gateway '{$name}' not found");
        }

        return $this->gateways[$name];
    }

    protected function savePayment(Order $order, array $paymentData)
    {
        return Payment::create([
            'order_id' => $order->id,
            'amount' => $paymentData['amount'],
            'currency' => $paymentData['currency'],
            'gateway' => $paymentData['gateway'],
            'gateway_payment_id' => $paymentData['payment_id'],
            'status' => $paymentData['status'],
            'metadata' => $paymentData['metadata'] ?? [],
        ]);
    }

    protected function saveRefund(Payment $payment, array $refundData)
    {
        return $payment->refunds()->create([
            'amount' => $refundData['amount'],
            'currency' => $refundData['currency'],
            'gateway_refund_id' => $refundData['refund_id'],
            'status' => $refundData['status'],
            'reason' => $refundData['reason'],
            'metadata' => $refundData['metadata'] ?? [],
        ]);
    }

    public function calculateTax($amount, $country, $state = null)
    {
        // Implement tax calculation logic based on location
        $taxRate = $this->getTaxRate($country, $state);
        return $amount * ($taxRate / 100);
    }

    protected function getTaxRate($country, $state = null)
    {
        // Implement tax rate lookup logic
        return config("bunny.ecommerce.tax_rates.{$country}" . ($state ? ".{$state}" : ''), 0);
    }
} 