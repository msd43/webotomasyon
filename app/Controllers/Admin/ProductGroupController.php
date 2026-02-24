<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Middleware;
use App\Core\Session;
use App\Models\ProductGroup;
use Throwable;

final class ProductGroupController extends Controller
{
    private Session $session;
    private ProductGroup $model;

    public function __construct(Session $session)
    {
        parent::__construct(dirname(__DIR__, 3) . '/resources/views');

        $this->session = $session;
        $this->session->start();

        $middleware = new Middleware($this->session);
        $middleware->setSecurityHeaders();
        $middleware->requireRole('admin');

        $this->model = new ProductGroup();
    }

    public function index(): void
    {
        $this->view('admin.product_groups.index', [
            'title' => 'Ürün Grupları',
            'groups' => $this->model->getAll(),
            'success' => $this->session->getFlash('success'),
            'error' => $this->session->getFlash('error'),
            'csrfToken' => $this->session->csrfToken(),
        ]);
    }

    public function create(): void
    {
        $this->view('admin.product_groups.create', [
            'title' => 'Ürün Grubu Ekle',
            'error' => $this->session->getFlash('error'),
            'old' => $this->session->getFlash('old') ?? [],
            'csrfToken' => $this->session->csrfToken(),
        ]);
    }

    public function store(): void
    {
        $this->validateCsrfOrRedirect('/admin/product-groups/create');
        $data = $this->validatePayload();
        if ($data === null) {
            $this->session->setFlash('old', $_POST);
            $this->redirect('/admin/product-groups/create');
        }

        try {
            $this->model->create($data);
            $this->session->setFlash('success', 'Ürün grubu başarıyla eklendi.');
            $this->redirect('/admin/product-groups');
        } catch (Throwable $e) {
            $this->session->setFlash('error', 'Ürün grubu eklenirken hata oluştu.');
            $this->session->setFlash('old', $_POST);
            $this->redirect('/admin/product-groups/create');
        }
    }

    public function edit(int $id): void
    {
        $group = $this->model->getById($id);
        if ($group === null) {
            $this->session->setFlash('error', 'Ürün grubu bulunamadı.');
            $this->redirect('/admin/product-groups');
        }

        $this->view('admin.product_groups.edit', [
            'title' => 'Ürün Grubu Düzenle',
            'group' => $group,
            'error' => $this->session->getFlash('error'),
            'csrfToken' => $this->session->csrfToken(),
        ]);
    }

    public function update(int $id): void
    {
        $this->validateCsrfOrRedirect('/admin/product-groups/edit?id=' . $id);
        $data = $this->validatePayload();
        if ($data === null) {
            $this->redirect('/admin/product-groups/edit?id=' . $id);
        }

        try {
            $this->model->update($id, $data);
            $this->session->setFlash('success', 'Ürün grubu güncellendi.');
        } catch (Throwable $e) {
            $this->session->setFlash('error', 'Ürün grubu güncellenemedi.');
            $this->redirect('/admin/product-groups/edit?id=' . $id);
        }

        $this->redirect('/admin/product-groups');
    }

    public function delete(int $id): void
    {
        $this->validateCsrfOrRedirect('/admin/product-groups');

        try {
            $this->model->delete($id);
            $this->session->setFlash('success', 'Ürün grubu silindi.');
        } catch (Throwable $e) {
            $this->session->setFlash('error', 'Ürün grubu silinemedi.');
        }

        $this->redirect('/admin/product-groups');
    }

    private function validateCsrfOrRedirect(string $redirect): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            throw new \RuntimeException('Method not allowed.');
        }

        $token = (string) ($_POST['csrf_token'] ?? '');
        if (!$this->session->validateCsrfToken($token)) {
            http_response_code(403);
            $this->session->setFlash('error', 'Geçersiz CSRF token.');
            $this->redirect($redirect);
        }
    }

    /** @return array<string,mixed>|null */
    private function validatePayload(): ?array
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $slug = trim((string) ($_POST['slug'] ?? ''));
        if ($name === '' || $slug === '') {
            $this->session->setFlash('error', 'Ad ve slug alanları zorunludur.');
            return null;
        }

        return [
            'name' => $name,
            'slug' => $slug,
            'description' => trim((string) ($_POST['description'] ?? '')) ?: null,
            'status' => (string) ($_POST['status'] ?? '1') === '1' ? 1 : 0,
        ];
    }
}
