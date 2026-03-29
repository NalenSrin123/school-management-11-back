<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', 'root123');
    $pdo->exec('CREATE DATABASE IF NOT EXISTS etec');
    echo 'Database created successfully!';
} catch (Exception $e) {
    echo $e->getMessage();
}
