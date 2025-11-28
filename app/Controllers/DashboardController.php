<?php
namespace App\Controllers;
class DashboardController extends AuthController {
    public function __construct() {
        parent::__construct();
        $this->checkAuth();
    }
    public function index(): void {
        $this->view('dashboard/index', ['pageTitle' => 'Dashboard', 'currentPage' => 'dashboard']);
    }
}
