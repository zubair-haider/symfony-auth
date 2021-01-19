<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ApiClient;

class UserController extends ApiController
{
    public function getToken(JWTTokenManagerInterface $JWTManager)
    {
        $user = new JWTUser("test", ['ROLE_USER']);
        $token = $JWTManager->create($user);
        return $this->respondWithSuccess($token);
    }

    public function userProfile(ApiClient $client, Request $request)
    {
        try {
            $token = $request->headers->get('Authorization');
            $url = $_ENV['DATA_URL'] . "api/user/me/";
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => $token,
                ],
            ]);
            $data = $response->toArray()['success'];
            return $this->respondWithSuccess($data);
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }

    public function userProfileUpdate(Request $request, ApiClient $client)
    {
        try {
            $token = $request->headers->get('Authorization');
            $request = $this->transformJsonBody($request);
            $roles = $request->get('roles');
            $url = $_ENV['DATA_URL'] . "api/user/me";
            $response = $client->request('PUT', $url,
                [
                    'headers' => [
                        'Authorization' => $token,
                    ],
                    'json' => ['roles' => $roles],
                ]);
            return $this->respondWithSuccess("User updated");
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }

    public function userProfileDelete(ApiClient $client)
    {
        try {
            $token = $request->headers->get('Authorization');
            $url = $_ENV['DATA_URL'] . "api/user/me/";
            $response = $client->request('DELETE', $url, [
                'headers' => [
                    'Authorization' => $token,
                ],
            ]);
            return $this->respondWithSuccess("user deleted");
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }

    public function showUser($id, ApiClient $client, Request $request)
    {
        try {
            $token = $request->headers->get('Authorization');
            $url = $_ENV['DATA_URL'] . "api/user/" . $id;
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => $token,
                ],
            ]);
            $data = $response->toArray()['success'];
            return $this->respondWithSuccess($data);
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }

    public function updateUser($id, Request $request, ApiClient $client)
    {
        try {
            $token = $request->headers->get('Authorization');
            $request = $this->transformJsonBody($request);
            $roles = $request->get('roles');
            $url = $_ENV['DATA_URL'] . "api/user/" . $id;
            $response = $client->request('PUT', $url,
                [
                    'headers' => [
                        'Authorization' => $token,
                    ],
                    'json' => ['roles' => $roles],
                ]);
            return $this->respondWithSuccess("User updated");
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }

    public function createUser(Request $request, ApiClient $client)
    {
        try {
            $token = $request->headers->get('Authorization');
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
            $response = $client->request('POST', $url, [
                'headers' => [
                    'Authorization' => $token,
                ], 'json' => $data]);

            return $this->respondWithSuccess("User Created");
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }

    public function deleteUser($id, Request $request,ApiClient $client)
    {
        try {
            $url = $_ENV['DATA_URL'] . "api/user/" . $id;
            $token = $request->headers->get('Authorization');
            $response = $client->request("DELETE", $url, [
                'headers' => [
                    'Authorization' => $token,
                ],
            ]);
            return $this->respondWithSuccess("user deleted");
        } catch (\Exception $e) {
            return $this->respondValidationError($e->getMessage());
        }
    }

}
