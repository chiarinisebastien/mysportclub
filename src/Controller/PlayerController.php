<?php

namespace App\Controller;

use App\Entity\Player;
use App\Repository\CategoryRepository;
use Faker\Factory;
use App\Form\PlayerType;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/player')]
class PlayerController extends AbstractController
{
    #[Route('/', name: 'app_player_index', methods: ['GET'])]
    public function index(PlayerRepository $playerRepository): Response
    {
        return $this->render('player/index.html.twig', [
            'players' => $playerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_player_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $player = new Player();
        $form = $this->createForm(PlayerType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($player);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Player added successfully'
            );

            return $this->redirectToRoute('app_player_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('player/new.html.twig', [
            'player' => $player,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_player_show', methods: ['GET'])]
    public function show(Player $player): Response
    {
        return $this->render('player/show.html.twig', [
            'player' => $player,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_player_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Player $player, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlayerType::class, $player, [
            'is_edit'=>true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Player edited successfully'
            );

            return $this->redirectToRoute('app_player_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('player/edit.html.twig', [
            'player' => $player,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_player_delete', methods: ['POST'])]
    public function delete(Request $request, Player $player, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$player->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($player);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_player_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/delete/all/player', name: 'app_player_delete_all_player', methods: ['POST', 'GET'])]
    public function app_player_delete_all_player(
        EntityManagerInterface $entityManager,
        PlayerRepository $playerRepository
        ): Response
    {
        $players = $playerRepository->findAll();

        foreach ($players as $player) {
            foreach ($player->getCategory() as $category) {
                $player->removeCategory($category);
            }
            $entityManager->remove($player);
        }
        $entityManager->flush();

        return $this->redirectToRoute('app_player_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/player/generate', name: 'app_player_generate', methods: ['GET'])]
    public function generate(
            PlayerRepository $playersRepository
            , EntityManagerInterface $entityManager
            , CategoryRepository $categoryRepository
        ): Response
    {
        
        $categoryCount = $categoryRepository->count([]);

        for ($i = 0 ; $i < 300 ; $i++) {
            $player = new Player();
            $faker = Factory::create('fr_FR');
            $firstname = $faker->firstname();
            $lastname = $faker->lastName();
            $categoryId = rand(1, $categoryCount);

            $category = $categoryRepository->findOneBy(['id'=>$categoryId]); 

            if ($category === null) {
                error_log("Group with ID $categoryId not found.");
            } else {
                error_log("Found group: " . $category->getTitle());
                $title = $category->getTitle();
                preg_match('/\d+/', $title, $matches);
                $age = $matches[0];
                $rangeMin = $age - 1;
                $rangeMax = $age + 1;
                $birthdate = $faker->dateTimeBetween('-'.$rangeMax.' years', '-'.$rangeMin. 'years');
                $player->addCategory($category);
            }



            $player->setFirstname($firstname);
            $player->setLastname($lastname);
            $player->setBirthdate($birthdate);

            $entityManager->persist($player);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_player_index', [], Response::HTTP_SEE_OTHER);
    }
}
