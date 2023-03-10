<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\Voter\UserVoter;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class UserController extends AbstractController
{
    public function __construct(private TagAwareCacheInterface $cache, SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Cette méthode permet de récupérer l'ensemble des ustilisateurs liés à un client.
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des utilisateurs liés à un client",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"getUsers"}))
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
     * @OA\Tag(name="Users")
     *
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/api/users', name: 'users', methods: ['GET'])]
    public function getAllUsers(
        UserRepository $userRepository,
        SerializerInterface $serializer,
        Request $request
    ): JsonResponse {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $idCache = "getAllUsers-" . $page . "-" . $limit;

        $jsonUserList = $this->cache->get($idCache, function (ItemInterface $item) use ($userRepository, $page, $limit, $serializer) {
            $company = $this->getUser();
            $context = SerializationContext::create()->setGroups(["getUsers"]);
            $item->tag("usersCache");
            $userList = $userRepository->findAllWithPagination($page, $limit, $company);
            return $serializer->serialize($userList, 'json', $context);
        });

        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }

    /**
     * Cette méthode permet de récuperer un utilisateur grâce à son ID
     * 
     * @OA\Response(
     *     response=200,
     *     description="Retourne le détail d'un utilisateurs liés à un client",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class))
     *     )
     * )
     * 
     * @OA\Response(
     *     response=404,
     *     description="Page introuvable",
     * )
     * 
     * @OA\Tag(name="Users")
     * @param int $id
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    #[Route('/api/users/{id<\d+>}', name: 'detailUser', methods: ['GET'])]
    public function getDetailUser(int $id, UserRepository $userRepository): JsonResponse
    {
        $this->denyAccessUnlessGranted(UserVoter::VIEW, $userRepository->find($id));
        $context = SerializationContext::create()->setGroups(['getUsers']);
        $jsonUser = $this->serializer->serialize($userRepository->find($id), 'json', $context);
        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }

    /**
     * Cette méthode permet de supprimer un utilisateur grâce à son ID
     * 
     * @OA\Response(
     *     response=204,
     *     description="Retourne un code 204 qui confirme la suppression de l'utilisateur",
     * )
     * 
     * @OA\Response(
     *     response=404,
     *     description="Page introuvable",
     * )
     * 
     * @OA\Tag(name="Users")
     * @param EntityManagerInterface $em
     * @param int $id
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    #[Route('/api/users/{id<\d+>}', name: 'deleteUser', methods: ['DELETE'])]
    public function deleteUser(int $id, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('USER_DELETE', $userRepository->find($id));
        $user = $userRepository->find($id);
        $this->cache->invalidateTags(["usersCache"]);
        $em->remove($user);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Cette méthode permet de créer un utilisateur
     * 
     * @OA\RequestBody(@Model(type=User::class, groups={"createUser"}))
     * @OA\Response(
     *     response=201,
     *     description="Créer un utilisateur lié à un client",
     *     @Model(type=User::class, groups={"createUser"})
     * )
     * 
     * @OA\Response(
     *     response=400,
     *     description="Mauvaise requête",
     * )
     * 
     * @OA\Tag(name="Users")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UrlGeneratorInterface $urlGenerator
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    #[Route('/api/users', name: "createUser", methods: ['POST'])]
    public function createUser(
        Request $request,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator
    ): JsonResponse {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');

        // On vérifie les erreurs
        $errors = $validator->validate($user);
        if ($errors->count() > 0) {
            return new JsonResponse($this->serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
            //throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, "La requête est invalide");
        }
        
        $user->setCompany($this->getUser());
        $em->persist($user);
        $em->flush();
        // On vide le cache. 
        $this->cache->invalidateTags(["usersCache"]);

        $jsonUser = $this->serializer->serialize($user, 'json', SerializationContext::create()->setGroups(["getUsers"]));
        $location = $urlGenerator->generate('detailUser', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ["Location" => $location], true);
    }
}
