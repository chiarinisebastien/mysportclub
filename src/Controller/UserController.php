<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasherInterface,
        EntityManagerInterface $entityManager
        ): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plaintextPassword = $user->getPassword();
            $hashedPassword = $userPasswordHasherInterface->hashPassword($user,$plaintextPassword);
            $user->setPassword($hashedPassword);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'User added successfully'
            );

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        User $user, 
        UserPasswordHasherInterface $userPasswordHasherInterface,
        EntityManagerInterface $entityManager
    ): Response
    {
        
        $currentPassword = $user->getPassword();
        $form = $this->createForm(UserType::class, $user, [
            'is_edit'=>true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $plaintextPassword = $user->getPassword();
            
            if ($plaintextPassword != 'Abc!-def123456789!') {
                $hashedPassword = $userPasswordHasherInterface->hashPassword($user,$plaintextPassword);
            }
            else {
                $hashedPassword = $currentPassword;
            }        

            $user->setPassword($hashedPassword);
            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->addFlash(
                'success',
                'User edited successfully'
            );

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
