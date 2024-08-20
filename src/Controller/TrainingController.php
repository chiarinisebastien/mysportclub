<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Training;
use App\Entity\TrainingAttendance;
use App\Form\TrainingType;
use App\Repository\PlayerRepository;
use App\Repository\TrainingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/training')]
class TrainingController extends AbstractController
{
    #[Route('/', name: 'app_training_index', methods: ['GET'])]
    public function index(TrainingRepository $trainingRepository): Response
    {
        return $this->render('training/index.html.twig', [
            'trainings' => $trainingRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_training_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $training = new Training();
        $form = $this->createForm(TrainingType::class, $training);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $startedAt = $training->getStartedAt();
            $endedAt = $training->getEndedAt();
            $trainingDays = $training->getTrainingDay(); 
            
            $currentDate = clone $startedAt;
            
            while ($currentDate <= $endedAt) {
                $dayOfWeek = (int)$currentDate->format('N'); 
            
                if (in_array($dayOfWeek, $trainingDays)) {
                    $newTraining = new Training();
                    $newTraining->setStartedAt($startedAt);
                    $newTraining->setEndedAt($endedAt);
        
                    $trainingAtDate = clone $currentDate;
                    $newTraining->setTrainingAt($trainingAtDate);
                    
                    $newTraining->setTrainingHour($training->getTrainingHour());
                    $newTraining->setCategory($training->getCategory());
                    $newTraining->setTrainingDay($training->getTrainingDay());
            
                    $entityManager->persist($newTraining);
                }
            
                $currentDate->modify('+1 day');
            }
            
            $entityManager->flush();
            
            return $this->redirectToRoute('app_training_index', [], Response::HTTP_SEE_OTHER);
        }
        
        
        

        return $this->render('training/new.html.twig', [
            'training' => $training,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_training_show', methods: ['GET'])]
    public function show(Training $training): Response
    {
        return $this->render('training/show.html.twig', [
            'training' => $training,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_training_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Training $training, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TrainingType::class, $training, [
            'is_edit' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_training_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('training/edit.html.twig', [
            'training' => $training,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_training_delete', methods: ['POST'])]
    public function delete(Request $request, Training $training, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$training->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($training);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_training_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{playerId}/{present}/{trainingId}', name: 'app_training_attendance', methods: ['GET'])]
    public function app_training_attendance(
        Player $playerId,
        $present = 0,
        Training $trainingId,
        PlayerRepository $playerRepository,
        TrainingRepository $trainingRepository,
        EntityManagerInterface $entityManager
        ): Response
    {
        $trainingAttendance = new TrainingAttendance();
        $trainingAttendance->setPlayer($playerId);
        $trainingAttendance->setTraining($trainingId);
        $trainingAttendance->setPresent($present);
        $entityManager->persist($trainingAttendance);
        $entityManager->flush();

        return $this->redirectToRoute('app_player_myaccount', [], Response::HTTP_SEE_OTHER);
    }
}
