<?php

use App\EntityManagerProvider;

use Doctrine\ORM\EntityManagerInterface;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use function DI\get;


return [

    "twig" => function(){
        $loader = new FilesystemLoader(__DIR__.'/templates');
        return new Environment($loader);
    },

    EntityManagerInterface::class => function () {
        return EntityManagerProvider::getEntityManager();
    },


    ValidatorInterface::class => function () {
        return Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->addDefaultDoctrineAnnotationReader()
            ->getValidator();
    },

    SerializerInterface::class => function () {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer(), new JsonSerializableNormalizer()];
        return new Serializer($normalizers, $encoders);

    },


];