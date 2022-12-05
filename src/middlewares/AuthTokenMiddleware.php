<?php

namespace App\middlewares;

use DateTimeImmutable;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use LogicException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;
use UnexpectedValueException;
use function DI\string;

class AuthTokenMiddleware implements MiddlewareInterface
{

    public function process(Request $request, RequestHandler $handler): Response
    {
        $params = $request->getServerParams();
        $authorization = $params['HTTP_AUTHORIZATION'] ?? null;


        if (! preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
            throw new HttpUnauthorizedException($request, 'Token not found');
        }

        $jwt = $matches[1];
        if (! $jwt) {
            throw new HttpUnauthorizedException($request, 'Token not found');
        }

        $secretKey  = getenv('APP_SECRET');


        JWT::$leeway += 60;

        try {
            $now = new DateTimeImmutable();
            $serverName = getenv('APP_DOMAIN_NAME');
            $decode_token = JWT::decode($jwt, new Key($secretKey, 'HS256'));

            if ($decode_token->iss !== $serverName ||
                $decode_token->nbf > $now->getTimestamp() ||
                $decode_token->exp < $now->getTimestamp())
            {
                throw new HttpUnauthorizedException($request, 'Invalid token');
            }

        } catch (LogicException|UnexpectedValueException $e) {
            throw new HttpBadRequestException($request, $e->getMessage());
        }
        
        return $handler->handle($request);
    }
}