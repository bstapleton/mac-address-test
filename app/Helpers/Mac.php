<?php

namespace App\Helpers;

class Mac
{
    public function convertToOui(string $mac)
    {
        $value = str_replace(['-', ':', '.', ' '], '', $mac);
        $value = str_split($value, 6);

        return $value[0];
    }
}
