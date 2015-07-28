<?php
defined('ROOT_PATH') || define('ROOT_PATH', realpath(__DIR__ . '/..'));

$app = require ROOT_PATH . '/src/app.php';
$app->run();
