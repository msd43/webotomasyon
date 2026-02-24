<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Middleware;
use App\Core\Session;

final class DashboardController extends Controller
{
    /** @var Middleware */
    private $middleware;

    public function __construct(Session $session)
    {
        parent::__construct(dirname(__DIR__, 3) . '/resources/views');

        $session->start();
        $this->middleware = new Middleware($session);
        $this->middleware->setSecurityHeaders();
        $this->middleware->requireRole('admin');
    }

    public function index(): void
    {
        $this->view('admin.dashboard', ['title' => 'Dashboard']);
    }
}
