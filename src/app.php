<?php

require_once ROOT_PATH .'/vendor/autoload.php';

$config = include ROOT_PATH .'/config/config.php';
$debug = isset($config['application_environment'])
    && $config['application_environment'] != 'production';

$app = new HackDayProject\Application([
    'config' => $config,
    'debug' => $debug,
]);

\Symfony\Component\Debug\ErrorHandler::register();
\Symfony\Component\Debug\ExceptionHandler::register();

return $app;
