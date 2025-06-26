<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use App\Service\ApiResponseFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private UserService $userService;
    private EntityManagerInterface $entityManager;
    private ApiResponseFormatter $apiFormatter;

    public function __construct(
        UserService $userService,
        EntityManagerInterface $entityManager,
        ApiResponseFormatter $apiFormatter
    ) {
        $this->userService = $userService;
        $this->entityManager = $entityManager;
        $this->apiFormatter = $apiFormatter;
    }

    #[Route('/api/users/current', name: 'get_current_user', methods: ['GET'])]
    public function getCurrentUser(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->apiFormatter->error('User not authenticated', 401);
        }

        return $this->apiFormatter->success([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ]);
    }

    #[Route('/api/users/{id}', name: 'get_user', methods: ['GET'])]
    public function getUserById(int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return $this->apiFormatter->error('User not found', 404);
        }

        return $this->apiFormatter->success([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ]);
    }

    #[Route('/api/users/{id}', name: 'update_user', methods: ['PUT'])]
    public function updateUser(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return $this->apiFormatter->error('User not found', 404);
        }

        $this->userService->updateUser($user, $data);

        return $this->apiFormatter->success(['message' => 'User updated successfully']);
    }
}