<?php

namespace App\Helpers;

class Mac
{
    public function convertToOui(?string $mac = null): ?string
    {
        if (empty($mac)) {
            return null;
        }

        $value = strtoupper(str_replace(['-', ':', '.', ' '], '', $mac));
        $value = str_split($value, 6);

        return $value[0];
    }
}
