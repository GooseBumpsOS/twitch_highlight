<?php

declare(strict_types=1);


namespace App\Controller\Api;


use App\Service\TwichCollector\TwichRequesterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ValidateController extends AbstractController
{
    /**
     * @var TwichRequesterService
     */
    private $twichRequester;

    public function __construct(TwichRequesterService $twichRequester)
    {
        $this->twichRequester = $twichRequester;
    }

    /**
     * @Route("/api/validate/video_exist/{videoId}", name="is_video_exist", methods={"GET"})
     * @param int $videoId
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function isVideoExist(int $videoId): JsonResponse
    {
        $isExist = true;
        if (!$this->twichRequester->isVideoExist($videoId)) {
            $isExist = false;
        }

        return $this->json($isExist);
    }
}