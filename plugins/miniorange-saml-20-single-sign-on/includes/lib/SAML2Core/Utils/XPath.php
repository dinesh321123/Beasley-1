<?php


namespace RobRichards\XMLSecLibs\Utils;

class XPath
{
    const ALPHANUMERIC = "\x5c\x77\x5c\144";
    const NUMERIC = "\x5c\x64";
    const LETTERS = "\134\x77";
    const EXTENDED_ALPHANUMERIC = "\x5c\167\134\144\134\163\x5c\x2d\137\72\x5c\56";
    const SINGLE_QUOTE = "\47";
    const DOUBLE_QUOTE = "\x22";
    const ALL_QUOTES = "\x5b\47\x22\135";
    public static function filterAttrValue($zF, $Xm = self::ALL_QUOTES)
    {
        return preg_replace("\x23" . $Xm . "\x23", '', $zF);
    }
    public static function filterAttrName($dC, $p8 = self::EXTENDED_ALPHANUMERIC)
    {
        return preg_replace("\43\133\136" . $p8 . "\x5d\x23", '', $dC);
    }
}
