<?php
namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AuthController extends ApiController
{
    public function register(Request $request, HttpClientInterface $client)
    {
        try {
            $request = $this->transformJsonBody($request);
            $password = $request->get('password');
            $email = $request->get('email');
            if (empty($password) || empty($email)) {
                return $this->respondValidationError("Password or Email");
            }
            $url = $_ENV['DATA_URL'] . "register";
            $response = $client->request(
                'POST',
                $url,
                [
                    'json' => ['email' => $email, "password" => $password],
                ]
            );
            $data = $response->toArray();
            return $this->respondWithSuccess($data);
        } catch (\Exception $e) {
            return $this->respondValidationError("Unable to Create user");
        }
    }

    public function login(Request $request, HttpClientInterface $client, JWTTokenManagerInterface $JWTManager)
    {
        try {
            $request = $this->transformJsonBody($request);
            $password = $request->get('password');
            $email = $request->get('email');
            $url = $_ENV['DATA_URL'] . "login";
            $response = $client->request(
                'POST',
                $url,
                [
                    'json' => ['username' => $email, "password" => $password],
                ]
            );
            $data = $response->toArray()['success'];
            $roles = $data['roles'];
            $email = $data['username'];
            $user = new JWTUser($email, $roles);
            $token = $JWTManager->create($user);
            $returnData = ["data" => $data, "token" => $token];
            return $this->respondWithSuccess($returnData);
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }

    }
}
