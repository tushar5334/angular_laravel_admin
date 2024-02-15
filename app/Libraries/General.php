<?php

/**
 * @uses General Functions
 * @author Tushar Patil <tushar5334@gmail.com>
 * @return
 */

namespace App\Libraries;

use Illuminate\Support\Facades\Storage;

class General
{
    /**
     * @author Tushar Patil <tushar5334@gmail.com>
     *
     * @uses Generate for generate randon string
     *
     * @return string
     */
    public static function generate_token(int $length = 30): string
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet) - 1;
        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[mt_rand(0, $max)];
        }
        return $token;
    }
}
