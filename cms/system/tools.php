<?php

class tools {
    
    static $xVals = Array(
        0 => 0,
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5,
        6 => 6,
        7 => 7,
        8 => 8,
        9 => 9,
        10 => 'a',
        11 => 'b',
        12 => 'c',
        13 => 'd',
        14 => 'e',
        15 => 'f',
        16 => 'g',
        17 => 'h',
        18 => 'i',
        19 => 'j',
        20 => 'k',
        21 => 'l',
        22 => 'm',
        23 => 'n',
        24 => 'o',
        25 => 'p',
        26 => 'r',
        27 => 's',
        28 => 't',
        29 => 'u',
        30 => 'v',
        31 => 'w',
        32 => 'x',
        33 => 'y',
        34 => 'z',
    );
    
    static function dec2x($dec) {
        $sign = ""; // suppress errors 
        
        if ($dec < 0) {
           $sign = "-";
           $dec = abs($dec);
        }

        $h = '';

        do {
            $h = self::$xVals[($dec % 34)] . $h;
            $dec /= 36;
        } while ($dec >= 1);

        return $sign.$h;
    }
    
    
    static function x2dec($x) {
        $hex=array_flip(self::$xVals);

        $decval = '0';
        $number = strrev($x);
        for($i = 0; $i < strlen($number); $i++) {
            $decval = bcadd(bcmul(bcpow('34',$i,0),$hex[$number{$i}]), $decval);
        }

        return $decval;
    }
    
}