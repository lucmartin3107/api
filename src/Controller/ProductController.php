<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Security\ApiKeyAuthenticator;
use Doctrine\DBAL\Driver\Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends ApiController
{

    /**
     * @Route("/api/products", name="app_product_all", methods={"GET"})
     */
    public function showAll(ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->findAll();

        return $this->json($product);
    }

    /**
     * @Route("/api/products/{id}", name="app_product_one", methods={"GET"})
     */
    public function showOne(ProductRepository $productRepository, int $id): JsonResponse
    {
        $product = $productRepository->find(['id' => $id]);

        if (!$product) {
            return $this->respondNotFound();
        }
        return $this->json($product);
    }

    /**
     * @Route("/api/products", name="app_product_create", methods={"POST"})
     */
    public function create(Request $request, ApiKeyAuthenticator $authenticator): JsonResponse
    {
        $request = $this->transformJsonBody($request);
        $entityManager = $this->getDoctrine()->getManager();

        $product = new Product();
        $product
            ->setName($request->get('name', 0), '')
            ->setDescription($request->get('description', 0), '')
            ->setPhoto($request->get('photo', 0), '')
            ->setPrice($request->get('price', 0), 0);

        $entityManager->persist($product);
        $entityManager->flush();


        return $this->json('product ' . $product->getName() . ' created successfully');
    }

    /**
     * @Route("/api/products/{id}", name="app_product_update", methods={"PUT"})
     */
    public function update(Request $request, ProductRepository $productRepository, int $id): JsonResponse
    {
        $request = $this->transformJsonBody($request);

        $product = $productRepository->find(['id' => $id]);

        if (!$product) {
            return $this->respondNotFound();
        }

        $entityManager = $this->getDoctrine()->getManager();

        if ($request->get('name')) {
            $product
                ->setName($request->get('name'));
        }
        if ($request->get('description')) {
            $product
                ->setName($request->get('description'));
        }
        if ($request->get('photo')) {
            $product
                ->setName($request->get('photo'));
        }
        if ($request->get('price')) {
            $product
                ->setName($request->get('price'));
        }

        $entityManager->persist($product);
        $entityManager->flush();


        return $this->json('product ' . $product->getName() . ' updated successfully');
    }

    /**
     * @Route("/api/products/{id}", name="app_product_delete", methods={"DELETE"})
     */
    public function delete(ProductRepository $productRepository, int $id): JsonResponse
    {
        $product = $productRepository->find(['id' => $id]);

        if (!$product) {
            return $this->respondNotFound();
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($product);
        $entityManager->flush();


        return $this->json('product ' . $product->getName() . ' deleted successfully');
    }

    /**
     * @Route("/api/carts/validate", name="app_product_validate", methods={"PUT"})
     */
    public function validate(OrderRepository $orderRepository, CartRepository $cartRepository): JsonResponse
    {
        $cart = $cartRepository->findOneFirst();

        $order = $orderRepository->findOneFirst();

        $order->setCreationDate(new \DateTime('now'));

        foreach ($cart->getProduct() as $p) {
            $order->addProducts($p);
            $order->setTotalPrice($order->getTotalPrice() + $p->getPrice());
        }

        return $this->json($orderRepository->findOneFirst());
    }

    /**
     * @Route("/api/carts/{id}", name="app_product_add", methods={"PUT"})
     */
    public function AddOrDeleteToCart(CartRepository $cartRepository, ProductRepository $productRepository, int $id): JsonResponse
    {
        $cart = $cartRepository->findOneFirst();

        $entityManager = $this->getDoctrine()->getManager();

        $product = $cartRepository->findOneByIdJoinedToCategory($id);

        if ($product) {
          $cart->removeProduct($product);
          $entityManager->persist($cart);
          $entityManager->flush();

          return $this->json('product ' . $product->getName() . ' deleted successfully to the shopping cart');
        }

        $product = $productRepository->find(['id' => $id]);

        if (!$product) {
            return $this->respondNotFound();
        }

        $cart
              ->addProduct($product);

          $entityManager->persist($cart);
          $entityManager->flush();

          return $this->json('product ' . $product->getName() . ' added successfully to the shopping cart');
    }

    /**
     * @Route("/api/carts", name="app_product_show", methods={"GET"})
     */
    public function showCart(CartRepository $cartRepository, ApiKeyAuthenticator $authenticator): JsonResponse
    {
        $cart = $cartRepository->findOneFirst();

        return $this->json($cart);
    }
}
