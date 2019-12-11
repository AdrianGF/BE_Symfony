<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
        echo '<pre>';
        var_dump($user->getName());
        var_dump($user->getId());
        var_dump($user->getEmail());
        var_dump($user->toArray());
        echo '</pre>';
        exit();
        // do something with all this new power!
        $user->getFirstName();
    }
}