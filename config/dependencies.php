<?php

use DI\Container;
use Pw\SlimApp\Repository\MySQLBankAccountRepository;
use Pw\SlimApp\Repository\MySQLRequestRepository;
use Pw\SlimApp\Repository\MySQLSignInRepository;
use Pw\SlimApp\Repository\MySQLTransactionRepository;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Pw\SlimApp\Repository\MySQLUserRepository;
use Pw\SlimApp\Repository\PDOSingleton;
use Psr\Container\ContainerInterface;

$container = new Container();

$container->set(
    'view',
    function () {
        return Twig::create(__DIR__ . '/../templates', ['cache' => false]);
    }
);

$container->set(
    'flash',
    function () {
        return new Messages();
    }
);

$container->set('db', function () {
    return PDOSingleton::getInstance(
        $_ENV['MYSQL_USER'],
        $_ENV['MYSQL_ROOT_PASSWORD'],
        $_ENV['MYSQL_HOST'],
        $_ENV['MYSQL_PORT'],
        $_ENV['MYSQL_DATABASE']
    );
});

$container->set('user_repository', function (ContainerInterface $container) {
    return new MySQLUserRepository($container->get('db'));
});

$container->set('sign_in', function (ContainerInterface $container) {
    return new MySQLSignInRepository($container->get('db'));
});

$container->set('bank_account', function (ContainerInterface $container){
    return new MySQLBankAccountRepository($container->get('db'));
});

$container->set('request', function (ContainerInterface $container){
    return new MySQLRequestRepository($container->get('db'));
});

$container->set('transaction', function (ContainerInterface $container){
    return new MySQLTransactionRepository($container->get('db'));
});