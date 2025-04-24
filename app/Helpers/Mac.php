<?php

namespace App\Helpers;

class Mac
{
    public function convertToOui(string $mac)
    {
        $value = strtoupper(str_replace(['-', ':', '.', ' '], '', $mac));
        $value = str_split($value, 6);

        return $value[0];
    }
}
