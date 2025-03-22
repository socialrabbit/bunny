<?php

namespace Bunny\Services;

use Illuminate\Support\Facades\{
    Cache,
    Log,
    Hash,
    Config,
    RateLimiter,
    Request,
    Session,
    Cookie
};
use Illuminate\Support\Str;
use Bunny\Models\{
    User,
    SecurityLog,
    TwoFactorAuth,
    ApiKey,
    IpBlock,
    SecuritySetting
};
use Bunny\Events\{
    SecurityAlert,
    LoginAttempt,
    ApiAccess
};

class SecurityService
{
    protected $cache;
    protected $settings;

    public function __construct()
    {
        $this->cache = Cache::tags(['security']);
        $this->settings = $this->loadSecuritySettings();
    }

    /**
     * Initialize security service
     */
    public function initialize()
    {
        $this->setupSecurityHeaders();
        $this->setupRateLimiting();
        $this->setupSessionSecurity();
        $this->setupCookieSecurity();
    }

    /**
     * Load security settings
     */
    protected function loadSecuritySettings()
    {
        return $this->cache->remember('security.settings', 3600, function () {
            return SecuritySetting::first() ?? $this->createDefaultSettings();
        });
    }

    /**
     * Create default security settings
     */
    protected function createDefaultSettings()
    {
        return SecuritySetting::create([
            'two_factor_enabled' => true,
            'password_expiry_days' => 90,
            'max_login_attempts' => 5,
            'lockout_duration' => 30,
            'session_timeout' => 120,
            'require_strong_password' => true,
            'password_min_length' => 8,
            'require_special_chars' => true,
            'require_numbers' => true,
            'require_uppercase' => true,
            'require_lowercase' => true,
            'api_rate_limit' => 60,
            'api_rate_limit_window' => 1,
            'enable_ip_blocking' => true,
            'enable_brute_force_protection' => true,
            'enable_xss_protection' => true,
            'enable_csrf_protection' => true,
            'enable_sql_injection_protection' => true,
            'enable_file_upload_protection' => true,
            'allowed_file_types' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'],
            'max_file_size' => 5,
            'enable_audit_logging' => true,
            'enable_notifications' => true,
        ]);
    }

    /**
     * Setup security headers
     */
    protected function setupSecurityHeaders()
    {
        $headers = [
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-XSS-Protection' => '1; mode=block',
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:;",
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
        ];

        foreach ($headers as $key => $value) {
            header("{$key}: {$value}");
        }
    }

