<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php'; // if using Composer
// or manually include your Router, Database, etc.

$route = $_GET['route'] ?? 'test/index';
// Simulate dispatch – your Router should handle this