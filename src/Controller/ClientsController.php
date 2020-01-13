<?php

namespace App\Controller;

use App\Entity\Clients;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;//showAll - Json

class ClientsController extends AbstractController
{
    /**
     * @Route("/clients", name="clients")
     */
    public function create_clients()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $client = new CLients();
        $client->setName('Keyboard');

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($client);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new user with id '.$client->getId());
    }

    /**
     * @Route("/getAll", name="user_show")
     */
    
    public function showAll()
    {   
        $test = array();

        try {
            $repository = $this->getDoctrine()->getRepository(Clients::class);
            $client = $repository->findAll();
            
            for ($i=0; $i < sizeof($client); $i++) { 
                $data[$i] = [
                    "name" => $client[$i]->getName(),
                    "username" => $client[$i]->getUsername(),
                    "email" => $client[$i]->getEmail()
                ];
            }
            
            return new JsonResponse([
                'success' => true,
                'data'    => $data // Your data here
            ]);
    
        } catch (\Exception $exception) {
    
            return new JsonResponse([
                'success' => false,
                'code'    => $exception->getCode(),
                'message' => $exception->getMessage(),
            ]);
    
        }
        //return new Response('Check out this great user: '.$user->getName());
    }

     /**
     * @Route("/client/{username}", name="client_show")
     */
    public function show($username)
    {
        // echo $username;
        $repository = $this->getDoctrine()->getRepository(Clients::class)->findOneBy(['username' => $username]);

        if (!$repository) {
            throw $this->createNotFoundException(
                'No product found for name '.$username
            );
        }

        $data[] = [
            "name" => $repository->getName(),
            "username" => $repository->getUsername(),
            "email" => $repository->getEmail()
        ];

        return new JsonResponse([
            'success' => true,
            'data'    => $data
        ]);

        // or render a template
        // in the template, print things with {{ product.name }}
        // return $this->render('product/show.html.twig', ['product' => $product]);
    }


    /*public function showAll()
    {

        $user= $this->getDoctrine()->getRepository(Users::class)->findAll();
        return $this->render('users/index.html.twig', array('user' => $user));
    }*/

}
