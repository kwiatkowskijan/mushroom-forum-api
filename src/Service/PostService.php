<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;

class PostService
{
    private PostRepository $postRepository;
    private GroupRepository $groupRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(PostRepository $postRepository, GroupRepository $groupRepository, EntityManagerInterface $entityManager)
    {
        $this->postRepository = $postRepository;
        $this->groupRepository = $groupRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllPosts(): array
    {
        return $this->postRepository->findAllPosts();
    }

    public function getPostById(int $id): ?Post
    {
        return $this->postRepository->findPostById($id);
    }

    public function createPost(array $data, User $user): Post
    {
        $post = new Post();
        $post->setTitle($data['title'] ?? '');
        $post->setContent($data['content'] ?? '');
        $post->setCreatedAt(new \DateTimeImmutable());
        $post->setUser($user);

        // Przypisz grupę jeśli podano group_id
        if (!empty($data['group_id'])) {
            $group = $this->groupRepository->find($data['group_id']);
            if ($group) {
                $post->setGroup($group);
            }
        }

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $post;
    }

    public function updatePost(Post $post, array $data): void
    {
        if (isset($data['title'])) {
            $post->setTitle($data['title']);
        }
        if (isset($data['content'])) {
            $post->setContent($data['content']);
        }
        $this->entityManager->flush();
    }

    public function deletePost(Post $post): void
    {
        $this->entityManager->remove($post);
        $this->entityManager->flush();
    }
}