<?php

declare(strict_types=1);


namespace App\Controller\Api;


use App\Entity\Chat;
use App\Entity\ChatDictionary;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    /**
     * @Route("/api/chat/{videoId}", name="get_chat_messages", methods={"GET"})
     * @param int $videoId
     * @return JsonResponse
     */
    public function getChatMessages(int $videoId): JsonResponse
    {
        $chatDic = $this->getDoctrine()->getManager()->getRepository(ChatDictionary::class)->findOneBy([
            'videoId' => $videoId
        ]);

        $result = null;
        if (!is_null($chatDic)) {
            /** @var Chat $chatItem */
            foreach ($chatDic->getChats()->getValues() as $chatItem) {
                $result['msg'][] = $chatItem->getMsg();
                $result['offset'][] = $chatItem->getTimeOffset();
            }
        }

        return $this->json($result);
    }
}