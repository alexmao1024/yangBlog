<?php

namespace App\DataFixtures;

use App\Factory\PostFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PostFixtures extends Fixture
{
    private PostFactory $postFactory;

    public function __construct(PostFactory $postFactory)
    {
        $this->postFactory = $postFactory;
    }

    public function load(ObjectManager $manager,): void
    {
        for ($i=0;$i<6;$i++)
        {
            $post = $this->postFactory->create('My Post 0' . $i, 'This is my No.' . $i . ' Post');
            $manager->persist($post);
        }

        $manager->flush();
    }
}
