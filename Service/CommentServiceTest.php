namespace App\Tests\Service;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Service\CommentService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CommentServiceTest extends TestCase
{
    public function testAddComment()
    {
        $user = new User();
        $post = new Post();

        $entityManager = $this->createMock(EntityManagerInterface::class);

        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Comment::class));

        $entityManager->expects($this->once())
            ->method('flush');

        $service = new CommentService($entityManager);
        $comment = $service->addComment($post, 'Testowy komentarz', $user);

        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals('Testowy komentarz', $comment->getContent());
        $this->assertSame($post, $comment->getPost());
        $this->assertSame($user, $comment->getAuthor());
    }
}
