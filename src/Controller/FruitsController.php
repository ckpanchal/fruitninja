<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Fruit;

class FruitsController extends AbstractController
{
    #[Route('/', name: 'app_fruits')]
    public function index(EntityManagerInterface $em): Response
    {
        $fruits = $em->getRepository(Fruit::class)->findAll();
        return $this->render('fruits/index.html.twig', [
            'fruits' => $fruits,
        ]);
    }

    #[Route('/favourite-fruits', name: 'app_favourite_fruits')]
    public function favouriteFruits(EntityManagerInterface $em): Response
    {
        $fruits = $em->getRepository(Fruit::class)->getFavouriteFruits();
        return $this->render('fruits/favourite.html.twig', [
            'fruits' => $fruits,
        ]);
    }

    #[Route('/add-remove-favourite/{id}', name: 'app_add_remove_favourite')]
    public function addRemoveFavourite(EntityManagerInterface $em, $id): Response
    {
        $fruit = $em->getRepository(Fruit::class)->find($id);
        if ($fruit && $fruit->getIsFavourite() == 1) {
            $fruit->setIsFavourite(false);
            $message = $fruit->getName() . ' removed from favourite.';
        } else {

            // Check favourite fruits should not more than 10
            $favouriteFruits = $em->getRepository(Fruit::class)->getFavouriteFruits();
            if ($favouriteFruits && count($favouriteFruits) == 10) {
                return new JsonResponse([
                    'status'    => false,
                    'message'   => 'You can add a maximum of 10 fruits to your favorites.'
                ]);    
            }

            $fruit->setIsFavourite(true);
            $message = $fruit->getName() . ' added to favourite.';
        }
        $em->persist($fruit);
        $em->flush();
        return new JsonResponse([
            'status'    => true,
            'message'   => $message
        ]);
    }
}
