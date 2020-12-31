<?php

declare(strict_types=1);


namespace App\Controller\Api;

use App\Service\Bid\BidManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BidController extends AbstractController
{
    /**
     * @var BidManager
     */
    private $bidManager;

    /**
     * BidController constructor.
     * @param BidManager $bidManager
     */
    public function __construct(BidManager $bidManager)
    {
        $this->bidManager = $bidManager;
    }

    /**
     * @Route("/api/new-work/", name="send_to_queue", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function sendToQueue(Request $request): JsonResponse
    {
        $this->bidManager->sendBid(
            $request->request->getInt('videoId'),
            (float)$request->request->get('coeff'),
            $request->request->get('keywords'),
            $request->request->get('user')
        );

        return $this->json('ok');
    }
}