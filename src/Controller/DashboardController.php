<?php

namespace Pw\SlimApp\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class DashboardController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function showDashboardPage(Request $request, Response $response): Response
    {
        if(!isset($_SESSION['logged'])){
            return $response->withHeader('Location', '/')->withStatus(301);
        }
        $user = $this->container->get('user_repository')->getUserInfo($_SESSION['email']);
        $this->container->get('transaction')->getLast5Transactions($_SESSION['email']);
        return $this->container->get('view')->render(
            $response,
            'dashboard.twig',
            [
                'money' => $user->wallet(),
                'transaction_1' => $GLOBALS['transactions']['1'],
                'transaction_2' => $GLOBALS['transactions']['2'],
                'transaction_3' => $GLOBALS['transactions']['3'],
                'transaction_4' => $GLOBALS['transactions']['4'],
                'transaction_5' => $GLOBALS['transactions']['5'],
            ]
        );
    }



}
