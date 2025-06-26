<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create sample users
        $user1 = new User();
        $user1->setEmail('user1@example.com')
              ->setPassword('password1')
              ->setRoles(['ROLE_USER']);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('user2@example.com')
              ->setPassword('password2')
              ->setRoles(['ROLE_USER']);
        $manager->persist($user2);

        // Create a sample group
        $group = new Group();
        $group->setName('Group 1')
              ->setDescription('This is a sample group.');
        $manager->persist($group);

        // Create a sample post
        $post = new Post();
        $post->setTitle('Sample Post Title')
             ->setContent('This is the content of the sample post.')
             ->setCreatedAt(new \DateTimeImmutable())
             ->setUser($user1)
             ->setGroup($group);
        $manager->persist($post);

        // Create a sample comment
        $comment = new Comment();
        $comment->setContent('This is a sample comment.')
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUser($user2)
                ->setPost($post);
        $manager->persist($comment);

        // Flush all changes to the database
        $manager->flush();
    }
}