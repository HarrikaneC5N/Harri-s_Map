<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PoiController extends AbstractController
{
    #[Route('/poi', name: 'poi')]
    public function index(): Response
    {
        return $this->render('poi/index.html.twig', [
            'controller_name' => 'PoiController',
        ]);
    }
    #[Route('/poi/create', name: 'poi_create')]
    public function create(): Response
    {
        return $this->render('poi/index.html.twig', [
            'controller_name' => 'PoiController',
        ]);
    }

    #[Route('/poi/show', name: 'poi_show')]
    public function show(): Response
    {
        return $this->render('poi/show.html.twig', [
            'controller_name' => 'PoiController',
        ]);
    }

    #[Route('/poi/update', name: 'poi_update')]
    public function update(): Response
    {
        return $this->render('poi/update.html.twig', [
            'controller_name' => 'PoiController',
        ]);
    }

    #[Route('/poi/delete', name: 'poi_delete')]
    public function delete(): Response
    {
        $this->addFlash('success', 'POI deleted successfully');
        return $this->render('poi/delete.html.twig', [
            'controller_name' => 'PoiController',
        ]);
    }
}
