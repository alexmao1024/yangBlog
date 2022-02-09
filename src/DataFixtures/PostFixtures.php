<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\PostFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public const LAST_POST = 'last_post';
    private PostFactory $postFactory;
    private $faker;

    public function __construct(PostFactory $postFactory)
    {
        $this->postFactory = $postFactory;
        $this->faker=Factory::create('zc_CN');
    }

    public function load(ObjectManager $manager,): void
    {
        $userRepo = $manager->getRepository(User::class);
        $editor = $userRepo->findOneBy(['username' => 'editor']);
        $simpleAdmin = $userRepo->findOneBy(['username' => 'simpleAdmin']);
        $admin = $userRepo->findOneBy(['username' => 'admin']);
        $userArray = [$editor,$simpleAdmin,$admin];

        $last_post = null;
        for ($i=0;$i<20;$i++)
        {
            $post = $this->postFactory->create($this->faker->sentence(), $this->faker->paragraph());
            if ($this->faker->boolean())
            {
                $post->setStatus(['published'=>1]);
            }

            $image = '00'.$this->faker->randomDigit().'.jpg';
            $post->setPostImage($image);

            if ($i == 19){
                $post->setStatus(['published'=>1]);
                $last_post = $post;
            }
            $authorRandIndex = array_rand($userArray);
            $post->setAuthor($userArray[$authorRandIndex]);

            $manager->persist($post);
        }

        $this->addReference(self::LAST_POST,$last_post);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }
}
