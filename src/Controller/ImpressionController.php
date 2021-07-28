<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Impression;
use App\Form\ImpressionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImpressionController extends AbstractController
{
    /**
     * @Route("/impression", name="impression")
     */
    public function index(): Response
    {
        return $this->render('impression/index.html.twig', [
            'controller_name' => 'ImpressionController',
        ]);
    }

    /**
     * @Route("/impression/delete/{id}", name="impression_delete")
     */
    public function delete(EntityManagerInterface $manager, Impression $impression)
    {
        $manager->remove($impression);
        $manager->flush();
        return $this->redirectToRoute("film_show", [
            "id" => $impression->getFilm()->getId()
        ]);
    }
}
