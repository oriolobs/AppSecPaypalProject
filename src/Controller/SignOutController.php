<?php
declare(strict_types=1);

namespace Pw\SlimApp\Controller;

use Exception;
use Psr\Container\ContainerInterface;
use Pw\SlimApp\Model\SignInForm;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

final class SignOutController
{
    private ContainerInterface $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function logout(Request $request, Response $response):Response
    {

        if( isset( $_SESSION['logged'])){
            session_unset();
            session_destroy();
        }
        return $response->withHeader('Location', '/')->withStatus(301);
    }

}