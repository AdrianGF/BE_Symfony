<?php

namespace App\Controller;

use App\Entity\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;//showAll - Json

//use Symfony\Component\HttpFoundation\Request;

class ClientController extends AbstractController
{
    /**
     * @Route("/getAll", name="client_all")
     */
    
    public function showAll()
    {
        try {

            $repository = $this->getDoctrine()->getRepository(Client::class);
            $client = $repository->findAll();

            for ($i=0; $i < sizeof($client); $i++) { 
                $data[$i] = [
                    "id" => $client[$i]->getId(),
                    "name" => $client[$i]->getName(),
                    "username" => $client[$i]->getUsername(),
                    "email" => $client[$i]->getEmail()
                ];
            }

            return new JsonResponse([
                'success' => true,
                'data'    => $data
            ]);

        } catch (\Exception $exception) {

            return new JsonResponse([
                'success' => false,
                'code'    => $exception->getCode(),
                'message' => $exception->getMessage(),
            ]);

        }
    }

    /**
     * @Route("/client/{id}", name="client_details")
     */
    public function show($id)
    {
        $repository = $this->getDoctrine()->getRepository(Client::class)->findOneBy(['id' => $id]);

        if (!$repository) {
            throw $this->createNotFoundException(
                'No product found for name '.$id
            );
        }

        $data[] = [
            "name" => $repository->getName(),
            "id" => $repository->getId()
        ];

        return new JsonResponse([
            'success' => true,
            'data'    => $data
        ]);
    }


}
