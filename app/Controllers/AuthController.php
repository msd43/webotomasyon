<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use PDO;
use Throwable;

final class AuthController extends Controller
{
    /** @var Session */
    private $session;

    /** @var PDO */
    private $db;

    public function __construct(Session $session)
    {
        parent::__construct(dirname(__DIR__, 2) . '/resources/views');

        $this->session = $session;
        $this->session->start();

        /** @var array{database: array<string, mixed>} $config */
        $config = require dirname(__DIR__, 2) . '/config.php';
        $this->db = Database::getInstance($config['database'])->getConnection();
    }

    public function login(): void
    {
        $csrfToken = $this->session->csrfToken();
        $error = $this->session->getFlash('error');

        $this->view('auth.login', [
            'csrfToken' => $csrfToken,
            'error' => is_string($error) ? $error : null,
        ]);
    }

    public function authenticate(): void
    {
        $csrfToken = (string) ($_POST['csrf_token'] ?? '');

        if (!$this->session->validateCsrfToken($csrfToken)) {
            http_response_code(403);
            $this->session->setFlash('error', 'Güvenlik doğrulaması başarısız. Lütfen tekrar deneyin.');
            $this->redirect('/login');
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            $this->session->setFlash('error', 'Hatalı e-posta veya şifre');
            $this->redirect('/login');
        }

        try {
            $stmt = $this->db->prepare(
                'SELECT id, role, password_hash, status, email FROM users WHERE LOWER(email) = LOWER(:email) LIMIT 1'
            );
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Throwable $exception) {
            $this->session->setFlash('error', 'Giriş işlemi sırasında sistem hatası oluştu.');
            $this->redirect('/login');
            return;
        }

        if (!is_array($user)) {
            $this->session->setFlash('error', 'Hatalı e-posta veya şifre');
            $this->redirect('/login');
        }

        $status = (int) ($user['status'] ?? 0);
        $passwordHash = (string) ($user['password_hash'] ?? '');

        if ($status !== 1 || !$this->verifyPassword($password, $passwordHash)) {
            $this->session->setFlash('error', 'Hatalı e-posta veya şifre');
            $this->redirect('/login');
        }

        if ($this->shouldRehashToBcrypt($passwordHash) && password_verify($password, $passwordHash)) {
            $rehash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            if ($rehash !== false) {
                $updateStmt = $this->db->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id');
                $updateStmt->execute([
                    ':password_hash' => $rehash,
                    ':id' => (int) $user['id'],
                ]);
            }
        }

        session_regenerate_id(true);

        $this->session->set('is_authenticated', true);
        $this->session->set('auth_user_id', (int) $user['id']);
        $this->session->set('auth_user_role', (string) $user['role']);
        $this->session->set('auth_user_email', (string) ($user['email'] ?? $email));

        $role = (string) $user['role'];

        if (hash_equals('admin', $role)) {
            $this->redirect('/admin/dashboard');
        }

        if (hash_equals('client', $role)) {
            $this->redirect('/client/dashboard');
        }

        $this->session->destroy();
        $this->session->start();
        $this->session->setFlash('error', 'Yetkisiz kullanıcı rolü tespit edildi.');
        $this->redirect('/login');
    }

    public function logout(): void
    {
        $this->session->destroy();
        $this->redirect('/login');
    }

    private function verifyPassword(string $plainPassword, string $storedHash): bool
    {
        if ($storedHash === '') {
            return false;
        }

        if (password_verify($plainPassword, $storedHash)) {
            return true;
        }

        if (strpos($storedHash, '$argon2id$') === 0 && function_exists('sodium_crypto_pwhash_str_verify')) {
            try {
                if (sodium_crypto_pwhash_str_verify($storedHash, $plainPassword)) {
                    return true;
                }
            } catch (Throwable $exception) {
                return false;
            }
        }

        $sha256 = hash('sha256', $plainPassword);
        if (hash_equals($storedHash, $sha256)) {
            return true;
        }

        $md5 = md5($plainPassword);
        if (hash_equals($storedHash, $md5)) {
            return true;
        }

        return hash_equals($storedHash, $plainPassword);
    }

    private function shouldRehashToBcrypt(string $hash): bool
    {
        if ($hash === '' || strpos($hash, '$argon2id$') === 0) {
            return false;
        }

        $info = password_get_info($hash);
        if (!isset($info['algoName']) || !is_string($info['algoName'])) {
            return false;
        }

        return strtolower($info['algoName']) === 'bcrypt' && password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
    }
}
