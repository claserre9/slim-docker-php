<?php

namespace App\controllers;

use App\entities\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Psr\Http\Message\ResponseInterface as Response;

abstract class AbstractController
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private Serializer $serializer;

    /**
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }

    /**
     * @return Serializer
     */
    public function getSerializer(): Serializer
    {
        return $this->serializer;
    }


    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    public function JsonResponse(Response $response, string $json, int $status = 200): Response
    {
        $response->getBody()->write($json);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);

    }

    public function getRepository(string $className): EntityRepository
    {
        return $this->getEntityManager()->getRepository($className);
    }

    public function serialize($object, $format = 'json', $context=[]): string
    {
        return $this->getSerializer()->serialize($object, $format, $context);
    }

    public function deserialize($data, $className ,$format = 'json'){
        return $this->getSerializer()->deserialize($data, $className ,$format);
    }

    public function validate($object): array
    {
        $errors =  $this->getValidator()->validate($object);
        $errorList = [];
        if(count($errors)){
            foreach ($errors as $error) {
                $errorList[] = array($error->getPropertyPath() => $error->getMessage());
            }
        }
        return $errorList;
    }

    public function findOr404(Request $request, string $entityClassName, int $idOrPk)
    {
        $entityObject = $this->getRepository($entityClassName)->find($idOrPk);

        if (!$entityObject) {
            throw new HttpNotFoundException($request, 'Resource not found');
        }

        return $entityObject;

    }


    public function normalize($data, $format=null, $context = [])
    {
        try {
            return $this->getSerializer()->normalize($data, $format, $context);
        } catch (ExceptionInterface $e) {
            exit($e->getMessage().PHP_EOL);
        }
    }

    public function create(Request $request, Response $response, array $args){}
    public function getAll(Request $request, Response $response, array $args){}
    public function get(Request $request, Response $response, array $args){}
    public function update(Request $request, Response $response, array $args){}
    public function delete(Request $request, Response $response, array $args){}
}