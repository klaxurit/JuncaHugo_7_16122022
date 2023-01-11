<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class ProductController extends AbstractController
{
    /**
     * Cette méthode permet de récupérer l'ensemble des produits BileMo.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des produits",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class))
     *     )
     * )
     * 
     * @OA\Response(
     *     response=404,
     *     description="Page introuvable",
     * )
     * 
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="La page que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     *
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Le nombre d'éléments que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Products")
     *
     * @param ProductRepository $productRepository
     * @param SerializerInterface $serializer
     * @param TagAwareCacheInterface $cache
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/products', name: 'products', methods: ['GET'])]
    public function getAllProducts(ProductRepository $productRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cache): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $idCache = "getAllProducts-" . $page . "-" . $limit;

        $productList = $cache->get($idCache, function (ItemInterface $item) use ($productRepository, $page, $limit) {
            $item->tag("productsCache");
            return $productRepository->findAllWithPagination($page, $limit);
        });

        $jsonProductList = $serializer->serialize($productList, 'json');
        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }

    /**
     * Cette méthode permet de récuperer un produit grâce à son ID
     * 
     * @OA\Response(
     *     response=200,
     *     description="Retourne le detail d'un produit",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class))
     *     )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Page introuvable",
     * )
     * 
     * @OA\Tag(name="Products")
     * @param int $id
     * @param ProductRepository $productRepository
     * @param SerializerInterface $serializer
     */
    #[Route('/api/products/{id<\d+>}', name: 'detailProduct', methods: ['GET'])]
    public function getDetailProduct(int $id, ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse 
    {
        $jsonProduct = $serializer->serialize($productRepository->findById($id), 'json');
        return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
    }
}
