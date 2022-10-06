<?php

namespace Pw\SlimApp\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class AccountSummaryController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function showAccountSummaryPage(Request $request, Response $response): Response
    {

        return $this->container->get('view')->render(
            $response,
            'account_summary.twig',
        );
    }

}
