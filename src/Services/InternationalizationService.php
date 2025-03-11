<?php

namespace Bunny\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;
use Bunny\Models\Currency;
use Bunny\Models\Language;
use Bunny\Models\TaxRule;
use Bunny\Models\ShippingZone;

class InternationalizationService
{
    protected $cacheKey = 'bunny_i18n_';
    protected $ttl = 3600; // 1 hour

    public function setLocale($locale)
    {
        if (!$this->isLocaleAvailable($locale)) {
            throw new \Exception("Locale '{$locale}' is not available");
        }

        App::setLocale($locale);
        session(['locale' => $locale]);
    }

    public function getCurrentLocale()
    {
        return session('locale', config('app.locale'));
    }

    public function getAvailableLocales()
    {
        return Cache::remember($this->cacheKey . 'locales', $this->ttl, function () {
            return Language::where('is_active', true)
                ->select('code', 'name', 'native_name')
                ->get();
        });
    }

    public function translate($key, $locale = null)
    {
        $locale = $locale ?? $this->getCurrentLocale();
        return __("bunny-ecommerce::{$key}", [], $locale);
    }

    public function formatCurrency($amount, $currency = null)
    {
        $currency = $currency ?? $this->getCurrentCurrency();
        $formatter = new \NumberFormatter($this->getCurrentLocale(), \NumberFormatter::CURRENCY);
        
        return $formatter->formatCurrency($amount, $currency->code);
    }

    public function convertCurrency($amount, $fromCurrency, $toCurrency)
    {
        $rate = $this->getExchangeRate($fromCurrency, $toCurrency);
        return $amount * $rate;
    }

    public function setCurrency($currencyCode)
    {
        if (!$this->isCurrencyAvailable($currencyCode)) {
            throw new \Exception("Currency '{$currencyCode}' is not available");
        }

        session(['currency' => $currencyCode]);
    }

    public function getCurrentCurrency()
    {
        $currencyCode = session('currency', config('bunny.ecommerce.default_currency'));
        return $this->getCurrency($currencyCode);
    }

    public function getAvailableCurrencies()
    {
        return Cache::remember($this->cacheKey . 'currencies', $this->ttl, function () {
            return Currency::where('is_active', true)
                ->select('code', 'name', 'symbol')
                ->get();
        });
    }

    public function calculateTax($amount, $country, $state = null)
    {
        $taxRule = $this->getTaxRule($country, $state);
        return $amount * ($taxRule->rate / 100);
    }

    public function getShippingZones($country)
    {
        return Cache::remember($this->cacheKey . 'shipping_zones_' . $country, $this->ttl, function () use ($country) {
            return ShippingZone::where('is_active', true)
                ->where(function ($query) use ($country) {
                    $query->where('countries', 'like', "%{$country}%")
                        ->orWhereNull('countries');
                })
                ->orderBy('priority')
                ->get();
        });
    }

    public function getShippingCost($country, $state = null, $weight = 0)
    {
        $zones = $this->getShippingZones($country);
        
        foreach ($zones as $zone) {
            if ($this->isInShippingZone($zone, $country, $state)) {
                return $this->calculateShippingCost($zone, $weight);
            }
        }

        return 0;
    }

    protected function isLocaleAvailable($locale)
    {
        return $this->getAvailableLocales()->contains('code', $locale);
    }

    protected function isCurrencyAvailable($currencyCode)
    {
        return $this->getAvailableCurrencies()->contains('code', $currencyCode);
    }

    protected function getCurrency($code)
    {
        return Cache::remember($this->cacheKey . 'currency_' . $code, $this->ttl, function () use ($code) {
            return Currency::where('code', $code)->firstOrFail();
        });
    }

    protected function getExchangeRate($fromCurrency, $toCurrency)
    {
        $cacheKey = $this->cacheKey . "exchange_rate_{$fromCurrency}_{$toCurrency}";
        
        return Cache::remember($cacheKey, $this->ttl, function () use ($fromCurrency, $toCurrency) {
            // Implement exchange rate API call here
            // For now, return a dummy rate
            return 1.0;
        });
    }

    protected function getTaxRule($country, $state = null)
    {
        $cacheKey = $this->cacheKey . "tax_rule_{$country}" . ($state ? "_{$state}" : '');
        
        return Cache::remember($cacheKey, $this->ttl, function () use ($country, $state) {
            return TaxRule::where('country', $country)
                ->where(function ($query) use ($state) {
                    $query->where('state', $state)
                        ->orWhereNull('state');
                })
                ->where('is_active', true)
                ->firstOrFail();
        });
    }

    protected function isInShippingZone($zone, $country, $state = null)
    {
        if (empty($zone->countries)) {
            return true;
        }

        $countries = explode(',', $zone->countries);
        if (!in_array($country, $countries)) {
            return false;
        }

        if ($state && !empty($zone->states)) {
            $states = explode(',', $zone->states);
            return in_array($state, $states);
        }

        return true;
    }

    protected function calculateShippingCost($zone, $weight)
    {
        $baseCost = $zone->base_cost;
        $weightCost = $weight * $zone->weight_cost;
        return $baseCost + $weightCost;
    }
} 