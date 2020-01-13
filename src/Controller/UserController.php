<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use App\Entity\Follow;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/test_get", name="test_get")
     */
    public function test()
    {                   
        $token = $this->getUser()->getToken();
        print_r($token);
    }

    /**
     * @Route("/follow/{id}", name="follow")
     */
    public function follow($id): Response
    {

        $token = $this->getUser()->getToken();
        $follower = $this->getDoctrine()->getRepository(User::class)->findOneBy(['token' => $token]);
        $followed = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $id]);
        

        $idr = $follower->getId();//"id_follower" => $idr
        $idd = $followed->getId();//"id_followed" => $idd

        if ((!$follower) && (!$followed) && ( $idr != $idd ) ) {
            throw $this->createNotFoundException(
                'No user found for name '.$idr
            );
        }else{
            $em = $this->getDoctrine()->getManager();

            $RAW_QUERY = "SELECT COUNT(*) FROM follow WHERE follwer = '$idr' AND followed = '$idd'";
            
            $statement = $em->getConnection()->prepare($RAW_QUERY);
            $statement->execute();
    
            $count = $statement->fetchAll();

            if( $count[0]['COUNT(*)'] >= 1 ){
                throw $this->createNotFoundException(
                    'The user is currently followed'
                );
            }else{
                $follow = new Follow();

                $follow->setFollower($idr);
                $follow->setFollowed($idd);
    
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($follow);
                $entityManager->flush();
    
                return new Response(
                    'Saved new follow with id: '.$follower->getId()
                    .' and new user with id: '.$followed->getId()
                );
            }
        }
    }

    /**
     * @Route("/unfollow/{id}", name="unollow")
     */
    public function unfollow($id): Response
    {
        $token = $this->getUser()->getToken();
        $follower = $this->getDoctrine()->getRepository(User::class)->findOneBy(['token' => $token]);
        $followed = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $id]);
        

        $idr = $follower->getId();//"id_follower" => $idr
        $idd = $followed->getId();//"id_followed" => $idd

        if ((!$follower) && (!$followed) && ( $idr != $idd ) ) {
            throw $this->createNotFoundException(
                'No user found for name '.$idr
            );
        }else{

            $em = $this->getDoctrine()->getManager();

            $RAW_QUERY = "SELECT COUNT(*) FROM follow WHERE follwer = '$idr' AND followed = '$idd'";
            
            $statement = $em->getConnection()->prepare($RAW_QUERY);
            $statement->execute();
    
            $count = $statement->fetchAll();

            if( $count[0]['COUNT(*)'] < 1 ){
                throw $this->createNotFoundException(
                    'You cant unfollow the user (not followed yet)'
                );
            }else{

                $RAW_QUERY = "SELECT id FROM follow WHERE follwer = '$idr' AND followed = '$idd'";
            
                $statement = $em->getConnection()->prepare($RAW_QUERY);
                $statement->execute();
        
                $id_follow = $statement->fetchAll();
                $id_follow = $id_follow[0]['id'];
                
                $unfollow = new Follow();
                $follow_data = $this->getDoctrine()->getRepository(Follow::class)->findOneBy(['id' => $id_follow]);

                $unfollow->setUnfollow($follow_data->getId());
                $entity = $em->merge($unfollow);  
                $em->remove($entity);
                $em->flush();
                
                return new Response(
                    'Deleted follower: '.$follower->getId()
                    .' to followed: '.$followed->getId()
                );
            }
        }
    }
}
