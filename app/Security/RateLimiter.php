<?php
namespace App\Security;
class RateLimiter {
    private static function getKey(string $acao, string $id): string {
        return "rate_limit_{$acao}_{$id}";
    }
    public static function check(string $acao, string $id, int $maxTent = 5, int $janela = 900): bool {
        $k = self::getKey($acao, $id);
        if (!isset($_SESSION[$k])) {
            $_SESSION[$k] = ['count' => 0, 'first_attempt' => time()];
        }
        $d = $_SESSION[$k];
        if (time() - $d['first_attempt'] > $janela) {
            $_SESSION[$k] = ['count' => 1, 'first_attempt' => time()];
            return true;
        }
        if ($d['count'] >= $maxTent) return false;
        $_SESSION[$k]['count']++;
        return true;
    }
    public static function reset(string $acao, string $id): void {
        unset($_SESSION[self::getKey($acao, $id)]);
    }
    public static function getRemainingTime(string $acao, string $id, int $janela = 900): int {
        $k = self::getKey($acao, $id);
        if (!isset($_SESSION[$k])) return 0;
        return max(0, $janela - (time() - $_SESSION[$k]['first_attempt']));
    }
}
