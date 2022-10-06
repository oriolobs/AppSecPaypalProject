<?php
namespace Pw\SlimApp\Controller;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

final class RequestController
{
    private ContainerInterface $container;
    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }

    public function showRequestForm(Request $request, Response $response){

        if(!isset($_SESSION['logged'])){
            return $response->withHeader('Location', '/')->withStatus(301);
        }
        return $this->container->get('view')->render(
            $response,
            'requests.twig'
        );
    }

    public function postRequest(Request $request, Response $response){
        $data = $request->getParsedBody();
        $user_exists = $this->container->get('user_repository')->userExists($data['email']);
        if($data['amount'] <= 0.0){
            return $this->container->get('view')->render(
                $response,
                'requests.twig',
                [
                    'error_message' => 'Please introduce a valid amount'
                ]
            );
        }
        if($user_exists == false){
            return $this->container->get('view')->render(
                $response,
                'requests.twig',
                [
                    'error_message' => 'The user you have introduced does not exist'
                ]
            );
        }else{
            $this->container->get('request')->postRequest($data);
            return $this->container->get('view')->render(
                $response,
                'requests.twig',
                [
                    'succesful_message' => 'The request has been succesfully submited'
                ]
            );
        }
    }

    public function showPendingRequests(Request $request, Response $response){
        if(!isset($_SESSION['logged'])){
            return $response->withHeader('Location', '/')->withStatus(301);
        }
        $this->container->get('request')->getUnpaidRequests($_SESSION['email']);
        $html = '';
        foreach ($GLOBALS['requests'] as $unpaid_request){
            $html .= '<tr>';
            $html .= '<th>' .$unpaid_request['id'].'</th>';
            $html .= '<th>'.$unpaid_request['requester'].'</th>';
            $html .= '<th>'.$unpaid_request['amount'].'</th>';
            $url = 'pw.test:8030/account/money/requests/'.$unpaid_request['id'].'/accept';
            $url = '/account/money/requests/'.$unpaid_request['id'].'/accept';
            $html .= '<th><form action ="'.$url.'"><input type="submit" value="Accept"></form></th>';
            $html .= '</tr>';
        }
        $error_message = $_SESSION['error_requests'] ?? '';
        $succesful_message = $_SESSION['succesful_request'] ?? '';
        unset($_SESSION['error_requests']);
        unset($_SESSION['succesful_request']);
        return $this->container->get('view')->render(
            $response,
            'pending_requests.twig',
            [
                'unpaid_requests' => $html,
                'error_message' => $error_message,
                'succesful_message' => $succesful_message
            ]
        );
    }

    public function acceptRequest(Request $request, Response $response, $args){
        if(!isset($_SESSION['logged'])){
            return $response->withHeader('Location', '/')->withStatus(301);
        }
        if($this->container->get('request')->checkRequestReceiver($_SESSION['email'], $args['id'])){
            if($this->container->get('request')->checkEnoughMoney($_SESSION['email'], $args['id'])){
                $this->container->get('request')->acceptRequest($_SESSION['email'], $args['id']);
                $_SESSION['succesful_request']='The request has been succesfully accepted!';
                return $response->withHeader('Location', '/account/money/requests/pending')->withStatus(301);
            }else{
                //notEnoughMoney
                $_SESSION['error_requests'] = "You don't have enough money to accept the request";
                return $response->withHeader('Location', '/account/money/requests/pending')->withStatus(301);
            }
        }else{
            //wrongReceiver
            $_SESSION['error_requests'] = "There was a mistake, you were not the receiver of that request";
            return $response->withHeader('Location', '/account/money/requests/pending')->withStatus(301);
        }
    }
}
