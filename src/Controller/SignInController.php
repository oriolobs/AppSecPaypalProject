<?php
declare(strict_types=1);

namespace Pw\SlimApp\Controller;

use Exception;
use Psr\Container\ContainerInterface;
use Pw\SlimApp\Model\SignInForm;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

final class SignInController
{
    private ContainerInterface $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function check(Request $request, Response $response):Response
    {
        try{
            $data = $request->getParsedBody();

            $form = new SignInForm($data['email'] ?? '',
                $data['password'] ?? '');

            $valid = $this->container->get('sign_in')->signIn($form);

            if($valid == true){
                $_SESSION['logged'] = 'Y';
                $_SESSION['email'] = $data['email'];
                return $response->withHeader('Location', '/account')->withStatus(301);
            }else{
                return $this->container->get('view')->render(
                    $response,
                    'sign-in.twig',
                    [
                        'error_message' => 'Check the input data and try again. Check your email if you have created an account but have not verified it',
                        'email' => $data['email']
                    ]
                );


            }
        }catch(Exception $exception){
            $response->getBody()
                ->write('Unexpected error: ' . $exception->getMessage());
            return $response->withStatus(500);

        }
    }

    public function main(Request $request, Response $response){

        if( isset($_SESSION['logged'])){
            return $response->withHeader('Location', '/account')->withStatus(301);
        }
        return $this->container->get('view')->render(
            $response,
            'sign-in.twig',
            [
                'error_message' => '',
                'email' => ''
            ]
        );
    }
}
