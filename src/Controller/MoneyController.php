<?php

namespace Pw\SlimApp\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class MoneyController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function showSendMoney(Request $request, Response $response): Response
    {
        if(!isset($_SESSION['logged'])){
            return $response->withHeader('Location', '/')->withStatus(301);
        }
        return $this->container->get('view')->render(
            $response,
            'send_money.twig'
        );
    }

    public function postSendMoney(Request $request, Response $response){
      try {

        $data = $request->getParsedBody();
        $send_money_to = $data['send_money_to'];
        $amount = $data['amount'];

        if(strcmp($data['send_money_to'], $_SESSION['email']) == 0){
          $_SESSION['sendMoney'] = "CAN'T SEND TO MYSELF";
        } //if
        else if(!$this->container->get('user_repository')->userExists($data['send_money_to'])){
          $_SESSION['sendMoney'] = "USER NO EXISTS";
        } //else-if
        else{
            if($this->container->get('user_repository')->checkValidOperation($_SESSION['email'], $amount) && $amount > 0){
              $this->container->get('user_repository')->sendMoney($_SESSION['email'], $send_money_to, $amount);
              $this->container->get('user_repository')->addTransaction($_SESSION['email'], $send_money_to, $amount);
              $_SESSION['sendMoney'] = "MONEY SENDED";
            } //if
            else{
              $_SESSION['sendMoney'] = "MONEY CAN'T BE SEND";
            } //else
        } //else

      } catch (Exception $exception) {
          $response->getBody()
              ->write('Unexpected error: ' . $exception->getMessage());
          return $response->withStatus(500);
      }

      return $this->container->get('view')->render(
          $response,
          'send_money.twig',
          [
            'action' => $_SESSION['sendMoney'],
          ]
      );
    }
}
