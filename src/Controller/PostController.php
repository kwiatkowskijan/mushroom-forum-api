<?php

namespace App\Controller;

use App\Entity\Post;
use App\Service\PostService;
use App\Service\ApiResponseFormatter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    private PostService $postService;
    private EntityManagerInterface $entityManager;
    private ApiResponseFormatter $apiFormatter;

    public function __construct(
        PostService $postService,
        EntityManagerInterface $entityManager,
        ApiResponseFormatter $apiFormatter
    ) {
        $this->postService = $postService;
        $this->entityManager = $entityManager;
        $this->apiFormatter = $apiFormatter;
    }

    #[Route('/api/posts', name: 'get_posts', methods: ['GET'])]
    public function getPosts(): JsonResponse
    {
        $posts = $this->postService->getAllPosts();

        $data = [];
        foreach ($posts as $post) {
            $data[] = [
                'id' => $post->getId(),
                'title' => $post->getTitle(),
                'content' => $post->getContent(),
                'createdAt' => $post->getCreatedAt()?->format('Y-m-d H:i:s'),
                'user' => $post->getUser()?->getId(),
                'group' => $post->getGroup()?->getId(),
            ];
        }

        return $this->apiFormatter->success($data);
    }

    #[Route('/api/posts/{id}', name: 'get_post', methods: ['GET'])]
    public function getPostById(int $id): JsonResponse
    {
        $post = $this->postService->getPostById($id);

        if (!$post) {
            return $this->apiFormatter->error('Post not found', 404);
        }

        return $this->apiFormatter->success([
            'id' => $post->getId(),
            'title' => $post->getTitle(),
            'content' => $post->getContent(),
            'createdAt' => $post->getCreatedAt()?->format('Y-m-d H:i:s'),
            'user' => $post->getUser()?->getId(),
            'group' => $post->getGroup()?->getId(),
        ]);
    }

    #[Route('/api/posts', name: 'create_post', methods: ['POST'])]
    public function createPost(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();

        if (!$user) {
            return $this->apiFormatter->error('User not authenticated', 401);
        }

        $post = $this->postService->createPost($data, $user);

        return $this->apiFormatter->success([
            'id' => $post->getId(),
            'message' => 'Post created successfully'
        ], 201);
    }

    #[Route('/api/posts/{id}', name: 'update_post', methods: ['PUT'])]
    public function updatePost(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $post = $this->postService->getPostById($id);

        if (!$post) {
            return $this->apiFormatter->error('Post not found', 404);
        }

        $this->denyAccessUnlessGranted('POST_EDIT', $post);

        $this->postService->updatePost($post, $data);

        return $this->apiFormatter->success(['message' => 'Post updated successfully']);
    }

    #[Route('/api/posts/{id}', name: 'delete_post', methods: ['DELETE'])]
    public function deletePost(int $id): JsonResponse
    {
        $post = $this->postService->getPostById($id);

        if (!$post) {
            return $this->apiFormatter->error('Post not found', 404);
        }

        $this->denyAccessUnlessGranted('POST_DELETE', $post);

        $this->postService->deletePost($post);

        return $this->apiFormatter->success(['message' => 'Post deleted successfully']);
    }
}