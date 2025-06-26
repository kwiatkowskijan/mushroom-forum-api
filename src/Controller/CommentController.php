<?php

namespace App\Controller;

use App\Service\CommentService;
use App\Service\ApiResponseFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    private CommentService $commentService;
    private EntityManagerInterface $entityManager;
    private ApiResponseFormatter $apiFormatter;

    public function __construct(
        CommentService $commentService,
        EntityManagerInterface $entityManager,
        ApiResponseFormatter $apiFormatter
    ) {
        $this->commentService = $commentService;
        $this->entityManager = $entityManager;
        $this->apiFormatter = $apiFormatter;
    }

    #[Route('/api/comments', name: 'get_comments', methods: ['GET'])]
    public function getComments(): JsonResponse
    {
        $comments = $this->commentService->getAllComments();

        $data = [];
        foreach ($comments as $comment) {
            $data[] = [
                'id' => $comment->getId(),
                'content' => $comment->getContent(),
                'createdAt' => $comment->getCreatedAt()?->format('Y-m-d H:i:s'),
                'user' => $comment->getUser()?->getId(),
                'post' => $comment->getPost()?->getId(),
            ];
        }

        return $this->apiFormatter->success($data);
    }

    #[Route('/api/comments/{id}', name: 'get_comment', methods: ['GET'])]
    public function getCommentById(int $id): JsonResponse
    {
        $comment = $this->commentService->getCommentById($id);

        if (!$comment) {
            return $this->apiFormatter->error('Comment not found', 404);
        }

        return $this->apiFormatter->success([
            'id' => $comment->getId(),
            'content' => $comment->getContent(),
            'createdAt' => $comment->getCreatedAt()?->format('Y-m-d H:i:s'),
            'user' => $comment->getUser()?->getId(),
            'post' => $comment->getPost()?->getId(),
        ]);
    }

    #[Route('/api/comments', name: 'create_comment', methods: ['POST'])]
    public function createComment(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();

        if (!$user) {
            return $this->apiFormatter->error('User not authenticated', 401);
        }

        $comment = $this->commentService->createComment($data, $user);

        return $this->apiFormatter->success([
            'id' => $comment->getId(),
            'message' => 'Comment created successfully'
        ], 201);
    }

    #[Route('/api/comments/{id}', name: 'update_comment', methods: ['PUT'])]
    public function updateComment(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $comment = $this->commentService->getCommentById($id);

        if (!$comment) {
            return $this->apiFormatter->error('Comment not found', 404);
        }

        $this->denyAccessUnlessGranted('COMMENT_EDIT', $comment);

        $this->commentService->updateComment($comment, $data);

        return $this->apiFormatter->success(['message' => 'Comment updated successfully']);
    }

    #[Route('/api/comments/{id}', name: 'delete_comment', methods: ['DELETE'])]
    public function deleteComment(int $id): JsonResponse
    {
        $comment = $this->commentService->getCommentById($id);

        if (!$comment) {
            return $this->apiFormatter->error('Comment not found', 404);
        }

        $this->denyAccessUnlessGranted('COMMENT_DELETE', $comment);

        $this->commentService->deleteComment($comment);

        return $this->apiFormatter->success(['message' => 'Comment deleted successfully']);
    }
}