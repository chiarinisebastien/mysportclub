<?php

namespace App\Controller;

use App\Entity\Cheat;
use App\Form\CheatType;
use App\Repository\CheatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cheat')]
class CheatController extends AbstractController
{
    #[Route('/', name: 'app_cheat_index', methods: ['GET'])]
    public function index(CheatRepository $cheatRepository): Response
    {
        return $this->render('cheat/index.html.twig', [
            'cheats' => $cheatRepository->findby([],['id'=>'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_cheat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cheat = new Cheat();
        $form = $this->createForm(CheatType::class, $cheat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cheat);
            $entityManager->flush();

            return $this->redirectToRoute('app_cheat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cheat/new.html.twig', [
            'cheat' => $cheat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cheat_show', methods: ['GET'])]
    public function show(Cheat $cheat): Response
    {
        return $this->render('cheat/show.html.twig', [
            'cheat' => $cheat,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cheat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cheat $cheat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CheatType::class, $cheat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cheat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cheat/edit.html.twig', [
            'cheat' => $cheat,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cheat_delete', methods: ['POST'])]
    public function delete(Request $request, Cheat $cheat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cheat->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cheat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cheat_index', [], Response::HTTP_SEE_OTHER);
    }
}
