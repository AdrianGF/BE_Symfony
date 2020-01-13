<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class RedisController extends AbstractController
{
    private $cache;
    public function __construct(AdapterInterface $cacheClient)
    {
        $this->cache = $cacheClient;
    }
    /**
     * @Route("/save/redis", name="save_data_social")
     */
    public function index()
    {
        $itemCache = $this->cache->getItem('email');
        $itemCache2 = $this->cache->getItem('name');
        $itemCache3 = $this->cache->getItem('username');
        // $username = 'ruamsa';
        // if (!$itemCache->isHit()) {
            // $itemCache->set('ruamsa');
            // $this->cache->save($itemCache);
        // } else {           
        $username = $itemCache->get();
        $data = [
            "email" => $itemCache->get(),
            "name" => $itemCache2->get(),
            "username" => $itemCache3->get()
        ];
        // }
        return new JsonResponse([
            'success' => true,
            'data'    => $data
        ]); 
    }
}