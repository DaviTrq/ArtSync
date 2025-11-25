<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/env.php';

use App\Controllers\AuthController;
use App\Security\SecurityHeaders;
use App\Controllers\DashboardController;
use App\Controllers\ScheduleController;
use App\Controllers\PortfolioController;
use App\Controllers\AiController;
use App\Controllers\AdminController;
use App\Controllers\LandingController;
use App\Controllers\ProfileController;
use App\Controllers\PremiumController;
use App\Controllers\SettingsController;
use App\Controllers\ForumController;
use App\Controllers\NetworkController;
use App\Controllers\SearchController;

SecurityHeaders::set();

ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.cookie_lifetime', env('SESSION_LIFETIME', 3600));
if (isset($_SERVER['HTTPS'])) {
    ini_set('session.cookie_secure', 1);
}

session_start();

$request_uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$route = strtok($request_uri, '?');

switch ($route) {
    case '/':
    case '':
        (new LandingController())->index();
        break;

    case '/login':
        $c = new AuthController();
        if ($method === 'GET') $c->showLogin();
        elseif ($method === 'POST') $c->handleLogin();
        break;

    case '/register':
        $c = new AuthController();
        if ($method === 'GET') $c->showRegister();
        elseif ($method === 'POST') $c->handleRegister();
        break;

    case '/logout':
        (new AuthController())->logout();
        break;

    case '/dashboard':
        (new DashboardController())->index();
        break;

    case '/schedule':
        (new ScheduleController())->index();
        break;

    case '/schedule/create':
        if ($method === 'POST') {
            (new ScheduleController())->create();
        } else {
            header('Location: /schedule'); exit;
        }
        break;

    case '/schedule/update':
        if ($method === 'POST') {
            (new ScheduleController())->update();
        } else {
            header('Location: /schedule'); exit;
        }
        break;

    case '/schedule/delete':
        (new ScheduleController())->delete();
        break;

    case '/portfolio':
        (new PortfolioController())->index();
        break;

    case '/portfolio/create':
        if ($method === 'POST') {
            (new PortfolioController())->create();
        } else {
            header('Location: /portfolio'); exit;
        }
        break;

    case '/portfolio/delete':
        (new PortfolioController())->delete();
        break;

    case '/portfolio/view':
        (new PortfolioController())->viewPublic();
        break;

    case '/ai':
        (new AiController())->index();
        break;

    case '/ai/ask':
        if ($method === 'POST') {
            (new AiController())->ask();
        } else {
            header('Location: /ai'); exit;
        }
        break;

    case '/admin':
        (new AdminController())->index();
        break;

    case '/admin/delete':
        (new AdminController())->deleteUser();
        break;

    case '/profile/view':
        (new ProfileController())->viewProfile();
        break;

    case '/profile/edit':
        (new ProfileController())->edit();
        break;

    case '/profile/update':
        if ($method === 'POST') {
            (new ProfileController())->update();
        } else {
            header('Location: /profile/edit'); exit;
        }
        break;

    case '/premium':
        (new PremiumController())->index();
        break;

    case '/settings':
        (new SettingsController())->index();
        break;

    case '/settings/update-profile':
        if ($method === 'POST') {
            (new SettingsController())->updateProfile();
        } else {
            header('Location: /settings'); exit;
        }
        break;

    case '/settings/delete-account':
        if ($method === 'POST') {
            (new SettingsController())->deleteAccount();
        } else {
            header('Location: /settings'); exit;
        }
        break;

    case '/settings/change-language':
        if ($method === 'POST') {
            (new SettingsController())->changeLanguage();
        } else {
            header('Location: /settings'); exit;
        }
        break;

    case '/settings/remove-photo':
        if ($method === 'POST') {
            (new SettingsController())->removePhoto();
        } else {
            header('Location: /settings'); exit;
        }
        break;

    case '/forum':
        (new ForumController())->index();
        break;

    case '/forum/view':
        (new ForumController())->view();
        break;

    case '/forum/create':
        if ($method === 'POST') {
            (new ForumController())->create();
        } else {
            header('Location: /forum'); exit;
        }
        break;

    case '/forum/comment':
        if ($method === 'POST') {
            (new ForumController())->comment();
        } else {
            header('Location: /forum'); exit;
        }
        break;

    case '/forum/approve':
        (new ForumController())->approve();
        break;

    case '/network':
        (new NetworkController())->index();
        break;

    case '/network/connect':
        if ($method === 'POST') {
            (new NetworkController())->connect();
        } else {
            header('Location: /network'); exit;
        }
        break;

    case '/network/accept':
        if ($method === 'POST') {
            (new NetworkController())->accept();
        } else {
            header('Location: /network'); exit;
        }
        break;

    case '/network/reject':
        if ($method === 'POST') {
            (new NetworkController())->reject();
        } else {
            header('Location: /network'); exit;
        }
        break;

    case '/network/stats':
        (new NetworkController())->stats();
        break;

    case '/search':
        (new SearchController())->index();
        break;

    case '/notifications/mark-read':
        if ($method === 'POST' && isset($_SESSION['user_id'])) {
            require_once __DIR__ . '/../app/Services/NotificationService.php';
            $notifService = new \App\Services\NotificationService();
            $id = $_POST['id'] ?? null;
            if ($id) {
                $notifService->markAsRead($id);
            }
            echo json_encode(['success' => true]);
            exit;
        }
        break;

    case '/notifications/mark-all-read':
        if ($method === 'POST' && isset($_SESSION['user_id'])) {
            require_once __DIR__ . '/../app/Services/NotificationService.php';
            $notifService = new \App\Services\NotificationService();
            $notifService->markAllAsRead($_SESSION['user_id']);
            echo json_encode(['success' => true]);
            exit;
        }
        break;

    case '/notifications/mark-admin-read':
        if ($method === 'POST' && isset($_SESSION['user_id']) && !empty($_SESSION['is_admin'])) {
            require_once __DIR__ . '/../app/Services/NotificationService.php';
            $notifService = new \App\Services\NotificationService();
            $id = $_POST['id'] ?? null;
            if ($id) {
                $notifService->markAdminNotificationRead($id);
            }
            echo json_encode(['success' => true]);
            exit;
        }
        break;

    default:
        http_response_code(404);
        echo '<h1>404 - Página Não Encontrada</h1>';
        break;
}
