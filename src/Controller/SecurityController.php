<?php
declare(strict_types=1);

namespace Pw\SlimApp\Controller;

use Exception;
use Psr\Container\ContainerInterface;
use Pw\SlimApp\Model\Security;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

final class SecurityController
{
    private ContainerInterface $container;
    private bool $firstTime = TRUE;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function showSecurityPage(Request $request, Response $response): Response
    {
        if(!isset($_SESSION['logged'])){
            return $response->withHeader('Location', '/')->withStatus(301);
        }
        return $this->container->get('view')->render(
            $response,
            'security.twig',
            [
              'action' => $_SESSION['success_passwordChange'],
            ]
        );
    }

    public function postChangePassword(Request $request, Response $response): Response
    {
      try {
          $data = $request->getParsedBody();
          $security = new Security(
              $data['old_password'],
              $data['new_password'],
              $data['confirmed_password']
          );

          $database_password = $this->container->get('user_repository')->getPassword($_SESSION['email']);

          if($security->resetPassword($database_password)){

            $this->container->get('user_repository')->changePassword($_SESSION['email'], $security->new_password());
            $_SESSION['success_passwordChange'] = "USER PASSWORD CHANGED";
          } //if
          else{
            $_SESSION['success_passwordChange'] = "USER PASSWORD HASN'T CHANGED";
          } //else
      } catch (Exception $exception) {
          $response->getBody()
              ->write('Unexpected error: ' . $exception->getMessage());
              echo $_SESSION['email'] . " " . $security->new_password();
          return $response->withStatus(500);
      }

      return $this->container->get('view')->render(
          $response,
          'security.twig',
          [
            'action' => $_SESSION['success_passwordChange'],
          ]
      );
    }
}
