<?php

namespace App\entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="products")
 * @ORM\HasLifecycleCallbacks()
 * @OA\Schema(
 *     description="Product",
 *     type="object",
 *     title="Product"
 * )
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @OA\Property()
     */
    private int $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Product
     */
    public function setId(int $id): Product
    {
        $this->id = $id;
        return $this;
    }
    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     * @OA\Property()
     */
    private string $name;

    /**
     * @ManyToOne(targetEntity="Category", fetch="EAGER")
     * @JoinColumn(name="category_id", referencedColumnName="id")
     * @OA\Property(ref="#/components/schemas/Category")
     */
    private Category $category;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Product
     */
    public function setName(string $name): Product
    {
        $this->name = $name;
        return $this;
    }


    /**
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Category $category

     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    }


}