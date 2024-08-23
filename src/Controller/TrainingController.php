<?php

namespace App\Controller;

use App\Entity\Cheat;
use App\Entity\Player;
use App\Entity\Category;
use App\Entity\Training;
use App\Form\TrainingType;
use App\Entity\TrainingAttendance;
use App\Repository\PlayerRepository;
use App\Repository\CategoryRepository;
use App\Repository\TrainingAttendanceRepository;
use App\Repository\TrainingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/training')]
class TrainingController extends AbstractController
{

    #[Route('/', name: 'app_training_index', methods: ['GET'])]
    public function index(
        TrainingRepository $trainingRepository,
        CategoryRepository $categoryRepository,
    ): Response
    {
        $user = $this->getUser(); 
        $trainings = $trainingRepository->findByUserCategories($user);
        return $this->render('training/index.html.twig', [
            'trainings' => $trainings,
            'categories' => $categoryRepository->findByUser($user),
            'title'=>'All Trainings',
            'nextTraining' => '',
        ]);
    }

    #[Route('/{category}', name: 'app_training_category', methods: ['GET'])]
    public function app_training_category(
        TrainingRepository $trainingRepository,
        CategoryRepository $categoryRepository,
        Category $category,
        EntityManagerInterface $entityManager,
        TrainingAttendanceRepository $trainingAttendanceRepository,
    ): Response {
        $controlUser = $category->getUsers()->toArray();
        $currentUser = $this->getUser();
        
        if (!in_array($currentUser, $controlUser)) {
            $cheat = new Cheat();
            $cheat->setUser($currentUser);
            $cheat->setMessage('Attempts to reach the training facilities of a category to which he does not have access');
            $entityManager->persist($cheat);
            $entityManager->flush();
            return $this->redirectToRoute('app_cheater', [], Response::HTTP_SEE_OTHER);
        }
    
        $user = $this->getUser(); 
        $trainings = $trainingRepository->findBy(['category' => $category]);
        $nextTraining = $trainingRepository->findNextTrainingByCategory($category);
        
        $allPlayers = $category->getPlayers();
        
        $attendances = $trainingAttendanceRepository->findBy(['training' => $nextTraining->getId()]);
        
        $attendanceMap = [];
        foreach ($attendances as $attendance) {
            $attendanceMap[$attendance->getPlayer()->getId()] = $attendance;
        }
        
        $playersWithAttendance = [];
        foreach ($allPlayers as $player) {
            if (isset($attendanceMap[$player->getId()])) {
                $playersWithAttendance[] = $attendanceMap[$player->getId()];
            } else {
                $fakeAttendance = new TrainingAttendance();
                $fakeAttendance->setPlayer($player);
                $fakeAttendance->setPresent(-1); 
                $playersWithAttendance[] = $fakeAttendance;
            }
        }
        
    
        return $this->render('training/index.html.twig', [
            'trainings' => $trainings,
            'nextTraining' => $nextTraining,
            'categories' => $categoryRepository->findByUser($user),
            'title' => $category->getTitle(),
            'players' => $playersWithAttendance,
        ]);
    }

    #[Route('/add/new', name: 'app_training_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository
        ): Response
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
            'categories' => $categoryRepository->findByUser($this->getUser()),
        ]);
    }

    // #[Route('/{id}', name: 'app_training_show', methods: ['GET'])]
    // public function show(Training $training): Response
    // {
    //     return $this->render('training/show.html.twig', [
    //         'training' => $training,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'app_training_edit', methods: ['GET', 'POST'])]
    public function edit(
            Request $request, 
            Training $training, 
            EntityManagerInterface $entityManager,
            CategoryRepository $categoryRepository
        ): Response
    {
        $category = $training->getCategory();
        $controlUser = $category->getUsers()->toArray();
        $currentUser = $this->getUser();
        
        if (!in_array($currentUser, $controlUser)) {
            $cheat = new Cheat();
            $cheat->setUser($currentUser);
            $cheat->SetMessage('Attempts to edit the training facilities of a category to which he does not have access');
            $entityManager->persist($cheat);
            $entityManager->flush();
            return $this->redirectToRoute('app_cheater', [], Response::HTTP_SEE_OTHER);
        }

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
            'categories' => $categoryRepository->findByUser($this->getUser()),
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
        EntityManagerInterface $entityManager,
        TrainingAttendanceRepository $trainingAttendanceRepository
        ): Response
    {
        $controlUser = $playerId->getUsers()->toArray();
        $currentUser = $this->getUser();
        
        if (!in_array($currentUser, $controlUser)) {
            $cheat = new Cheat();
            $cheat->setUser($currentUser);
            $cheat->SetMessage('Attempts to change the presence of a player to whom he does not have access');
            $entityManager->persist($cheat);
            $entityManager->flush();
            return $this->redirectToRoute('app_cheater', [], Response::HTTP_SEE_OTHER);
        }

        $previousAttendance = $trainingAttendanceRepository->findBy(['training'=>$trainingId, 'player'=>$playerId]);
        if ($previousAttendance) {
            foreach ($previousAttendance as $attendance) {
                $entityManager->remove($attendance);
            }
            $entityManager->flush();
        }

        $trainingAttendance = new TrainingAttendance();
        $trainingAttendance->setPlayer($playerId);
        $trainingAttendance->setTraining($trainingId);
        $trainingAttendance->setPresent($present);
        $entityManager->persist($trainingAttendance);
        $entityManager->flush();

        return $this->redirectToRoute('app_player_myaccount', [], Response::HTTP_SEE_OTHER);
    }

    
    #[Route('/training/delete/all/training', name: 'app_training_delete_all_training', methods: ['POST', 'GET'])]
    public function app_training_delete_all_training(
        EntityManagerInterface $entityManager,
        TrainingRepository $trainingRepository
        ): Response
    {
        $trainings = $trainingRepository->findAll();

        foreach ($trainings as $training) {
    
            $entityManager->remove($training);
        }
    
        $entityManager->flush();
    
        return $this->redirectToRoute('app_training_index', [], Response::HTTP_SEE_OTHER);
    }
}
