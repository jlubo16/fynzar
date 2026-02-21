<?php
// app/Helpers/MathHelper.php
namespace App\Helpers;

class MathHelper
{
    public static function standardDeviation($array)
    {
        $n = count($array);
        if ($n === 0) {
            return 0;
        }
        
        $mean = array_sum($array) / $n;
        $carry = 0.0;
        
        foreach ($array as $val) {
            $d = ((double) $val) - $mean;
            $carry += $d * $d;
        }
        
        return sqrt($carry / $n);
    }
}