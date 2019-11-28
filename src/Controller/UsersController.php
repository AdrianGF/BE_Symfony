<?php

namespace App\Controller;

use App\Entity\Users;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;//showAll - Json

class UsersController extends AbstractController
{
    /**
     * @Route("/users", name="users")
     */
    public function create_users()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = new Users();
        $user->setName('Keyboard');
        $user->setCif(1999);

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($user);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new user with id '.$user->getId());
    }

    /**
     * @Route("/getAll", name="user_show")
     */
    
    public function showAll()
    {
        try {

            $repository = $this->getDoctrine()->getRepository(Users::class);
            $user = $repository->findAll();

            for ($i=0; $i < sizeof($user); $i++) { 
                $data[$i] = [
                    "id" => $user[$i]->getId(),
                    "name" => $user[$i]->getName(),
                    "cif" => $user[$i]->getCif(),
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
     * @Route("/user/{id}", name="client_show")
     */
    public function show($id)
    {
        $repository = $this->getDoctrine()->getRepository(Users::class)->findOneBy(['id' => $id]);

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
            'data'    => $data // Your data here
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
