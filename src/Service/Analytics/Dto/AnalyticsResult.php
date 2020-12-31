<?php

declare(strict_types=1);


namespace App\Service\Analytics\Dto;


use JsonSerializable;

class AnalyticsResult implements JsonSerializable
{
    /** @var int[] show how many message per minute */
    public $chatActivity;

    /** @var int[] */
    public $highLiteOffset;

    /** @var int[] show how manny keywords was per minute */
    public $chatAnalyseByCriteria;

    public function jsonSerialize(): array
    {
        return [
            'chatActivity' => [
                'values' => $this->chatActivity
            ],
            'highLiteOffset' =>
                $this->highLiteOffset
            ,
            'chatAnalyseByCriteria' => [
                'values' => $this->chatAnalyseByCriteria
            ]
        ];
    }
}