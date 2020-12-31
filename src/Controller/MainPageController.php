<?php

namespace App\Controller;

use App\Service\TwichCollector\StorageProvider\DbStorageProvider;
use App\Service\TwichCollector\TwichChatCollector;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends AbstractController
{
    /**
     * @var DbStorageProvider
     */
    private $storageProvider;

    public function __construct(DbStorageProvider $storageProvider)
    {
        $this->storageProvider = $storageProvider;
    }

    /**
     * @Route("/", name="main_page")
     */
    public function index(): Response
    {
        (new TwichChatCollector())->collect(837062619, $this->storageProvider);
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MainPageController.php',
        ]);
    }
}
