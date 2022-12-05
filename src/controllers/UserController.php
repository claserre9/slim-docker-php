<?php

namespace App\controllers;


use App\entities\User;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;


class UserController extends AbstractController
{

    public function create(Request $request, Response $response, array $args)
    {
        list(
            'firstname' => $firstName,
            'lastname' => $lastName,
            'email' => $email,
            'password' => $password,
            ) = $request->getParsedBody();

        $user = (new User())
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setEmail($email)
            ->setPassword($password);

        $errors = $this->validate($user);
        if (count($errors) > 0) {
            return $this->JsonResponse($response, json_encode($errors));
        }

//        $this->getEntityManager()->getEventManager()->addEventSubscriber($this->userEventSubscriber);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        $userJson = $this->serialize($user, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['password']]);


        return $this->JsonResponse($response, $userJson);

    }


    public function getAll(Request $request, Response $response, array $args)
    {
        $users = $this->getRepository(User::class)->findAll();

        $usersJson = $this->serialize($users);

        return $this->JsonResponse($response, $usersJson);
    }


    public function get(Request $request, Response $response, array $args)
    {

    }


    public function update(Request $request, Response $response, array $args)
    {

    }

    public function delete(Request $request, Response $response, array $args)
    {

    }

    public function login(Request $request, Response $response, array $args): Response
    {
        list(
            'email' => $email,
            'password' => $password,
            ) = $request->getParsedBody();
        /** @var User $user */
        $user = $this->getRepository(User::class)->findOneBy(array('email' => $email));

        if ($user) {
            if (password_verify($password, $user->getPassword())) {

                $token = $this->createJWTToken($user);

                return $this->JsonResponse($response, $token);
            }
        }

        throw new HttpBadRequestException($request, 'Invalid Credentials');

    }

    /**
     * @param \App\entities\User $user
     * @return false|string
     */
    public function createJWTToken(User $user)
    {
        $key = getenv('APP_SECRET');
        $issueAt = new \DateTimeImmutable();
        $expire = $issueAt->modify('+5 minutes')->getTimestamp();
        $uniqueUserId = $user->getEmail();


        $payload = [
            'iss' => getenv('APP_DOMAIN_NAME'),
            'iat' => $issueAt->getTimestamp(),
            'nbf' => $issueAt->getTimestamp(),
            'exp' => $expire,
            'data' => [
                'username' => $uniqueUserId,
            ]
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');
        return json_encode(['jwt' => $jwt]);
    }

}