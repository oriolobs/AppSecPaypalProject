<?php

namespace Pw\SlimApp\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class ProfileController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function showProfilePage(Request $request, Response $response): Response
    {
        if(!isset($_SESSION['logged'])){
            return $response->withHeader('Location', '/')->withStatus(301);
        }
        $user = $this->container->get('user_repository')->getUserInfo($_SESSION['email']);
        return $this->container->get('view')->render(
            $response,
            'profile.twig',
            [
                'email' => $user->email(),
                'birthday' => $user->birthday(),
                'phone_number' => $user->phone_number(),
                'image' => $user->image_user_url()
            ]
        );
    }

    public function transitionToSecurity(Request $request, Response $response):Response
    {
        return $this->container->get('view')->render(
          $response,
          'security.twig'
      );
    }

    public function postUploadChangeUser(Request $request, Response $response){
      try {


        $data = $request->getParsedBody();
        $phone_number = $data['phone_number'];

        \Cloudinary::config(array(
          "cloud_name" => "dumd56xwy",
          "api_key" => "212822638412729",
          "api_secret" => "mYsI3QKVHC81NP9B1glpDkSnsFE"
        ));

        if($_FILES["myfile"]["tmp_name"] != "" && $_FILES["myfile"]["size"] < 1000000){
          \Cloudinary\Uploader::upload($_FILES["myfile"]["tmp_name"], array("public_id" => $_SESSION["email"],
                                                                            "format" => "PNG"));

          $image = \Cloudinary\Uploader::upload($_FILES["myfile"]["tmp_name"], array("public_id" => $_SESSION["email"]));

          $image_profile_url = $image["secure_url"];
          $url_modified = explode("upload", $image_profile_url);
          $size = "c_scale,h_400,w_400";

          $image_profile = $url_modified[0] . "upload/" . $size . $url_modified[1];

          $this->container->get('user_repository')->setProfile($_SESSION['email'], $image_profile, $phone_number);
        } //if
        else{
            $this->container->get('user_repository')->setProfile($_SESSION['email'], "", $phone_number);
        }

      } catch (Exception $exception) {
          $response->getBody()
              ->write('Unexpected error: ' . $exception->getMessage());
          return $response->withStatus(500);
      }

      return $response->withHeader('Location', '/profile')->withStatus(301);
    }

}
