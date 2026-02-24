<?php
declare(strict_types=1);

use App\Controllers\Admin\ClientController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ProductController;
use App\Controllers\Admin\ProductGroupController;
use App\Controllers\AuthController;
use App\Core\Session;

$router->get('/', static function (): void {
    header('Location: /login', true, 302);
    exit;
});

$router->get('/login', static function (): void {
    (new AuthController(new Session()))->login();
});

$router->post('/login', static function (): void {
    (new AuthController(new Session()))->authenticate();
});

$router->get('/logout', static function (): void {
    (new AuthController(new Session()))->logout();
});

$router->get('/admin/dashboard', static function (): void {
    (new DashboardController(new Session()))->index();
});

$router->get('/admin/clients', static function (): void {
    (new ClientController(new Session()))->index();
});

$router->get('/admin/clients/create', static function (): void {
    (new ClientController(new Session()))->create();
});

$router->post('/admin/clients/store', static function (): void {
    (new ClientController(new Session()))->store();
});

$router->get('/admin/clients/edit', static function (): void {
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(400);
        throw new RuntimeException('Geçersiz müşteri kimliği.');
    }
    (new ClientController(new Session()))->edit($id);
});

$router->post('/admin/clients/update', static function (): void {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(400);
        throw new RuntimeException('Geçersiz müşteri kimliği.');
    }
    (new ClientController(new Session()))->update($id);
});

$router->post('/admin/clients/delete', static function (): void {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(400);
        throw new RuntimeException('Geçersiz müşteri kimliği.');
    }
    (new ClientController(new Session()))->delete($id);
});

$router->get('/admin/product-groups', static function (): void {
    (new ProductGroupController(new Session()))->index();
});

$router->get('/admin/product-groups/create', static function (): void {
    (new ProductGroupController(new Session()))->create();
});

$router->post('/admin/product-groups/store', static function (): void {
    (new ProductGroupController(new Session()))->store();
});

$router->get('/admin/product-groups/edit', static function (): void {
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(400);
        throw new RuntimeException('Geçersiz ürün grubu kimliği.');
    }
    (new ProductGroupController(new Session()))->edit($id);
});

$router->post('/admin/product-groups/update', static function (): void {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(400);
        throw new RuntimeException('Geçersiz ürün grubu kimliği.');
    }
    (new ProductGroupController(new Session()))->update($id);
});

$router->post('/admin/product-groups/delete', static function (): void {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(400);
        throw new RuntimeException('Geçersiz ürün grubu kimliği.');
    }
    (new ProductGroupController(new Session()))->delete($id);
});

$router->get('/admin/products', static function (): void {
    (new ProductController(new Session()))->index();
});

$router->get('/admin/products/create', static function (): void {
    (new ProductController(new Session()))->create();
});

$router->post('/admin/products/store', static function (): void {
    (new ProductController(new Session()))->store();
});

$router->get('/admin/products/edit', static function (): void {
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(400);
        throw new RuntimeException('Geçersiz ürün kimliği.');
    }
    (new ProductController(new Session()))->edit($id);
});

$router->post('/admin/products/update', static function (): void {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(400);
        throw new RuntimeException('Geçersiz ürün kimliği.');
    }
    (new ProductController(new Session()))->update($id);
});

$router->post('/admin/products/delete', static function (): void {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(400);
        throw new RuntimeException('Geçersiz ürün kimliği.');
    }
    (new ProductController(new Session()))->delete($id);
});
