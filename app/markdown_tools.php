<?php
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Go, le tout en gestion des exceptions
 */
try {
    /**
     * Injecteur de dépendances...
     */
    $DI = \MdTools\DI::getDI();
    /**
     * Récupération de la configuration
     */
    $cfgFile = __DIR__ . '/../config/config.php';
    if (!is_file($cfgFile)) {
        echo 'Config file missing !' . PHP_EOL;
        exit(2);
    }
    $config = @include($cfgFile);
    $DI->setShared('config', $config);
    /**
     * Ensuite la requête...
     */
    $request = new \MdTools\Console\Request();
    $DI->setShared('request', $request);
    /**
     * instance de router pour gérer tout ça...
     */
    $router = \MdTools\Console\Router\Router::getInstance($config);
    $router->setRequest($request);
    $DI->setShared('router', $router);
    /**
     * On a tout pour continuer, c'est parti...
     */
    $route = $router->matchCurrentRequest();
    if ($route instanceof \MdTools\Console\Router\Route) {
        $route->dispatch($request);
    } else {
        echo 'Command not found !' . PHP_EOL;
        exit(3);
    }
} catch (\Exception $ex) {
    echo $ex->getMessage() . PHP_EOL;
    exit(1);
}
