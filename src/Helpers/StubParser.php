<?php
// src/Helpers/StubParser.php

namespace Bunny\Helpers;

class StubParser
{
    /**
     * Replace placeholders in a stub with provided key-value pairs.
     *
     * @param string $stub
     * @param array  $replacements
     * @return string
     */
    public static function parse($stub, array $replacements)
    {
        foreach ($replacements as $key => $value) {
            $stub = str_replace('{{ ' . $key . ' }}', $value, $stub);
        }
        return $stub;
    }
}