    /**
     * Setup rate limiting
     */
    protected function setupRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute($this->settings->api_rate_limit);
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute($this->settings->max_login_attempts);
        });
    }

    /**
     * Setup session security
     */
    protected function setupSessionSecurity()
    {
        Config::set('session.lifetime', $this->settings->session_timeout);
        Config::set('session.secure', true);
        Config::set('session.http_only', true);
        Config::set('session.same_site', 'lax');
    }

    /**
     * Setup cookie security
     */
    protected function setupCookieSecurity()
    {
        Config::set('session.cookie_secure', true);
        Config::set('session.cookie_http_only', true);
        Config::set('session.cookie_same_site', 'lax');
    }

    /**
     * Validate password strength
     */
    public function validatePasswordStrength($password)
    {
        $errors = [];

        if (strlen($password) < $this->settings->password_min_length) {
            $errors[] = "Password must be at least {$this->settings->password_min_length} characters long.";
        }

        if ($this->settings->require_special_chars && !preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character.';
        }

        if ($this->settings->require_numbers && !preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number.';
        }

        if ($this->settings->require_uppercase && !preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter.';
        }

        if ($this->settings->require_lowercase && !preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter.';
        }

        return $errors;
    }

    /**
     * Check if password needs to be changed
     */
    public function checkPasswordExpiry($user)
    {
        if (!$this->settings->password_expiry_days) {
            return false;
        }

        $lastPasswordChange = $user->password_changed_at ?? $user->created_at;
        return $lastPasswordChange->addDays($this->settings->password_expiry_days)->isPast();
    }

    /**
     * Setup two-factor authentication
     */
    public function setupTwoFactorAuth($user)
    {
        $secret = Str::random(32);
        $qrCode = $this->generateQRCode($secret, $user->email);

        $twoFactor = TwoFactorAuth::updateOrCreate(
            ['user_id' => $user->id],
            [
                'secret' => $secret,
                'enabled' => true,
                'backup_codes' => $this->generateBackupCodes(),
            ]
        );

        return [
            'secret' => $secret,
            'qr_code' => $qrCode,
            'backup_codes' => $twoFactor->backup_codes,
        ];
    }

    /**
     * Verify two-factor authentication code
     */
    public function verifyTwoFactorCode($user, $code)
    {
        $twoFactor = $user->twoFactorAuth;

        if (!$twoFactor || !$twoFactor->enabled) {
            return false;
        }

        if (in_array($code, $twoFactor->backup_codes)) {
            $backupCodes = collect($twoFactor->backup_codes)
                ->reject(fn($backupCode) => $backupCode === $code)
                ->values()
                ->toArray();

            $twoFactor->update(['backup_codes' => $backupCodes]);
            return true;
        }

        return $this->verifyTOTP($twoFactor->secret, $code);
    }

    /**
     * Generate API key
     */
    public function generateApiKey($user, $name)
    {
        $key = Str::random(64);
        $hashedKey = Hash::make($key);

        ApiKey::create([
            'user_id' => $user->id,
            'name' => $name,
            'key' => $hashedKey,
            'last_used_at' => null,
            'expires_at' => now()->addDays(30),
        ]);

        return $key;
    }

    /**
     * Validate API key
     */
    public function validateApiKey($key)
    {
        $apiKey = ApiKey::where('expires_at', '>', now())
            ->where('revoked', false)
            ->get()
            ->first(function ($apiKey) use ($key) {
                return Hash::check($key, $apiKey->key);
            });

        if ($apiKey) {
            $apiKey->update(['last_used_at' => now()]);
            event(new ApiAccess($apiKey));
            return true;
        }

        return false;
    }

    /**
     * Check for IP blocking
     */
    public function checkIpBlocking($ip)
    {
        if (!$this->settings->enable_ip_blocking) {
            return false;
        }

        return IpBlock::where('ip', $ip)
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * Block IP address
     */
    public function blockIp($ip, $reason, $duration = 24)
    {
        IpBlock::create([
            'ip' => $ip,
            'reason' => $reason,
            'expires_at' => now()->addHours($duration),
        ]);

        event(new SecurityAlert("IP {$ip} blocked for {$reason}"));
    }

    /**
     * Log security event
     */
    public function logSecurityEvent($type, $description, $userId = null)
    {
        if (!$this->settings->enable_audit_logging) {
            return;
        }

        SecurityLog::create([
            'user_id' => $userId,
            'type' => $type,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Generate QR code for 2FA
     */
    protected function generateQRCode($secret, $email)
    {
        $issuer = config('app.name');
        $otpauth = "otpauth://totp/{$issuer}:{$email}?secret={$secret}&issuer={$issuer}";
        
        return "https://chart.googleapis.com/chart?cht=qr&chs=200x200&chl=" . urlencode($otpauth);
    }

    /**
     * Generate backup codes for 2FA
     */
    protected function generateBackupCodes()
    {
        return collect(range(1, 8))->map(function () {
            return Str::random(10);
        })->toArray();
    }

    /**
     * Verify TOTP code
     */
    protected function verifyTOTP($secret, $code)
    {
        // Implement TOTP verification logic here
        // You can use a package like spomky-labs/otphp
        return true;
    }

    /**
     * Scan for security vulnerabilities
     */
    public function scanForVulnerabilities()
    {
        $vulnerabilities = [];

        // Check for outdated dependencies
        $vulnerabilities['dependencies'] = $this->checkDependencies();

        // Check for misconfigurations
        $vulnerabilities['configurations'] = $this->checkConfigurations();

        // Check for file permissions
        $vulnerabilities['permissions'] = $this->checkFilePermissions();

        // Check for security headers
        $vulnerabilities['headers'] = $this->checkSecurityHeaders();

        return $vulnerabilities;
    }

    /**
     * Check dependencies for vulnerabilities
     */
    protected function checkDependencies()
    {
        // Implement dependency checking logic
        return [];
    }

    /**
     * Check for misconfigurations
     */
    protected function checkConfigurations()
    {
        $issues = [];

        if (!config('app.debug')) {
            $issues[] = 'Debug mode is enabled in production';
        }

        if (!config('session.secure')) {
            $issues[] = 'Session cookies are not secure';
        }

        return $issues;
    }

    /**
     * Check file permissions
     */
    protected function checkFilePermissions()
    {
        $issues = [];
        $paths = [
            storage_path('app'),
            storage_path('framework'),
            storage_path('logs'),
        ];

        foreach ($paths as $path) {
            if (!is_writable($path)) {
                $issues[] = "Directory {$path} is not writable";
            }
        }

        return $issues;
    }

    /**
     * Check security headers
     */
    protected function checkSecurityHeaders()
    {
        $issues = [];
        $headers = getallheaders();

        if (!isset($headers['X-Frame-Options'])) {
            $issues[] = 'X-Frame-Options header is missing';
        }

        if (!isset($headers['X-XSS-Protection'])) {
            $issues[] = 'X-XSS-Protection header is missing';
        }

        return $issues;
    }
}