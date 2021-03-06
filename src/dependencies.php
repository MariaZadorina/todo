<?php

// DIC configuration
$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// db
$container['db'] = function ($c){
    $settings = $c->get('settings')['db'];
    $dbHost = $settings['host'];
    $dbUser = $settings['user'];
    $dbPass = $settings['password'];
    $dbName = $settings['database'];
    $db = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=UTF8", $dbUser, $dbPass);
    return $db;
};