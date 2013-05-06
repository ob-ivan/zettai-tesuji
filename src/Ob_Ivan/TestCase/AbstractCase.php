<?php
namespace Ob_Ivan\TestCase;

use PHPUnit_Framework_TestCase;

abstract class AbstractCase extends PHPUnit_Framework_TestCase
{
    protected function generateChar()
    {
        return chr(mt_rand(32, 126));
    }

    protected function generateFloat($min, $max)
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }

    protected function generateText($maxLength)
    {
        $text = '';
        while (empty($text)) {
            $chars = [];
            for ($i = 0; $i < $maxLength; ++$i) {
                $chars[] = $this->generateChar();
            }
            $text = trim(implode('', $chars));
        }
        return $text;
    }

}
