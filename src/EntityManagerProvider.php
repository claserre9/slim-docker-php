<?php

namespace App;

use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMSetup;

class EntityManagerProvider
{

    public static function getEntityManager()
    {
        $config = ORMSetup::createAnnotationMetadataConfiguration(
            array(__DIR__ . "/entities"),
            true
        );


        $conn = array(
            'url' => getenv('APP_DB_URL')
        );



        try {
            return EntityManager::create($conn, $config);

        } catch (ORMException $e) {
            exit($e->getMessage() . PHP_EOL);
        }

    }

}