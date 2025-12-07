<?php

namespace App\Controller;

use App\Entity\Maquette;
use App\Form\MaquetteForm;
use App\Repository\MaquetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/maquette')]
final class MaquetteController extends AbstractController
{
    #[Route(name: 'app_maquette_index', methods: ['GET'])]
    public function index(MaquetteRepository $maquetteRepository): Response
    {
        return $this->render('maquette/index.html.twig', [
            'maquettes' => $maquetteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_maquette_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $maquette = new Maquette();
        $form = $this->createForm(MaquetteForm::class, $maquette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($maquette);
            $entityManager->flush();

            return $this->redirectToRoute('app_maquette_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('maquette/new.html.twig', [
            'maquette' => $maquette,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_maquette_show', methods: ['GET'])]
    public function show(Maquette $maquette): Response
    {
        return $this->render('maquette/show.html.twig', [
            'maquette' => $maquette,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_maquette_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Maquette $maquette, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MaquetteForm::class, $maquette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_maquette_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('maquette/edit.html.twig', [
            'maquette' => $maquette,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_maquette_delete', methods: ['POST'])]
    public function delete(Request $request, Maquette $maquette, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$maquette->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($maquette);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_maquette_index', [], Response::HTTP_SEE_OTHER);
    }
}
