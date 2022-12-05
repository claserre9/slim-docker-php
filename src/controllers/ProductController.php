<?php

namespace App\controllers;

use App\entities\Category;
use App\entities\Product;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;



class ProductController extends AbstractController
{
    /**
     * @OA\Post(
     *     path="/api/products",
     *     @OA\Response(
     *         response="200",
     *         description="The data",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     )
     * )
     */
    public function create(Request $request, Response $response, array $args): Response
    {

        list(
            'name' => $name,
            'category_id' => $category_id
            ) = $request->getParsedBody();


        $product = new Product();
        $product->setName($name);
        /** @var Category $category */
        if ($category = $this->getRepository(Category::class)->find($category_id)) {
            $product->setCategory($category);
        } else {
            throw new HttpNotFoundException($request, 'Category not found');
        }

        $errors = $this->validate($product);
        if (count($errors) > 0) {
            return $this->JsonResponse($response, json_encode($errors));
        }

        $this->getEntityManager()->persist($product);

        $this->getEntityManager()->flush();

        $productJson = $this->serialize($product);

        return $this->JsonResponse($response, $productJson);

    }

    public function getAll(Request $request, Response $response, array $args): Response
    {


        $products = $this->getRepository(Product::class)->findAll();

        $productsJson = $this->serialize($products);

        return $this->JsonResponse($response, $productsJson);
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        /** @var Product $product */
        $product = $this->findOr404($request, Product::class, $args['id']);

        $productJson = $this->serialize($product);

        return $this->JsonResponse($response, $productJson);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        /** @var Product $product */
        $product = $this->findOr404($request, Product::class, $args['id']);

        $this->getEntityManager()->remove($product);
        $this->getEntityManager()->flush();

        return $this->JsonResponse($response, "", 204);
    }

    public function update(Request $request, Response $response, array $args): Response
    {

        list(
            'name' => $name,
            '$category_id' => $category_id
            ) = $request->getParsedBody();

        /** @var Product $product */
        $product = $this->getRepository(Product::class)->find($args['id']);
        if ($product) {
            $product->setName($name);

            if ($category = $this->getRepository(Category::class)->find($category_id)) {
                $product->setCategory($category);
            } else {
                throw new HttpBadRequestException($request, 'Category not found');
            }

            $errors = $this->validate($product);
            if (count($errors) > 0) {
                return $this->JsonResponse($response, json_encode($errors));
            }

            $this->getEntityManager()->flush();

            $productJson = $this->serialize($product);
            return $this->JsonResponse($response, $productJson);
        } else {
            throw new HttpNotFoundException($request, 'Resource not found!');
        }

    }


}