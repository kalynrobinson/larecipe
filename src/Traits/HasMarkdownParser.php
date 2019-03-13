<?php

namespace BinaryTorch\LaRecipe\Traits;

use ParsedownExtra;

trait HasMarkdownParser
{
    /**
     * @param $text
     *
     * @throws \Exception
     *
     * @return null|string|string[]
     */
    public function parse($text)
    {
        return (new ParsedownExtra())->text($text);
    }
}
