<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;

class CommentService
{
    private CommentRepository $commentRepository;
    private PostRepository $postRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(CommentRepository $commentRepository, PostRepository $postRepository, EntityManagerInterface $entityManager)
    {
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllComments(): array
    {
        return $this->commentRepository->findAllComments();
    }

    public function getCommentById(int $id): ?Comment
    {
        return $this->commentRepository->findCommentById($id);
    }

    public function createComment(array $data, User $user): Comment
    {
        $comment = new Comment();
        $comment->setContent($data['content'] ?? '');
        $comment->setCreatedAt(new \DateTimeImmutable());
        $comment->setUser($user);

        // Przypisz post jeśli podano post_id
        if (!empty($data['post_id'])) {
            $post = $this->postRepository->find($data['post_id']);
            if ($post) {
                $comment->setPost($post);
            }
        }

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $comment;
    }

    public function updateComment(Comment $comment, array $data): void
    {
        if (isset($data['content'])) {
            $comment->setContent($data['content']);
        }
        $this->entityManager->flush();
    }

    public function deleteComment(Comment $comment): void
    {
        $this->entityManager->remove($comment);
        $this->entityManager->flush();
    }
}