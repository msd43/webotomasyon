<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Middleware;
use App\Core\Session;
use App\Models\Client;
use Throwable;

final class ClientController extends Controller
{
    private Session $session;

    private Middleware $middleware;

    private Client $clientModel;

    public function __construct(Session $session)
    {
        parent::__construct(dirname(__DIR__, 3) . '/resources/views');

        $this->session = $session;
        $this->session->start();

        $this->middleware = new Middleware($this->session);
        $this->middleware->setSecurityHeaders();
        $this->middleware->requireRole('admin');

        $this->clientModel = new Client();
    }

    public function index(): void
    {
        $clients = $this->clientModel->getAll();

        $this->view('admin.clients.index', [
            'title' => 'Müşteriler',
            'clients' => $clients,
            'success' => $this->session->getFlash('success'),
            'error' => $this->session->getFlash('error'),
            'csrfToken' => $this->session->csrfToken(),
        ]);
    }

    public function create(): void
    {
        $old = $this->session->getFlash('old');

        $this->view('admin.clients.create', [
            'title' => 'Yeni Müşteri Ekle',
            'error' => $this->session->getFlash('error'),
            'old' => is_array($old) ? $old : [],
            'csrfToken' => $this->session->csrfToken(),
        ]);
    }

    public function store(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            throw new \RuntimeException('Method not allowed.');
        }

        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!$this->session->validateCsrfToken($csrfToken)) {
            http_response_code(403);
            $this->session->setFlash('error', 'Geçersiz CSRF token.');
            $this->redirect('/admin/clients/create');
        }

        $data = $this->collectAndValidate();
        if ($data === null) {
            $this->session->setFlash('old', $_POST);
            $this->redirect('/admin/clients/create');
        }

        try {
            $this->clientModel->create($data);
            $this->session->setFlash('success', 'Müşteri başarıyla eklendi.');
        } catch (Throwable $exception) {
            $this->session->setFlash('error', 'Müşteri kaydedilirken bir hata oluştu.');
            $this->session->setFlash('old', $_POST);
            $this->redirect('/admin/clients/create');
        }

        $this->redirect('/admin/clients');
    }

    public function edit(int $id): void
    {
        $client = $this->clientModel->getById($id);

        if ($client === null) {
            $this->session->setFlash('error', 'Müşteri bulunamadı.');
            $this->redirect('/admin/clients');
        }

        $this->view('admin.clients.edit', [
            'title' => 'Müşteri Düzenle',
            'client' => $client,
            'error' => $this->session->getFlash('error'),
            'csrfToken' => $this->session->csrfToken(),
        ]);
    }

    public function update(int $id): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            throw new \RuntimeException('Method not allowed.');
        }

        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!$this->session->validateCsrfToken($csrfToken)) {
            http_response_code(403);
            $this->session->setFlash('error', 'Geçersiz CSRF token.');
            $this->redirect('/admin/clients/edit?id=' . $id);
        }

        $data = $this->collectAndValidate();
        if ($data === null) {
            $this->redirect('/admin/clients/edit?id=' . $id);
        }

        try {
            $this->clientModel->update($id, $data);
            $this->session->setFlash('success', 'Müşteri başarıyla güncellendi.');
        } catch (Throwable $exception) {
            $this->session->setFlash('error', 'Müşteri güncellenirken bir hata oluştu.');
            $this->redirect('/admin/clients/edit?id=' . $id);
        }

        $this->redirect('/admin/clients');
    }

    public function delete(int $id): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            throw new \RuntimeException('Method not allowed.');
        }

        $csrfToken = (string) ($_POST['csrf_token'] ?? '');
        if (!$this->session->validateCsrfToken($csrfToken)) {
            http_response_code(403);
            $this->session->setFlash('error', 'Geçersiz CSRF token.');
            $this->redirect('/admin/clients');
        }

        try {
            $this->clientModel->delete($id);
            $this->session->setFlash('success', 'Müşteri başarıyla silindi.');
        } catch (Throwable $exception) {
            $this->session->setFlash('error', 'Müşteri silinirken bir hata oluştu.');
        }

        $this->redirect('/admin/clients');
    }

    /**
     * @return array<string, mixed>|null
     */
    private function collectAndValidate(): ?array
    {
        $firstName = trim((string) ($_POST['first_name'] ?? ''));
        $lastName = trim((string) ($_POST['last_name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));

        if ($firstName === '' || $lastName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->session->setFlash('error', 'Lütfen zorunlu alanları doğru şekilde doldurun.');
            return null;
        }

        return [
            'user_id' => null,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => trim((string) ($_POST['phone'] ?? '')) ?: null,
            'company' => trim((string) ($_POST['company'] ?? '')) ?: null,
            'address' => trim((string) ($_POST['address'] ?? '')) ?: null,
            'city' => trim((string) ($_POST['city'] ?? '')) ?: null,
            'country' => trim((string) ($_POST['country'] ?? '')) ?: null,
            'status' => isset($_POST['status']) && (string) $_POST['status'] === '1' ? 1 : 0,
        ];
    }
}
