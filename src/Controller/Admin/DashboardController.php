<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Entity\Image; 
use App\Entity\Contact;
use App\Entity\Maquette;
use App\Entity\CategoriesSite;
use App\Entity\Options;
use App\Repository\ImageRepository;
use App\Service\ImageProcessor;
use App\Service\ImageHtmlBuilder;
use App\Form\ChangePasswordFormType; 
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


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
     * Affiche le profil de l' utilisateur.
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

    
    #[Route('/admin-baba-5487894613734/back-office/api/images', name: 'admin_api_images')]
    public function apiImages(ImageRepository $imageRepository, ImageHtmlBuilder $imageHtmlBuilder): JsonResponse
    {
        $images = $imageRepository->findAll();

        $data = array_map(fn($image) => [
            'id' => $image->getId(),
            'url' => $imageHtmlBuilder->getImageUrlByFormat("original_compressed",$image->getFileName(),$image->getFormats()),             
        ], $images);

        return new JsonResponse($data);
    }

    #[Route('/admin-baba-5487894613734/back-office/images/bulk-upload', name: 'admin_image_bulk_upload')]
    public function bulkUpload(Request $request, ParameterBagInterface $params, ImageProcessor $imageProcessor, LoggerInterface $logger, EntityManagerInterface $entityManager): Response
    {
       $files = $request->files->get('multiUpload');
       
       foreach ($files as $file) {
             if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                     $originalName = $file->getClientOriginalName();
                     $extension = $file->guessExtension();
                     $baseName = pathinfo($originalName, PATHINFO_FILENAME);
                     $uploadedFileName = $baseName.'-'.uniqid().'.'.$extension;
             }
            
           
           $image = New Image;
           $image->setOriginalName($originalName);
           $image->setFileName($uploadedFileName);
           $image->setUpdatedAt(new \DateTimeImmutable());

            // transfer du fichier dans le dossier upload
           $uploadDir = $params->get('app.upload_dir');

        

       try {
                $movedFile = $file->move($uploadDir,$image->getFileName());
                $logger->info("Fichier déplacé :{$image->getFileName()}");
                //génération des images aux différents formats
                $formats = $imageProcessor->processImage($movedFile,$movedFile->getRealPath());
                 
                $image->setFormats($formats);

                //enregistrement en base de données
                $entityManager->persist($image);
                $entityManager->flush();

                

        } catch (\Throwable $e) {
            $logger->error("Echec du move pour {$file->getClientOriginalName()}",[
                'error' => $e->getMessage(),
            ]);

         }
        }
            return new JsonResponse([
                    'status' => 'success'
            ]);

    }



    #[Route('/admin-baba-5487894613734/back-office/api/gallery_images', name:'admin_api_gallery_images', methods: ['POST'])]
    public function apiGallery(Request $request, ImageRepository $imageRepository, ImageHtmlBuilder $imageHtmlBuilder): JsonResponse
    {
       $raw = $request->getContent();
       $ids = array_map('intval',explode(',',$raw));

       $images = $imageRepository->findBy(['id' => $ids]);

       $data = array_map(fn($image) =>[
        'id' => $image->getId(),
        'url' => $imageHtmlBuilder->getImageUrlByFormat("original_compressed",$image->getFilename(),$image->getFormats()),
       ],$images);

       return new jsonResponse($data);

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

        yield MenuItem::linkToCrud('Messages de contact', 'fas fa-envelope', Contact::class);

        yield MenuItem::linkToCrud('Maquettes', 'fas fa-palette', Maquette::class);

        yield MenuItem::linkToCrud('Catégorie site', 'fas fa-tags', CategoriesSite::class);

        yield MenuItem::linkToCrud('Options','',Options::class);
    }

 /**
 * @param Assets $assets
 */
  public function configureAssets(): Assets
    {
    return Assets::new()->addWebpackEncoreEntry('adminGallery');
    }

}
