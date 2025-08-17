<?php

namespace App\Command;

use App\Entity\User; // Assurez-vous que c'est bien votre entité utilisateur
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:hash-password',
    description: 'Hashes a plain password for manual DB update.',
)]
class HashPasswordCommand extends Command
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('plainPassword', InputArgument::REQUIRED, 'The plain password to hash')
            ->addArgument('userEmail', InputArgument::OPTIONAL, 'The email of the user to update directly (optional)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $plainPassword = $input->getArgument('plainPassword');
        $userEmail = $input->getArgument('userEmail');

        // Hacher le mot de passe
        // Nous utilisons un objet User bidon pour le hachage car le hasher a besoin d'une instance de UserInterface
        // Le sel n'est plus pertinent avec les derniers algorithmes de hachage comme Argon2i ou bcrypt.
        $hashedPassword = $this->passwordHasher->hashPassword(new User(), $plainPassword);

        $io->writeln(sprintf('Plain Password: <info>%s</info>', $plainPassword));
        $io->writeln(sprintf('Hashed Password: <comment>%s</comment>', $hashedPassword));

        if ($userEmail) {
            // Tenter de trouver l'utilisateur et mettre à jour le mot de passe directement
            $userRepository = $this->entityManager->getRepository(User::class);
            $user = $userRepository->findOneBy(['email' => $userEmail]);

            if ($user) {
                $user->setPassword($hashedPassword);
                $this->entityManager->flush();
                $io->success(sprintf('User <info>%s</info> password updated directly in DB.', $userEmail));
            } else {
                $io->warning(sprintf('User with email <error>%s</error> not found. Password not updated.', $userEmail));
            }
        }

        $io->note('You can now use this Hashed Password to manually update your database.');

        return Command::SUCCESS;
    }
}