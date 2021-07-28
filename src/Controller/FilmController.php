<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Impression;
use App\Entity\User;
use App\Form\FilmType;
use App\Form\ImpressionType;
use App\Repository\FilmRepository;
use App\Repository\ImpressionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class FilmController extends AbstractController
{
    /**
     * @Route("/film", name="film")
     */
    public function index(FilmRepository $filmRepo): Response
    {

        $films = $filmRepo->findAll();


        return $this->render('film/index.html.twig', [
            'controller_name' => 'FilmController',
            'films' => $films
        ]);
    }

    /**
     *
     * @Route("/film/show/{id}", name="film_show", requirements={"id": "\d+"})
     * @Route("/film/impression/edit/{impressionid}", name="impression_edit")
     */

    public function show(Film $film, Impression $impression = null, Request $request, EntityManagerInterface $manager){

            $impressions = $film->getImpressions();
            $modeEdition = true;
            if(!$impression) {
                $impression = new Impression();
                $modeEdition = false;
            }
                if($this->getUser()){
                $form = $this->createForm(ImpressionType::class, $impression);

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $impression->setCreatedAt(new \DateTime());
                    $impression->setFilm($film);
                    $impression->setAuthor($this->getUser());
                    $manager->persist($impression);
                    $manager->flush();
                    $this->redirectToRoute("film_show", [
                        "id" => $film->getId()
                    ]);
                }

            return $this->render('film/show.html.twig', [
                'film' => $film,
                'formImpression' => $form->createView(),
                'impressions' => $impressions
            ]);

            }
            return $this->render('film/show.html.twig', [
                'film' => $film,
                'impressions' => $impressions
            ]);
        }


    /**
     *
     * @Route("/film/new", name="film_new")
     * @Route("/film/edit/{id}", name="film_edit")
     */

    public function create(Film $film = null, EntityManagerInterface $manager, Request $request, UserInterface $user){

        $modeEdition = true;

        if(!$film){
            $film = new Film();
            $modeEdition = false;
        }
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){
            $film->setAuthor($user);
            $manager->persist($film);
            $manager->flush();
            return $this->redirectToRoute("film_show", [
                'id' => $film->getId()
            ]);
        }

        return $this->render('film/new.html.twig', [
            'formFilm' => $form->createView(),
            'edition' => $modeEdition
        ]);
    }

    /**
     * @Route("/film/delete/{id}", name="film_delete")
     */
    public function delete(EntityManagerInterface $manager, Film $film, UserInterface $user){

        if($film->getAuthor() == $user) {
            $manager->remove($film);
            $manager->flush();
        }
        return $this->redirectToRoute("film");

    }
}
