<?php
namespace Pw\SlimApp\Controller;

use Exception;
use Iban\Validation\Validator;
use Psr\Container\ContainerInterface;
use Pw\SlimApp\Model\BankAccountForm;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class LoadMoneyController
{
    private ContainerInterface $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function showBankAccount(Request $request, Response $response){
        try{
            if(!isset($_SESSION['logged'])){
                return $response->withHeader('Location', '/')->withStatus(301);
            }
            $email = $_SESSION['email'];
            $exists = $this->container->get('bank_account')->checkBankAccount($email);

            if($exists == true){
                $iban = $_SESSION['iban'];
                return $this->container->get('view')->render(
                    $response,
                    'load_money_2.twig',
                    array(
                        'iban' => $_SESSION['iban'])
                );

            }
            else{
                return $this->container->get('view')->render(
                    $response,
                    'load_money_1.twig'
                );
            }


        }catch (Exception $exception){
            $response->getBody()
                ->write('Unexpected error: ' . $exception->getMessage());
            return $response->withStatus(500);
        }
    }

    public function postBankAccount(Request $request, Response $response){
        $data = $request->getParsedBody();
        $iban = $data['iban'];
        $validator = new Validator();
        if(!$validator->validate($iban)){
            return $this->container->get('view')->render(
                $response,
                'load_money_1.twig',
                [
                    'error_message' => 'Please, introduce a valid IBAN'
                ]
            );
        }
        else{
            $bankAccountForm = new BankAccountForm(
                $data['owner'],
                $data['iban']
            );
            $this->container->get('bank_account')->postBankAccount($bankAccountForm);
            $_SESSION['iban'] = $iban;
            return $this->container->get('view')->render(
                $response,
                'load_money_2.twig'
            );
        }
    }

    public function loadMoney(Request $request, Response $response){
        $data = $request->getParsedBody();
        $money = $data['money'];
        if($money <= 0.0){
            return $this->container->get('view')->render(
                $response,
                'load_money_2.twig',
                [
                    'error_message'=> 'Please introduce a valid amount'
                ]

            );
        }else{
            $this->container->get('bank_account')->loadMoney($money);
            return $this->container->get('view')->render(
                $response,
                'load_money_2.twig',
                [
                    'succesful_message' => 'Money added to your wallet!'
                ]
            );
        }

    }
}
