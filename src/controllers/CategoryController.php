<?php

namespace App\controllers;

use App\entities\Category;
use App\entities\Product;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

class CategoryController extends AbstractController
{

    public function create(Request $request, Response $response, array $args)
    {
        $name = $request->getParsedBody()['name'];

        $category = new Category();
        $category->setName($name);

        $errors = $this->validate($category);
        if (count($errors) > 0) {
            return $this->JsonResponse($response, json_encode($errors));
        }

        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush();

        $productJson = $this->serialize($category);

        return $this->JsonResponse($response, $productJson);
    }

    public function getAll(Request $request, Response $response, array $args)
    {
        $categories = $this->getRepository(Category::class)->findAll();

        $categoriesJson = $this->serialize($categories);

        return $this->JsonResponse($response, $categoriesJson);
    }

    public function get(Request $request, Response $response, array $args)
    {
        /** @var Category $category */
        $category = $this->findOr404($request, Category::class, $args['id']);

        $categoryJson = $this->serialize($category);

        return $this->JsonResponse($response, $categoryJson);
    }

    public function update(Request $request, Response $response, array $args)
    {
        list('name' => $name,) = $request->getParsedBody();

        /** @var Category $category */
        $category = $this->getRepository(Product::class)->find($args['id']);
        if ($category) {
            $category->setName($name);


            $errors = $this->validate($category);
            if (count($errors) > 0) {
                return $this->JsonResponse($response, json_encode($errors));
            }

            $this->getEntityManager()->flush();

            $categoryJson = $this->serialize($category);
            return $this->JsonResponse($response, $categoryJson);
        } else {
            throw new HttpNotFoundException($request, 'Resource not found!');
        }
    }

    public function delete(Request $request, Response $response, array $args)
    {
        /** @var Category $category */
        $category = $this->findOr404($request, Category::class, $args['id']);

        $this->getEntityManager()->remove($category);
        $this->getEntityManager()->flush();

        return $this->JsonResponse($response, "", 204);
    }

}