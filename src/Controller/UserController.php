<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use App\Entity\Follow;
use Symfony\Component\HttpFoundation\Response;
//
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(Request $request)
    {
        return "W.I.P";
    }

    /**
     * @Route("/social_log", name="social_log")
     */
    public function social_log(Request $request)
    {
        $content = $request->getContent();
        $data = json_decode($content);
        $mail = $data->mail;

        $em = $this->getDoctrine()->getManager();

        $RAW_QUERY = "SELECT username, `name`, email FROM user WHERE email = '$mail'";
        
        $statement = $em->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $data_user = $statement->fetchAll();
        
        // print_r($data_user);

        
        return new JsonResponse([
            'data' => $data_user
            ]);
    }

    /**
     * @Route("/test_get", name="test_get")
     */
    public function test(Request $request)
    {              
        // $testy = $request->headers->has('Authorization');  
        
        
        print_r($request->headers->all());
        

        // return new Response(
        //     'Token: '.$this->getUser()->getToken()
        // );

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
