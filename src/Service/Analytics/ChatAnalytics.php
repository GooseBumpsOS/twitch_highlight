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
        $chatTmp = 0;
        $humorCount = [];
        $tmp = 0;
        $prevMin = 0;

        foreach ($chat as $chatMsg) {
            if (intdiv($chatMsg->getTimeOffset(), 60) === $prevMin) {
                $chatTmp++;
                if ($this->isInText($chatMsg->getMsg(), $keywordsSep)) {
                    $tmp++;
                }
            } else {
                for ($i = 0; $i < intdiv($chatMsg->getTimeOffset(), 60) - $prevMin - 1; $i++) {
                    $humorCount[] = 0;
                    $chatSerial[] = 0;
                }

                $humorCount[] = $tmp;
                $chatSerial[] = $chatTmp;
                $tmp = 0;
                $chatTmp = 0;
                $prevMin = intdiv($chatMsg->getTimeOffset(), 60);
            }
        }

        $percentDiff = $volume;
        $highLights = [];

        foreach ($humorCount as $key => $chatMsg) {
            if (($key < count($humorCount) - 2) && $humorCount[$key] * $percentDiff < $humorCount[$key + 1] && $humorCount[$key + 1] * $percentDiff >= $humorCount[$key + 2] && $humorCount[$key] !== 0) {
                $highLights[] = ($key + 1);
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