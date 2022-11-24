<?php


abstract class BasicEnum
{
    private static $constCacheArray = NULL;
    public static function getConstants()
    {
        if (!(self::$constCacheArray == NULL)) {
            goto ec;
        }
        self::$constCacheArray = array();
        ec:
        $E_ = get_called_class();
        if (array_key_exists($E_, self::$constCacheArray)) {
            goto sA;
        }
        $RZ = new ReflectionClass($E_);
        self::$constCacheArray[$E_] = $RZ->getConstants();
        sA:
        return self::$constCacheArray[$E_];
    }
    public static function isValidName($UO, $se = false)
    {
        $BT = self::getConstants();
        if (!$se) {
            goto LM;
        }
        return array_key_exists($UO, $BT);
        LM:
        $yp = array_map("\163\164\162\x74\157\154\157\167\x65\162", array_keys($BT));
        return in_array(strtolower($UO), $yp);
    }
    public static function isValidValue($wE, $se = true)
    {
        $jQ = array_values(self::getConstants());
        return in_array($wE, $jQ, $se);
    }
}
