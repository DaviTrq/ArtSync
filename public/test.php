<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Security;
use Config\Database;

echo "Testando carregamento...<br>";

try {
    echo "Autoload: OK<br>";
    echo "Security: OK<br>";
    echo "Database class: OK<br>";
    
    $db = Database::getInstance();
    echo "Database connection: OK<br>";
    
    echo "<br>Tudo funcionando!";
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "<br>";
    echo "Arquivo: " . $e->getFile() . "<br>";
    echo "Linha: " . $e->getLine() . "<br>";
}
