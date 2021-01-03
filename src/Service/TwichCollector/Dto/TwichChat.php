<?php

declare(strict_types=1);


namespace App\Service\TwichCollector\Dto;


class TwichChat
{
    public $message;
    public $offset;
    /** @var array $emoticon contain info about smiles */
    public $emoticon;
}