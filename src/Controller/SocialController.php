<?php
namespace App\Controller;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
//
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\RedirectResponse;

class SocialController extends Controller
{
    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/connect/amazon")
     */
    public function connectAction()
    {
        return $this->get('oauth2.registry')
            ->getClient('amazon')
            ->redirect();
    }
    /**
     * Facebook redirects to back here afterwards
     *
     * @Route("/connect/amazon/check", name="connect_amazon_check")
     */
    public function connectCheckAction(Request $request)
    {
        $client = $this->get('oauth2.registry')
            ->getClient('amazon');
        $user = $client->fetchUser();

        $user->getName();
        $user->getEmail();

        //print_r( $user->getName());

        $s_user = new User();
        $mail_base = $user->getEmail();

        $entityManager = $this->getDoctrine()->getManager();

        $RAW_QUERY = "SELECT COUNT(*) FROM user WHERE `email` = '$mail_base'";
        
        $statement = $entityManager->getConnection()->prepare($RAW_QUERY);
        $statement->execute();
        $count = $statement->fetchAll();
        
        if( $count[0]['COUNT(*)'] != 0 ){
            print("ya esxiste");
        }else{
            $s_user->setName($user->getName());
            $s_user->setEmail($user->getEmail());
            $s_user->setUsername($user->getName());
    
            //print_r($s_user);
    
            $entityManager->persist($s_user);
            $entityManager->flush();
    

        }
        $r_mail = 'social_'.$user->getEmail();

        $redireccion = new RedirectResponse('/');
        $redireccion->setTargetUrl('http://localhost:8081/social/'.$r_mail);
        return $redireccion;

    }
}