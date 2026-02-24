<?php
declare(strict_types=1);

namespace App\Core;

final class Session
{
    private const KEY_FINGERPRINT_IP = '_fingerprint_ip';
    private const KEY_FINGERPRINT_UA = '_fingerprint_ua';
    private const KEY_LAST_REGENERATED = '_last_regenerated';
    private const KEY_FLASH = '_flash';
    private const KEY_CSRF = '_csrf_token';

    /** @var int */
    private $regenerateInterval;

    public function __construct(int $regenerateInterval = 300)
    {
        $this->regenerateInterval = $regenerateInterval;
    }

    public function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $this->enforceSessionSecurity();
            return;
        }

        $isHttps = $this->isHttps();

        if (PHP_VERSION_ID >= 70300) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => $isHttps,
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
        } else {
            session_set_cookie_params(0, '/; samesite=Strict', '', $isHttps, true);
        }

        session_name('MASTERVAULTSESSID');
        session_start();

        $this->enforceSessionSecurity();
    }

    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function destroy(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'] ?? '/',
                $params['domain'] ?? '',
                (bool) ($params['secure'] ?? false),
                true
            );
        }

        session_destroy();
    }

    public function setFlash(string $key, $value): void
    {
        if (!isset($_SESSION[self::KEY_FLASH]) || !is_array($_SESSION[self::KEY_FLASH])) {
            $_SESSION[self::KEY_FLASH] = [];
        }

        $_SESSION[self::KEY_FLASH][$key] = $value;
    }

    public function getFlash(string $key, $default = null)
    {
        $flashMessages = $_SESSION[self::KEY_FLASH] ?? [];

        if (!is_array($flashMessages) || !array_key_exists($key, $flashMessages)) {
            return $default;
        }

        $value = $flashMessages[$key];
        unset($_SESSION[self::KEY_FLASH][$key]);

        if ($_SESSION[self::KEY_FLASH] === []) {
            unset($_SESSION[self::KEY_FLASH]);
        }

        return $value;
    }

    public function csrfToken(): string
    {
        $token = $_SESSION[self::KEY_CSRF] ?? null;

        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            $_SESSION[self::KEY_CSRF] = $token;
        }

        return $token;
    }

    public function validateCsrfToken(string $token): bool
    {
        $storedToken = $_SESSION[self::KEY_CSRF] ?? '';

        if (!is_string($storedToken) || $storedToken === '' || $token === '') {
            return false;
        }

        return hash_equals($storedToken, $token);
    }

    private function enforceSessionSecurity(): void
    {
        $currentIp = $this->getClientIp();
        $currentUa = $this->getUserAgent();

        $storedIp = $_SESSION[self::KEY_FINGERPRINT_IP] ?? null;
        $storedUa = $_SESSION[self::KEY_FINGERPRINT_UA] ?? null;

        if (!is_string($storedIp) || !is_string($storedUa)) {
            $_SESSION[self::KEY_FINGERPRINT_IP] = $currentIp;
            $_SESSION[self::KEY_FINGERPRINT_UA] = $currentUa;
            $_SESSION[self::KEY_LAST_REGENERATED] = time();
            session_regenerate_id(true);
            return;
        }

        $isIpMatch = hash_equals($storedIp, $currentIp);
        $isUaMatch = hash_equals($storedUa, $currentUa);

        if (!$isIpMatch || !$isUaMatch) {
            $this->destroy();
            throw new \RuntimeException('Session validation failed. Please log in again.');
        }

        $lastRegenerated = (int) ($_SESSION[self::KEY_LAST_REGENERATED] ?? 0);
        if ($lastRegenerated <= 0 || (time() - $lastRegenerated) >= $this->regenerateInterval) {
            session_regenerate_id(true);
            $_SESSION[self::KEY_LAST_REGENERATED] = time();
        }
    }

    private function getClientIp(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        return is_string($ip) && $ip !== '' ? $ip : '0.0.0.0';
    }

    private function getUserAgent(): string
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown-agent';
        return is_string($ua) && $ua !== '' ? $ua : 'unknown-agent';
    }

    private function isHttps(): bool
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }

        if (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443) {
            return true;
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            return strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https';
        }

        return false;
    }
}
