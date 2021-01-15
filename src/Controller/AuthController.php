<?php
namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AuthController extends ApiController
{
    public function register(Request $request, HttpClientInterface $client, JWTTokenManagerInterface $JWTManager)
    {
        try {
            $request = $this->transformJsonBody($request);
            $password = $request->get('password');
            $email = $request->get('email');
            if (empty($password) || empty($email)) {
                return $this->respondValidationError("Invalid Password or Email");
            }
            $url = $_ENV['DATA_URL'] . "admin/user/register";
            $response = $client->request(
                'POST',
                $url,
                [
                    'json' => ['email' => $email, "password" => $password],
                ]
            );
            $data = $response->toArray()['success'];
            $userId = $data['id'];
            $roles = $data['roles'];
            $email = $data['email'];
            $user = new JWTUser($email, $roles);
            $token = $JWTManager->create($user);

            $url = $_ENV['DATA_URL'] . "user/token";
            $response = $client->request(
                'POST',
                $url,
                [
                    'json' => ['userId' => $userId, "token" => $token],
                ]
            );
            $data = $response->toArray()['success'];
            $returnData = ["data"=>$data,"token"=>$token];
            return $this->respondWithSuccess($returnData);
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }

    public function login(Request $request, HttpClientInterface $client)
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
            return $this->respondWithSuccess($data);
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }

    }
}
