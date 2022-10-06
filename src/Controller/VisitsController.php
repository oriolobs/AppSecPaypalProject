<?php

namespace Pw\SlimApp\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class VisitsController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function showVisits(Request $request, Response $response): Response
    {
        if (empty($_SESSION['counter'])) {
            $_SESSION['counter'] = 1;
        } else {
            $_SESSION['counter']++;
        }

        return $this->container->get('view')->render(
            $response,
            'visits.twig',
            [
                'visits' => $_SESSION['counter'],
            ]
        );
    }
}