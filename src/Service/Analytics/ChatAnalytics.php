<?php

declare(strict_types=1);


namespace App\Service\Analytics;

use App\Entity\Chat;
use App\Service\Analytics\Dto\AnalyticsResult;

class ChatAnalytics
{
    private const SECONDS_IN_MINUTE = 60;

    /**
     * @param float $volume this show how graph should change(strong of changes)
     * @param string $keywords
     * @param Chat[] $chat
     * @return AnalyticsResult
     */
    public function makeAnalytics(float $volume, string $keywords, array $chat): AnalyticsResult
    {
        $result = new AnalyticsResult();

        $keywordsSep = preg_split("/[,;]/m", $keywords);

        $keywordsSep = array_map(static function ($item) {
            return trim($item);
        }, $keywordsSep);

        $segregatedComments = $this->segregateCommentsPerMinute($chat);

        $result->chatActivity = $this->getCountCommentsPerMinute($segregatedComments);

        $result->chatAnalyseByCriteria = $this->countKeywordsPerMinutes($segregatedComments, $keywordsSep);

        $result->highLiteOffsets = $this->getHighlightOffsets($result->chatAnalyseByCriteria, $volume);

        return $result;
    }

    /**
     * @param Chat[] $chat
     * @return array
     */
    private function segregateCommentsPerMinute(array $chat): array
    {
        $offsetToMin = static function (int $offset) {
            return intdiv($offset, self::SECONDS_IN_MINUTE);
        };

        $result = [];
        $currentMin = 0;
        foreach ($chat as $chatItem) {
            if ($currentMin + 1 === $offsetToMin($chatItem->getTimeOffset())) {
                $currentMin++;
            } elseif ($offsetToMin($chatItem->getTimeOffset()) > $currentMin + 1) {
                $nextMin = $offsetToMin($chatItem->getTimeOffset());
                foreach (range($currentMin, $nextMin) as $min) {
                    $result[$min] = [];
                }
                $currentMin = $nextMin;
            }

            $result[$currentMin][] = $chatItem->getMsg();
        }

        return $result;
    }

    /**
     * @param array $segregatedComments
     * @return array
     */
    private function getCountCommentsPerMinute(array $segregatedComments): array
    {
        $result = array_fill(0, count($segregatedComments), 0);

        foreach ($segregatedComments as $minute => $commentsPerMinute) {
            $result[$minute] = count($commentsPerMinute);
        }

        return $result;
    }

    /**
     * @param array $segregatedComments
     * @param array $keywords
     * @return array
     */
    private function countKeywordsPerMinutes(array $segregatedComments, array $keywords): array
    {
        $result = array_fill(0, count($segregatedComments), 0);

        foreach ($segregatedComments as $currentMinute => $commentPerMinute) {
            foreach ($commentPerMinute as $comment) {
                if ($this->isInText($comment, $keywords)) {
                    $result[$currentMinute]++;
                }
            }
        }

        return $result;
    }

    /**
     * @param string $text
     * @param array $keywords
     * @return bool
     */
    private function isInText(string $text, array $keywords): bool
    {
        $result = false;

        $text = strtolower($text);
        foreach ($keywords as $keyword) {
            $result = $result || in_array(strtolower($keyword), preg_split('/[,\s\.\;]/m', $text), true);
        }

        return $result;
    }

    /**
     * @param array $keywordsPerMinutes
     * @param float $volume
     * @return array
     */
    private function getHighlightOffsets(array $keywordsPerMinutes, float $volume): array
    {
        $result = [];
        $lastMinute = count($keywordsPerMinutes) - 1;
        foreach ($keywordsPerMinutes as $minute => $keywordsPerMinute) {
            if ($minute !== 0 && $minute !== $lastMinute &&
                $keywordsPerMinutes[$minute - 1] * $volume < $keywordsPerMinutes[$minute] && $keywordsPerMinutes[$minute + 1] * $volume < $keywordsPerMinutes[$minute]) {
                $result[] = $minute * self::SECONDS_IN_MINUTE;
            }
        }

        return $result;
    }
}