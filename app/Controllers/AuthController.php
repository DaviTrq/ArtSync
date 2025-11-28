<?php
namespace App\Controllers;
use App\Models\User;
use App\Repositories\PDO\PdoUserRepository;
use App\Security\CSRF;
use App\Security\RateLimiter;
class AuthController {
    protected PdoUserRepository $repo;
    public function __construct() {
        session_status() == PHP_SESSION_NONE && session_start();
        $this->repo = new PdoUserRepository();
    }
    protected function checkAuth(): void {
        !isset($_SESSION['user_id']) && header('Location: /login') && exit;
    }
    protected function view(string $caminho, array $dados = []): void {
        extract($dados);
        require __DIR__ . "/../../views/{$caminho}.php";
    }
    public function showLogin(): void {
        $this->view('auth/login');
    }
    public function showRegister(): void {
        $this->view('auth/register');
    }
    public function handleLogin(): void {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $this->view('auth/login', ['error' => 'Token inválido']);
            return;
        }
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $senha = $_POST['password'] ?? '';
        $ip = $_SERVER['REMOTE_ADDR'];
        $maxTent = env('MAX_LOGIN_ATTEMPTS', 5);
        $timeout = env('LOGIN_TIMEOUT', 900);
        if (!RateLimiter::check('login', $ip, $maxTent, $timeout)) {
            $tempo = ceil(RateLimiter::getRemainingTime('login', $ip, $timeout) / 60);
            $this->view('auth/login', ['error' => "Aguarde {$tempo} min"]);
            return;
        }
        !filter_var($email, FILTER_VALIDATE_EMAIL) && $this->view('auth/login', ['error' => 'Email inválido']) && exit;
        $usr = $this->repo->findByEmail($email);
        if ($usr && password_verify($senha, $usr->password)) {
            RateLimiter::reset('login', $ip);
            session_regenerate_id(true);
            $_SESSION['user_id'] = $usr->id;
            $_SESSION['artist_name'] = $usr->artistName;
            $_SESSION['email'] = $usr->email;
            $_SESSION['is_admin'] = $usr->isAdmin;
            header("Location: /dashboard");
            exit;
        }
        $this->view('auth/login', ['error' => 'Credenciais inválidas']);
    }
    public function handleRegister(): void {
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $this->view('auth/register', ['error' => 'Token inválido']);
            return;
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        if (!RateLimiter::check('register', $ip, 3, 3600)) {
            $tempo = ceil(RateLimiter::getRemainingTime('register', $ip, 3600) / 60);
            $this->view('auth/register', ['error' => "Aguarde {$tempo} min"]);
            return;
        }
        $nome = htmlspecialchars(trim($_POST['artist_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $senha = $_POST['password'] ?? '';
        $conf = $_POST['confirm_password'] ?? '';
        if (empty($nome) || empty($email) || empty($senha)) {
            $this->view('auth/register', ['error' => 'Preencha todos os campos']);
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view('auth/register', ['error' => 'Email inválido']);
            return;
        }
        if ($senha !== $conf) {
            $this->view('auth/register', ['error' => 'Senhas diferentes']);
            return;
        }
        if (strlen($senha) < 6) {
            $this->view('auth/register', ['error' => 'Senha muito curta']);
            return;
        }
        if ($this->repo->findByEmail($email)) {
            $this->view('auth/register', ['error' => 'Email já existe']);
            return;
        }
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $usr = new User(null, $nome, $email, $hash, false);
        if (!$this->repo->save($usr)) {
            $this->view('auth/register', ['error' => 'Erro ao criar conta']);
            return;
        }
        header("Location: /login");
        exit;
    }
    public function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        header("Location: /login");
        exit;
    }
}
