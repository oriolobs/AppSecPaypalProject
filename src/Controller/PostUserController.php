<?php

declare(strict_types=1);

namespace Pw\SlimApp\Controller;

use DateTime;
use Exception;
use Psr\Container\ContainerInterface;
use Pw\SlimApp\Model\User;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

final class PostUserController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function create(Request $request, Response $response): Response
    {
        
        try {
            $data = $request->getParsedBody();

            // TODO - Validate data before instantiating the user
            $user = new User(
                $data['email'] ?? '',
                $data['password'] ?? '',
                new DateTime(),
                new DateTime(),
                new DateTime(),
                $data['phone_number'] ?? '',
                '',
                'not verified'
            );

            $this->container->get('user_repository')->save($user);
        } catch (Exception $exception) {
            $response->getBody()
                ->write('Unexpected error: ' . $exception->getMessage());
            return $response->withStatus(500);
        }

        return $response->withStatus(201);
    }
}
