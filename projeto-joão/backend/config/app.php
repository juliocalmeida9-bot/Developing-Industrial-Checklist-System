<?php
// ====================================================================
// CheckInd — Configurações da Aplicação
// ====================================================================

// Caminho base na URL (sem barra final)
// BASE_PATH e BASE_URL são detectados automaticamente para funcionar em Laragon, XAMPP ou site customizado.
$scriptPath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$basePath = rtrim($scriptPath, '/');

define('BASE_PATH', $basePath === '/' ? '' : $basePath);

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? '') === '443'
    ? 'https'
    : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseUrl = $scheme . '://' . $host . (BASE_PATH !== '' ? BASE_PATH : '');

define('BASE_URL', rtrim($baseUrl, '/'));

// Fuso horário
date_default_timezone_set('America/Sao_Paulo');

// Exibição de erros (desative em produção)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
