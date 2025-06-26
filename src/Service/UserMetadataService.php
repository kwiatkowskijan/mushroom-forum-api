<?php

namespace App\Service;

use App\Entity\UserMetadata;
use App\Repository\UserMetadataRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserMetadataService
{
    private UserMetadataRepository $userMetadataRepository;
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(UserMetadataRepository $userMetadataRepository, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userMetadataRepository = $userMetadataRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllForUser(int $userId): array
    {
        return $this->userMetadataRepository->findAllForUser($userId);
    }

    public function create(int $userId, array $data): UserMetadata
    {
        $user = $this->userRepository->find($userId);

        $meta = new UserMetadata();
        $meta->setKey($data['key'] ?? '');
        $meta->setValue($data['value'] ?? '');
        $meta->setUser($user);

        $this->entityManager->persist($meta);
        $this->entityManager->flush();

        return $meta;
    }

    public function update(int $userId, int $metadataId, array $data): ?UserMetadata
    {
        $meta = $this->userMetadataRepository->findOneForUser($userId, $metadataId);

        if (!$meta) {
            return null;
        }

        if (isset($data['key'])) {
            $meta->setKey($data['key']);
        }
        if (isset($data['value'])) {
            $meta->setValue($data['value']);
        }

        $this->entityManager->flush();

        return $meta;
    }

    public function delete(int $userId, int $metadataId): bool
    {
        $meta = $this->userMetadataRepository->findOneForUser($userId, $metadataId);

        if (!$meta) {
            return false;
        }

        $this->entityManager->remove($meta);
        $this->entityManager->flush();

        return true;
    }
}