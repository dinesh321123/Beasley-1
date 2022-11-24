<?php


namespace RobRichards\XMLSecLibs\Utils;

class XPath
{
    const ALPHANUMERIC = "\134\167\134\x64";
    const NUMERIC = "\134\x64";
    const LETTERS = "\134\167";
    const EXTENDED_ALPHANUMERIC = "\134\x77\134\144\x5c\x73\134\x2d\137\x3a\x5c\x2e";
    const SINGLE_QUOTE = "\x27";
    const DOUBLE_QUOTE = "\x22";
    const ALL_QUOTES = "\133\x27\42\x5d";
    public static function filterAttrValue($wE, $mL = self::ALL_QUOTES)
    {
        return preg_replace("\x23" . $mL . "\43", '', $wE);
    }
    public static function filterAttrName($UO, $a9 = self::EXTENDED_ALPHANUMERIC)
    {
        return preg_replace("\43\x5b\x5e" . $a9 . "\x5d\x23", '', $UO);
    }
}
