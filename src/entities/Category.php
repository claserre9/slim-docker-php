<?php

namespace App\entities;

use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;


/**
 * @ORM\Entity
 * @ORM\Table(name="categories")
 * @OA\Schema(
 *     description="Category",
 *     type="object",
 *     title="Category"
 * )
 */
class Category
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @OA\Property()
     */
    private int $id;
    /**
     * @ORM\Column(type="string", unique=true)
     * @OA\Property()
     */
    private string $name;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Category
     */
    public function setId(int $id): Category
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Category
     */
    public function setName(string $name): Category
    {
        $this->name = $name;
        return $this;
    }



}