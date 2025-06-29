namespace App\Tests\Service;

use App\Entity\Group;
use App\Entity\User;
use App\Service\GroupService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class GroupServiceTest extends TestCase
{
    public function testCreateGroup()
    {
        $user = new User();
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Group::class));

        $entityManager->expects($this->once())
            ->method('flush');

        $service = new GroupService($entityManager);
        $group = $service->createGroup('Nazwa grupy', $user);

        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals('Nazwa grupy', $group->getName());
        $this->assertSame($user, $group->getOwner());
    }
}
