<?php

namespace App\Controller\Api;

use App\Entity\Poi;
use App\Repository\PoiRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController extends AbstractController
{
    // Fonction pour récupérer les POI
    #[Route('/api/pois', name: 'api_pois', methods: ['GET'])]
    public function getPois(PoiRepository $poiRepository): JsonResponse
    {
        $pois = $poiRepository->findAll();
        $data = [];

        foreach ($pois as $poi) {
            $data[] = [
                'id' => $poi->getId(),
                'name' => $poi->getName(),
                'lat' => $poi->getLat(),
                'long' => $poi->getLongitude(),
                'description' => $poi->getDescription(),
                'teaser' => $poi->getTeaser(),
                'picture' => $poi->getPicture()
            ];
        }

        return $this->json($data);
    }
    // Fonction pour ajouter un POI & validation des données avec ValidatorInterface
    #[Route('/api/pois/create', name: 'api_create_poi', methods: ['POST'])]
    public function createPoi(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $data = json_decode($request->getContent(), true);

        $poi = new Poi();
        $poi->setName($data['name']);
        $poi->setDescription($data['description']);
        $poi->setTeaser($data['teaser']);
        $poi->setLat($data['lat']);
        $poi->setLongitude($data['long']);
        $poi->setPicture($data['picture'] ?? 'images/marker-icon.png');

        $errors = $validator->validate($poi);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['success' => false, 'errors' => $errorMessages]);
        }

        try {
            $entityManager->persist($poi);
            $entityManager->flush();
            return $this->json(['success' => true, 'message' => 'POI added successfully']);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => 'Failed to add POI: ' . $e->getMessage()]);
        }
    }

    // Fonction pour récupérer les images dans le select de l'ajout de POI
    #[Route('/api/images', name: 'api_images')]
    public function getImages(): JsonResponse
    {
        $imageDirectory = $this->getParameter('kernel.project_dir') . '/public/images/';
        $images = array_diff(scandir($imageDirectory), array('..', '.'));
        $images = array_values($images); // Réindexe le tableau pour assurer que c'est une liste

        return $this->json($images);
    }


}
