<?php

declare(strict_types=1);


namespace App\Service\Analytics;

use App\Entity\Chat;
use App\Service\Analytics\Dto\AnalyticsResult;

class ChatAnalytics
{
    /**
     * @param float $volume this show how graph should change(strong of changes)
     * @param string $keywords
     * @param Chat[] $chat
     * @return AnalyticsResult
     */
    public function makeAnalytics(float $volume, string $keywords, array $chat): AnalyticsResult
    {
        $result = new AnalyticsResult();

        $keywords = preg_replace("/[,;]/m", "|", $keywords);
        $keywordsSep = explode("|", $keywords);

        $keywordsSep = array_map(static function ($item) {
            return trim($item);
        }, $keywordsSep);

        $chatSerial = [];
        $words = 0;
        $humorCount = [];
        $keywordsPerMinute = 0;
        $prevMin = 0;

        foreach ($chat as $chatMsg) {
            if (intdiv($chatMsg->getTimeOffset(), 60) === $prevMin) {
                $words++;
                if ($this->isInText($chatMsg->getMsg(), $keywordsSep)) {
                    $keywordsPerMinute++;
                }
            } else {
                for ($i = 0; $i < intdiv($chatMsg->getTimeOffset(), 60) - $prevMin - 1; $i++) {
                    $humorCount[] = 0;
                    $chatSerial[] = 0;
                }

                $humorCount[] = $keywordsPerMinute;
                $chatSerial[] = $words;
                $keywordsPerMinute = 0;
                $words = 0;
                $prevMin = intdiv($chatMsg->getTimeOffset(), 60);
            }
        }

        $percentDiff = $volume;
        $highLights = [];

        foreach ($humorCount as $minute => $chatMsg) {
            if (($minute < count($humorCount) - 2) && $humorCount[$minute] * $percentDiff < $humorCount[$minute + 1] && $humorCount[$minute + 1] * $percentDiff >= $humorCount[$minute + 2] && $humorCount[$minute] !== 0) {
                $highLights[] = ($minute + 1) * 60;
            }
        }

        $result->chatActivity = $chatSerial;
        $result->chatAnalyseByCriteria = $humorCount;
        $result->highLiteOffset = $highLights;

        return $result;
    }

    /**
     * @param string $text
     * @param array $find
     * @return bool
     */
    private function isInText(string $text, array $find): bool
    {
        foreach ($find as $item) {
            if (stripos(strtolower($text), strtolower($item)) !== false) {
                return true;
            }
        }

        return false;
    }
}