<?php

namespace Utils;

class RandomUtility
{
    
    /**
     * Generate a random number to be used as ANSI color code in Console
     * @return int A random number among 2 4 8 16 32 64 128
     */
    public static function generateRandomColorInt()
    {
        $random_bits = rand(1, 7);
        return pow(2, $random_bits);
    }
}
