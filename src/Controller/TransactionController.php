<?php
namespace Pw\SlimApp\Controller;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

final class TransactionController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function showAllTransactions(ServerRequestInterface $request, ResponseInterface $response):Response
    {
        $this->container->get('transaction')->getAllTransactions($_SESSION['email']);
        $html = '';
        foreach ($GLOBALS['transactions'] as $transaction){
            $html .= '<tr>';
            $html .= '<th>' .$transaction['id'].'</th>';
            $html .= '<th>'.$transaction['sender'].'</th>';
            $html .= '<th>'.$transaction['receiver'].'</th>';
            $html .= '<th>'.$transaction['amount'].'</th>';
            $html .= '</tr>';
        }
        return $this->container->get('view')->render(
            $response,
            'transactions.twig',
            [
                'transactions' => $html
            ]
        );
    }
}