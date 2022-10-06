<?php

namespace Pw\SlimApp\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Pw\SlimApp\Model\User;
use Pw\SlimApp\Model\EmailVerification;

use DateTime;

final class SignUpController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function showSignUpPage(Request $request, Response $response): Response
    {
        if( isset($_SESSION['logged'])){
            return $this->container->get('view')->render(
                $response,
                'profile.twig'
            );
        }
        $messages = $this->container->get('flash')->getMessages();

        $notifications = $messages['notifications'] ?? [];

        return $this->container->get('view')->render(
            $response,
            'sign-up.twig',
            [
                'notifications' => $notifications
            ]
        );
    }

    public function getToken(Request $request, Response $response): Response
    {


        if ($this->container->get('user_repository')->verifyAccount($_SESSION['email'])) {
            $status = $this->container->get('user_repository')->getToken($_SESSION['verification_code']);

            if ($status == 'not verified') {
                //echo "NOT VERIFIED";
                $status = $this->container->get('user_repository')->updateAccountToVerified($_SESSION['email']);
                return $this->container->get('view')->render(
                    $response,
                    'activation.twig'
                );
            } else {
                echo "<script type='text/javascript'>alert('Something went wrong');</script>";
                //echo "SOMETHING WENT WRONG";
                return $response->withStatus(500);
            }
        } else {
            echo "<script type='text/javascript'>alert('Account verified');</script>";
            //echo "ACCOUNT VERIFIED";
            return $response->withStatus(500);
        }

    }

    public function postSignUp(Request $request, Response $response): Response
    {
        try {


            /*
                      if(empty($_POST['email']));

                      if(!empty($_POST['password']));

                      if(!empty($_POST['birthday']));

                      if(!checkEmail($_POST['email']));

                      if(!checkPassword($_POST['password']));

                      if(!checkBirthday($_POST['birthday']));

                      if(!empty($_POST['phone_number']))
                        if(!checkPhoneNumber($_POST['phone_number']));
            */
            // TODO - Validate data before instantiating the user
            $data = $request->getParsedBody();
            $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
            $_SESSION['email'] = $data['email'];
            $_SESSION['verification_code'] = $verification_code;
            $user = new User(
                $data['email'],
                $data['password'],
                new DateTime(),
                new DateTime(),
                $data['birthday'],
                $data['phone_number'] ?? '',
                $verification_code,
                'not verified',
                20.00
            );

            if(!$this->container->get('user_repository')->userExists($data['email'])){

                if(!$user->checkBirthday($data['birthday'])){
                  return $this->container->get('view')->render(
                      $response,
                      'sign-up.twig',
                      [
                          'error' => "User must be older than 18"
                      ]
                  );
                }

                $this->container->get('user_repository')->save($user);
                //echo "User exists\n";
                $verification = new EmailVerification($data['email']);
                $verification->sendMail($verification_code);
            }
            else{
                //TODO Mostrar que l'usuari ja existeix
                //echo "User no exists\n";
                echo "<script type='text/javascript'>
                    alert('Error, user already exists');
                    window.location.href='/sign-up';
                </script>";


            }
        } catch (Exception $exception) {
            $response->getBody()
                ->write('Unexpected error: ' . $exception->getMessage());
            return $response->withStatus(500);
        }



        return $response->withStatus(201);

    }

    public function showActivation(Request $request, Response $response): Response
    {
        if( isset($_SESSION['logged'])){
            return $this->container->get('view')->render(
                $response,
                'profile.twig'
            );
        }
        return $this->container->get('view')->render(
            $response,
            'activation.twig'
        );
    }
}
