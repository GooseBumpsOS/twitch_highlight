<?php

namespace App\Controller;

use App\Service\TwichCollector\TwichChatCollector;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends AbstractController
{
    /**
     * @Route("/", name="main_page")
     */
    public function index(): Response
    {
        dd((new TwichChatCollector())->collect());
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MainPageController.php',
        ]);
    }
}
