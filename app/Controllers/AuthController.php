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
                'SELECT id, role, password_hash, status FROM users WHERE email = :email LIMIT 1'
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

        if ($status !== 1 || $passwordHash === '' || !password_verify($password, $passwordHash)) {
            $this->session->setFlash('error', 'Hatalı e-posta veya şifre');
            $this->redirect('/login');
        }

        session_regenerate_id(true);

        $this->session->set('is_authenticated', true);
        $this->session->set('auth_user_id', (int) $user['id']);
        $this->session->set('auth_user_role', (string) $user['role']);
        $this->session->set('auth_user_email', $email);

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
}
