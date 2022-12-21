<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $listCompany = [];
        for ($i = 0; $i < 5; $i++) {
            $company = new Company();
            $company->setName("Company" . $i);
            $company->setRoles(["ROLE_USER"]);
            $company->setPassword($this->userPasswordHasher->hashPassword($company, "password"));
            $manager->persist($company);
            $listCompany[] = $company;
        }

        for ($i=0; $i < 30; $i++) {
            $product = new Product();
            $product->setName("Product" . $i);
            $product->setDescription("Description du produit : " . $i);
            $product->setPrice($i);
            $manager->persist($product);
        }

        for ($i=0; $i < 20; $i++) {
            $user = new User();
            $user->setFirstName("Firstname" . $i);
            $user->setLastName("Lastname" . $i);
            $user->setCompany($listCompany[array_rand($listCompany)]);
            $user->setEmail("email@" . $i . ".com");
            $manager->persist($user);
        }

        $manager->flush();
    }
}
