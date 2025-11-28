<?php
namespace App\Controllers;
class SpotifyController extends AuthController
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri = 'http://artsync.local/spotify_callback';
    public function __construct()
    {
        parent::__construct();
        $this->checkAuth();
        $this->clientId     = $_ENV['SPOTIFY_CLIENT_ID'];
        $this->clientSecret = $_ENV['SPOTIFY_CLIENT_SECRET'];
    }
    /**
     * STEP 1: Redireciona o usuário para login do Spotify
     */
    public function connect(): void
    {
        $scopes = [
            'user-read-email',
            'user-top-read',
            'playlist-read-private'
        ];
        $authUrl = "https://accounts.spotify.com/authorize?" . http_build_query([
            'response_type' => 'code',
            'client_id'     => $this->clientId,
            'scope'         => implode(' ', $scopes),
            'redirect_uri'  => $this->redirectUri,
        ]);
        header("Location: {$authUrl}");
        exit;
    }
    /**
     * STEP 2: Callback: recebe o "code" do Spotify e troca por Access Token + Refresh Token
     */
    public function callback(): void
    {
        if (!isset($_GET['code'])) {
            $this->fail("Nenhum código recebido do Spotify.");
        }
        $code = $_GET['code'];
        $response = $this->requestToken([
            'grant_type'   => 'authorization_code',
            'code'         => $code,
            'redirect_uri' => $this->redirectUri
        ]);
        if (!isset($response['access_token'])) {
            $this->fail("Erro ao obter token do Spotify.");
        }
        $_SESSION['spotify_access_token']  = $response['access_token'];
        $_SESSION['spotify_refresh_token'] = $response['refresh_token'] ?? null;
        $_SESSION['spotify_expires_at']    = time() + $response['expires_in'];
        $_SESSION['feedback'] = ['type' => 'success', 'message' => 'Spotify conectado com sucesso!'];
        header('Location: /dashboard');
        exit;
    }
    /**
     * Obtém novo token usando refresh token caso esteja expirado
     */
    public function getAccessToken(): ?string
    {
        if (!isset($_SESSION['spotify_access_token'])) return null;
        if (time() >= ($_SESSION['spotify_expires_at'] ?? 0)) {
            if (!isset($_SESSION['spotify_refresh_token'])) return null;
            $response = $this->requestToken([
                'grant_type'    => 'refresh_token',
                'refresh_token' => $_SESSION['spotify_refresh_token']
            ]);
            $_SESSION['spotify_access_token'] = $response['access_token'];
            $_SESSION['spotify_expires_at']   = time() + $response['expires_in'];
        }
        return $_SESSION['spotify_access_token'];
    }
    /**
     * Função privada que envia requisição para /api/token
     */
    private function requestToken(array $params): array
    {
        $ch = curl_init('https://accounts.spotify.com/api/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($error || $status !== 200) {
            return [];
        }
        return json_decode($response, true);
    }
    private function fail(string $msg): void
    {
        $_SESSION['feedback'] = ['type' => 'error', 'message' => $msg];
        header('Location: /dashboard');
        exit;
    }
}
