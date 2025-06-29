namespace App\Tests\Service;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Group;
use App\Repository\PostRepository;
use App\Service\PostService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class PostServiceTest extends TestCase
{
    public function testCreatePost()
    {
        $user = new User();
        $group = new Group();

        $data = [
            'title' => 'Test Post',
            'content' => 'To jest treść testowego posta',
        ];

        $postRepository = $this->createMock(PostRepository::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Post::class));

        $entityManager->expects($this->once())
            ->method('flush');

        $service = new PostService($entityManager, $postRepository);
        $post = $service->createPost($data, $user, $group);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals('Test Post', $post->getTitle());
        $this->assertEquals('To jest treść testowego posta', $post->getContent());
        $this->assertSame($user, $post->getAuthor());
        $this->assertSame($group, $post->getGroup());
    }
}
