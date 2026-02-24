<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Middleware;
use App\Core\Session;
use App\Models\Product;
use App\Models\ProductGroup;
use Throwable;

final class ProductController extends Controller
{
    /** @var Session */
    private $session;
    /** @var Product */
    private $productModel;
    /** @var ProductGroup */
    private $groupModel;

    public function __construct(Session $session)
    {
        parent::__construct(dirname(__DIR__, 3) . '/resources/views');

        $this->session = $session;
        $this->session->start();

        $middleware = new Middleware($this->session);
        $middleware->setSecurityHeaders();
        $middleware->requireRole('admin');

        $this->productModel = new Product();
        $this->groupModel = new ProductGroup();
    }

    public function index(): void
    {
        $this->view('admin.products.index', [
            'title' => 'Ürünler',
            'products' => $this->productModel->getAll(),
            'success' => $this->session->getFlash('success'),
            'error' => $this->session->getFlash('error'),
            'csrfToken' => $this->session->csrfToken(),
        ]);
    }

    public function create(): void
    {
        $this->view('admin.products.create', [
            'title' => 'Ürün Ekle',
            'groups' => $this->groupModel->getActive(),
            'error' => $this->session->getFlash('error'),
            'old' => $this->session->getFlash('old') ?? [],
            'csrfToken' => $this->session->csrfToken(),
        ]);
    }

    public function store(): void
    {
        $this->validateCsrfOrRedirect('/admin/products/create');
        $data = $this->validatePayload();
        if ($data === null) {
            $this->session->setFlash('old', $_POST);
            $this->redirect('/admin/products/create');
        }

        try {
            $this->productModel->create($data);
            $this->session->setFlash('success', 'Ürün başarıyla eklendi.');
            $this->redirect('/admin/products');
        } catch (Throwable $e) {
            $this->session->setFlash('error', 'Ürün eklenirken hata oluştu.');
            $this->session->setFlash('old', $_POST);
            $this->redirect('/admin/products/create');
        }
    }

    public function edit(int $id): void
    {
        $product = $this->productModel->getById($id);
        if ($product === null) {
            $this->session->setFlash('error', 'Ürün bulunamadı.');
            $this->redirect('/admin/products');
        }

        $this->view('admin.products.edit', [
            'title' => 'Ürün Düzenle',
            'product' => $product,
            'groups' => $this->groupModel->getActive(),
            'error' => $this->session->getFlash('error'),
            'csrfToken' => $this->session->csrfToken(),
        ]);
    }

    public function update(int $id): void
    {
        $this->validateCsrfOrRedirect('/admin/products/edit?id=' . $id);
        $data = $this->validatePayload();
        if ($data === null) {
            $this->redirect('/admin/products/edit?id=' . $id);
        }

        try {
            $this->productModel->update($id, $data);
            $this->session->setFlash('success', 'Ürün güncellendi.');
        } catch (Throwable $e) {
            $this->session->setFlash('error', 'Ürün güncellenemedi.');
            $this->redirect('/admin/products/edit?id=' . $id);
        }

        $this->redirect('/admin/products');
    }

    public function delete(int $id): void
    {
        $this->validateCsrfOrRedirect('/admin/products');

        try {
            $this->productModel->delete($id);
            $this->session->setFlash('success', 'Ürün silindi.');
        } catch (Throwable $e) {
            $this->session->setFlash('error', 'Ürün silinemedi.');
        }

        $this->redirect('/admin/products');
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
        $groupId = (int) ($_POST['group_id'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $type = (string) ($_POST['type'] ?? 'general');
        $billingCycle = (string) ($_POST['billing_cycle'] ?? 'monthly');

        $validTypes = ['hosting', 'server', 'license', 'general'];
        $validCycles = ['monthly', 'annually', 'one-time', 'free'];

        if ($groupId <= 0 || $name === '' || $slug === '' || !in_array($type, $validTypes, true) || !in_array($billingCycle, $validCycles, true)) {
            $this->session->setFlash('error', 'Lütfen zorunlu alanları doğru doldurun.');
            return null;
        }

        return [
            'group_id' => $groupId,
            'name' => $name,
            'slug' => $slug,
            'description' => trim((string) ($_POST['description'] ?? '')) ?: null,
            'type' => $type,
            'module' => trim((string) ($_POST['module'] ?? '')) ?: null,
            'price' => (float) ($_POST['price'] ?? 0),
            'setup_fee' => (float) ($_POST['setup_fee'] ?? 0),
            'billing_cycle' => $billingCycle,
            'status' => (string) ($_POST['status'] ?? '1') === '1' ? 1 : 0,
        ];
    }
}
