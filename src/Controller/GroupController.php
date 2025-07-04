<?php

namespace App\Controller;

use App\Service\GroupService;
use App\Service\ApiResponseFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{
    private GroupService $groupService;
    private EntityManagerInterface $entityManager;
    private ApiResponseFormatter $apiFormatter;

    public function __construct(
        GroupService $groupService,
        EntityManagerInterface $entityManager,
        ApiResponseFormatter $apiFormatter
    ) {
        $this->groupService = $groupService;
        $this->entityManager = $entityManager;
        $this->apiFormatter = $apiFormatter;
    }

    #[Route('/api/groups', name: 'get_groups', methods: ['GET'])]
    public function getGroups(): JsonResponse
    {
        $groups = $this->groupService->getAllGroups();

        $data = [];
        foreach ($groups as $group) {
            $data[] = [
                'id' => $group->getId(),
                'name' => $group->getName(),
                'description' => $group->getDescription(),
            ];
        }

        return $this->apiFormatter->success($data);
    }

    #[Route('/api/groups/{id}', name: 'get_group', methods: ['GET'])]
    public function getGroupById(int $id): JsonResponse
    {
        $group = $this->groupService->getGroupById($id);

        if (!$group) {
            return $this->apiFormatter->error('Group not found', 404);
        }

        return $this->apiFormatter->success([
            'id' => $group->getId(),
            'name' => $group->getName(),
            'description' => $group->getDescription(),
        ]);
    }

    #[Route('/api/groups', name: 'create_group', methods: ['POST'])]
    public function createGroup(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $group = $this->groupService->createGroup($data);

        return $this->apiFormatter->success([
            'id' => $group->getId(),
            'message' => 'Group created successfully'
        ], 201);
    }

    #[Route('/api/groups/{id}', name: 'update_group', methods: ['PUT'])]
    public function updateGroup(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $group = $this->groupService->getGroupById($id);

        if (!$group) {
            return $this->apiFormatter->error('Group not found', 404);
        }

        $this->denyAccessUnlessGranted('GROUP_EDIT', $group);

        $this->groupService->updateGroup($group, $data);

        return $this->apiFormatter->success(['message' => 'Group updated successfully']);
    }

    #[Route('/api/groups/{id}', name: 'delete_group', methods: ['DELETE'])]
    public function deleteGroup(int $id): JsonResponse
    {
        $group = $this->groupService->getGroupById($id);

        if (!$group) {
            return $this->apiFormatter->error('Group not found', 404);
        }

        $this->denyAccessUnlessGranted('GROUP_DELETE', $group);

        $this->groupService->deleteGroup($group);

        return $this->apiFormatter->success(['message' => 'Group deleted successfully']);
    }
}