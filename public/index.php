<?php

use App\Kernel;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__).'/config/bootstrap.php';

// Custom IP access control
if ($_SERVER['APP_ENV'] !== 'dev') {
    if (isset($_SERVER['HTTP_CLIENT_IP'])
        || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
        || !(in_array(@$_SERVER['REMOTE_ADDR'], [$_SERVER['AUTH_IP']]) || php_sapi_name() === 'cli-server')
    ) {
        header('HTTP/1.0 403 Forbidden');
        exit('You are not allowed to access this file.');
    }
}
// End custom IP access control

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
