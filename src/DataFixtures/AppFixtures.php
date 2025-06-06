<?php
namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Users
        $users = [];
        for ($u = 1; $u <= 5; $u++) {
            $user = new User();
            $user->setUsername($faker->unique()->userName());
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $manager->persist($user);
            $users[] = $user;
        }

        // Products
        for ($i = 1; $i <= 10; $i++) {
            $product = new Product();
            $product->setTitle($faker->words(3, true));
            $product->setDescription($faker->sentence(10));
            $product->setPrice($faker->randomFloat(2, 5, 200));
            $product->setReference('REF' . $faker->unique()->numerify('###'));
            $product->setImage(null);
            $manager->persist($product);
        }

        // Articles & Comments
        for ($i = 1; $i <= 7; $i++) {
            $article = new Article();
            $article->setTitle($faker->sentence(4));
            $article->setContent($faker->paragraph(5));
            $article->setImage(null);
            $manager->persist($article);

            // Comments for each article
            for ($j = 1; $j <= rand(2, 5); $j++) {
                $comment = new Comment();
                $comment->setContent($faker->sentence(8));
                $comment->setArticle($article);
                $comment->setAuthor($faker->randomElement($users));
                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}
