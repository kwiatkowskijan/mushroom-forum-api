<?php

namespace App\Service;

use App\Entity\Group;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;

class GroupService
{
    private GroupRepository $groupRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(GroupRepository $groupRepository, EntityManagerInterface $entityManager)
    {
        $this->groupRepository = $groupRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllGroups(): array
    {
        return $this->groupRepository->findAllGroups();
    }

    public function getGroupById(int $id): ?Group
    {
        return $this->groupRepository->findGroupById($id);
    }

    public function createGroup(array $data): Group
    {
        $group = new Group();
        $group->setName($data['name'] ?? '');
        $group->setDescription($data['description'] ?? null);

        $this->entityManager->persist($group);
        $this->entityManager->flush();

        return $group;
    }

    public function updateGroup(Group $group, array $data): void
    {
        if (isset($data['name'])) {
            $group->setName($data['name']);
        }
        if (array_key_exists('description', $data)) {
            $group->setDescription($data['description']);
        }
        $this->entityManager->flush();
    }

    public function deleteGroup(Group $group): void
    {
        $this->entityManager->remove($group);
        $this->entityManager->flush();
    }
}