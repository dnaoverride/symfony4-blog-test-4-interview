<?php

namespace App\DataFixtures;

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\User;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Cocur\Slugify\Slugify;
use Faker\Factory;
use Faker\Generator;
use Faker;


class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadCategories($manager);
        $this->loadArticles($manager);
    }

    private function loadArticles(ObjectManager $manager)
    {
        $slugify = new Slugify();
        $user = $this->getReference('user');
        $category = $this->getReference("category");
        $faker = Faker\Factory::create();


        for ($i = 0; $i < 10; $i++) {
            $article = new article();
            $article->setContent($faker->realText(1200) );
            $article->setTitle($faker->sentence(8,true));
            $article->setImageURL($faker->imageUrl(800,600,'technics',false));
            $article->setPublishedAt($faker->dateTimeBetween('-3 days','now'));
            $article->setUser($user);
            $article->setCategory($category);
            $slug = $slugify->slugify($article->getTitle());
            $article->setArticleslug($slug);
            $manager->persist($article);
        }

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('marko');
        $user->setFullName('Marko Marinovic');
        $user->setEmail('markomarinovic@gmail.com');
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
                'm4rk0123'
            )
        );
        $user->setRoles(["ROLE_ADMIN","ROLE_USER","ROLE_EDIT_ARTICLE"]);

        $this->setReference('user', $user);

        $manager->persist($user);
        $manager->flush();
    }

    private function loadCategories (ObjectManager $manager)
    {
        $category = new Category();
        $category->setName('First Category');
        $category->setCategorySlug('first-category');
        $category->setCreatedAt(new \DateTime());

        $this->setReference('category', $category);
        $manager->persist($category);
        $manager->flush();
    }

}