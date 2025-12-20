<?php

declare(strict_types=1);

namespace Engine\Auth\Authentication;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CredentialsValidatorMiddleware implements MiddlewareInterface
{
    public function __construct (AuthErrors $errors, Credentials $credentials){
        $this->_credentials = $credentials;
        $this->_errors = $errors;
    }

    private function validateUserName() : void
    {
        if (!empty($userName = $this->_credentials->userName)){
            if (!preg_match("/^[a-zA-Z0-9]+$/", $userName)){
                $this->_errors->incorrectCharacters('UserName');
            }
        }else{
            $this->_errors->emptyField('UserName');
        }
    }

    private function validateUserPassword() : void
    {
        if (!empty($userPassword = $this->_credentials->userPassword)){
            if (!preg_match("/^[a-zA-Z0-9!@#$%^&*()-_=+]+$/", $userPassword)){
                $this->_errors->incorrectCharacters('UserPassword');
            }
        }else{
            $this->_errors->emptyField('UserPassword');
        }
    }

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->validateUserName();
        $this->validateUserPassword();
        if ($this->_errors->hasErrors()) {
            return new JsonResponse($this->_errors, 406);
        }
        $request = $request->withAttribute('Credentials', $this->_credentials);
        return $response = $handler->handle($request);
    }

}