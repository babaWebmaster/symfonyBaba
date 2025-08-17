<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use App\Entity\Post;
use App\Entity\Image; 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Form\ChangePasswordFormType; 
use Symfony\Component\Routing\Attribute\Route;

#[AdminDashboard(routePath: '/admin-baba-5487894613734/back-office', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
        public function index(): Response
    {
         // Cette ligne redirige *directement* vers la liste des Posts
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(PostCrudController::class)->generateUrl());

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    /**
     * Affiche la liste des utilisateurs.
     */
    #[Route('/admin-baba-5487894613734/back-office/users', name: 'back_office_users')]
    public function users(Request $request, UserPasswordHasherInterface $userPasswordHasher,EntityManagerInterface $entityManager): Response
    {
       $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('currentPassword')->getData();
            if (!$userPasswordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('danger', 'Le mot de passe actuel est incorrect.');
            } else {
                $newPassword = $form->get('newPassword')->getData();
                $user->setPassword($userPasswordHasher->hashPassword($user, $newPassword));
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Votre mot de passe a été mis à jour.');
            }
            // Rediriger vers le NOM DE LA ROUTE de cette page après soumission.
            return $this->redirectToRoute('back_office_users'); // Utilise votre nom de route
        }

        // Le template rendu pour cette page personnalisée
        // Assurez-vous que ce template existe : templates/admin/users_profile_page.html.twig par exemple
        return $this->render('back_office/admin/user.html.twig', [
            'user' => $user,
            'changePasswordForm' => $form->createView(),
        ]);
    }

    
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Projet David');
    }

    public function configureMenuItems(): iterable
    {
         yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

         yield MenuItem::linkToCrud('Posts', 'fas fa-file-alt', Post::class);

         yield MenuItem::section('Médiathèque');
         yield MenuItem::linkToCrud('Images', 'fa fa-images', Image::class);

         yield MenuItem::section('Mon Compte'); // Nouvelle section (optionnel)
        yield MenuItem::linkToUrl('Mon Profil','fa fa-user','users');

        
    }
}
