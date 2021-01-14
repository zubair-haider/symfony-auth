<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UserController extends ApiController
{
    public function getToken(JWTTokenManagerInterface $JWTManager)
    {
        $user = new JWTUser($email, $roles);
        $token = $JWTManager->create($user);
        return $this->respondWithSuccess($token);
    }

    public function showUser($id, HttpClientInterface $client)
    {
        try {
            $url = $_ENV['DATA_URL'] . "api/user/" . $id;
            $response = $client->request('GET', $url);
            $data = $response->toArray()['success'];
            return $this->respondWithSuccess($data);
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }

    public function updateUser($id, Request $request, HttpClientInterface $client)
    {
        try {
            $request = $this->transformJsonBody($request);
            $roles = $request->get('roles');
            $url = $_ENV['DATA_URL'] . "api/user/" . $id;
            $response = $client->request('PUT', $url,
                [
                    'json' => ['roles' => $roles],
                ]);
            return $this->respondWithSuccess("User updated");
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }

    public function createUser(Request $request, HttpClientInterface $client)
    {
        try {
            $request = $this->transformJsonBody($request);
            $password = $request->get('password');
            $email = $request->get('email');
            $roles = $request->get('roles');
            if (empty($password) || empty($email)) {
                return $this->respondValidationError("Invalid Password or Email");
            }
            $data = [
                'password' => $password,
                'email' => $email,
                'roles' => $roles,
            ];
            $url = $_ENV['DATA_URL'] . "api/user";
            $response = $client->request('POST', $url, ['json' => $data]);

            return $this->respondWithSuccess("User Created");
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }

    public function deleteUser($id, HttpClientInterface $client, Request $request)
    {
        try {
            $request = $this->transformJsonBody($request);
            $roles = $request->get('roles');
            $url = $_ENV['DATA_URL'] . "api/user/" . $id;
            $response = $client->request("DELETE", $url);
            return $this->respondWithSuccess("user deleted");
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }

}
