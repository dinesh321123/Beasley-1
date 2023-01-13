<?php


abstract class BasicEnum
{
    private static $constCacheArray = NULL;
    public static function getConstants()
    {
        if (!(self::$constCacheArray == NULL)) {
            goto H9r;
        }
        self::$constCacheArray = array();
        H9r:
        $c4 = get_called_class();
        if (array_key_exists($c4, self::$constCacheArray)) {
            goto VQx;
        }
        $pe = new ReflectionClass($c4);
        self::$constCacheArray[$c4] = $pe->getConstants();
        VQx:
        return self::$constCacheArray[$c4];
    }
    public static function isValidName($dC, $Jo = false)
    {
        $Jd = self::getConstants();
        if (!$Jo) {
            goto p20;
        }
        return array_key_exists($dC, $Jd);
        p20:
        $wp = array_map("\x73\x74\x72\164\157\154\x6f\x77\145\x72", array_keys($Jd));
        return in_array(strtolower($dC), $wp);
    }
    public static function isValidValue($zF, $Jo = true)
    {
        $pC = array_values(self::getConstants());
        return in_array($zF, $pC, $Jo);
    }
}
