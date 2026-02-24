<?php
declare(strict_types=1);

namespace App\Core;

final class Middleware
{
    /** @var Session */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function setSecurityHeaders(): void
    {
        $isHttps = $this->isHttps();

        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
        header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:; connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self';");
        header('X-XSS-Protection: 1; mode=block');

        if ($isHttps) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }
    }

    public function isAuthenticated(): bool
    {
        $userId = $this->session->get('auth_user_id');
        $isLoggedIn = $this->session->get('is_authenticated', false);

        return is_int($userId) && $userId > 0 && $isLoggedIn === true;
    }

    public function requireRole(string $role): void
    {
        if (!$this->isAuthenticated()) {
            http_response_code(401);
            throw new \RuntimeException('Unauthorized access. Authentication required.');
        }

        $currentRole = $this->session->get('auth_user_role');

        if (!is_string($currentRole) || !hash_equals($role, $currentRole)) {
            http_response_code(403);
            throw new \RuntimeException('Forbidden access. Insufficient privileges.');
        }
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
