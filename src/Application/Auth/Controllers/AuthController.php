<?php

declare(strict_types=1);

namespace Application\Auth\Controllers;

use Engine\Auth\Account\Account;
use Engine\Auth\Authentication\Authentication;
use Engine\Auth\Services\TokenService;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthController
{
    public function __construct(private Authentication $authentication, private TokenService $service){}

    public function auth(ServerRequestInterface $request) : ResponseInterface{
        $credentials = $request->getAttribute('Credentials');
        /** @var Account $auth */
        $auth = $this->authentication->authinticate($credentials);
        if ($auth->code === 200){
            $accessToken = $this->service->generateAccessToken($auth->body->id);
            $refreshToken = $this->service->generateRefreshToken($auth->body->id);
            //setcookie('httpOnlyCookie', 'cookie data', time() + (86400 * 30), '/', '', true, true); // 30 дней
            return new JsonResponse($auth->body = ['AccessToken' => $accessToken, 'RefreshToken' => $refreshToken]);
        }
    }

    public function refresh(ServerRequestInterface $request) : ResponseInterface{
        $token = ($request->getHeader('refresh')[0]);
        $result = $this->service->refresh($token);
        return new JsonResponse($result);
    }

}