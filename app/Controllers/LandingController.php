<?php
namespace App\Controllers;
class LandingController extends AuthController {
    public function index(): void {
        isset($_SESSION['user_id']) && header('Location: /dashboard') && exit;
        $this->view('landing/index', ['pageTitle' => 'Art Sync - Sua Carreira na Era Digital', 'currentPage' => 'landing']);
    }
}
