<?php

namespace App\Controller;

use App\Entity\User;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class SocialController extends Controller
{
    private $cache;
    public function __construct(AdapterInterface $cacheClient)
    {
        $this->cache = $cacheClient;
    }

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
        // $user->toArray();
        // echo '<pre>';
        // var_dump($user->getName());
        // var_dump($user->getId());
        // var_dump($user->getEmail());
        // var_dump($user->toArray());
        // echo '</pre>';
        $valUser = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $user->getId()]);
        // echo '<pre>';
        // var_dump($repository);
        // echo '</pre>';
        // exit();
        if($valUser === null){
            $em = $this->getDoctrine()->getManager();
            $usuario = new User();
            $usuario->setName($user->getName());
            $usuario->setEmail($user->getEmail());
            $usuario->setUsername($user->getId());
            
            $em->persist($usuario);
            $em->flush();
        }else{
            echo "EL usuario existe";
        }
        // do something with all this new power!
        // $user->getFirstName();
        // return new JsonResponse([
        //     'success' => true,
        //     'data'    => $user->toArray()
        // ]); 

        $itemCache = $this->cache->getItem('email');
        $itemCache2 = $this->cache->getItem('name');
        $itemCache3 = $this->cache->getItem('username');
        // $username = 'ruamsa';
        // if (!$itemCache->isHit()) {
        $itemCache->set($user->getEmail());
        $itemCache2->set($user->getName());
        $itemCache3->set($user->getId());
        $this->cache->save($itemCache);
        $this->cache->save($itemCache2);
        $this->cache->save($itemCache3);
        // } else {           
        //     $username = $itemCache->get();
        // }

        $redireccion = new RedirectResponse('/');
        $redireccion->setTargetUrl('http://localhost:8081/social');
        return $redireccion;
    }
}