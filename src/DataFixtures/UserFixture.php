<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('david.pautrat@gmail.com'); // Votre email d'administrateur
        $user->setRoles(['ROLE_ADMIN']); // Attribuez-lui le rôle d'administrateur
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            '123456' // Le mot de passe en clair pour le test (ne pas utiliser en production !)
        );
        $user->setPassword($hashedPassword);

        $manager->persist($user);
        $manager->flush();
    }
}