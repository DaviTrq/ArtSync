<?php
namespace App\Controllers;
class PremiumController extends AuthController {
    public function __construct() {
        parent::__construct();
        $this->checkAuth();
    }
    public function index(): void {
        $this->view('premium/index', ['pageTitle' => 'Assinatura', 'currentPage' => 'premium']);
    }
}
