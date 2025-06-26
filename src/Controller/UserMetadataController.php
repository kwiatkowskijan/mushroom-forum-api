<?php

namespace App\Controller;

use App\Service\UserMetadataService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserMetadataController extends AbstractController
{
    private UserMetadataService $userMetadataService;
    private EntityManagerInterface $entityManager;

    public function __construct(UserMetadataService $userMetadataService, EntityManagerInterface $entityManager)
    {
        $this->userMetadataService = $userMetadataService;
        $this->entityManager = $entityManager;
    }

    #[Route('/api/users/{userId}/metadata', name: 'get_user_metadata', methods: ['GET'])]
    public function getUserMetadata(int $userId): JsonResponse
    {
        $metadata = $this->userMetadataService->getAllForUser($userId);

        $data = [];
        foreach ($metadata as $meta) {
            $data[] = [
                'id' => $meta->getId(),
                'key' => $meta->getKey(),
                'value' => $meta->getValue(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/api/users/{userId}/metadata', name: 'create_user_metadata', methods: ['POST'])]
    public function createUserMetadata(Request $request, int $userId): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $meta = $this->userMetadataService->create($userId, $data);

        return $this->json([
            'id' => $meta->getId(),
            'message' => 'Metadata created successfully'
        ], 201);
    }

    #[Route('/api/users/{userId}/metadata/{metadataId}', name: 'update_user_metadata', methods: ['PUT'])]
    public function updateUserMetadata(Request $request, int $userId, int $metadataId): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $meta = $this->userMetadataService->update($userId, $metadataId, $data);

        if (!$meta) {
            return $this->json(['message' => 'Metadata not found'], 404);
        }

        return $this->json(['message' => 'Metadata updated successfully']);
    }

    #[Route('/api/users/{userId}/metadata/{metadataId}', name: 'delete_user_metadata', methods: ['DELETE'])]
    public function deleteUserMetadata(int $userId, int $metadataId): JsonResponse
    {
        $deleted = $this->userMetadataService->delete($userId, $metadataId);

        if (!$deleted) {
            return $this->json(['message' => 'Metadata not found'], 404);
        }

        return $this->json(['message' => 'Metadata deleted successfully']);
    }
}