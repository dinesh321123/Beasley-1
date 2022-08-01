<?php


namespace RobRichards\XMLSecLibs;

use DOMElement;
use Exception;
class XMLSecurityKey
{
    const TRIPLEDES_CBC = "\150\x74\x74\160\x3a\x2f\x2f\x77\167\167\x2e\x77\63\x2e\x6f\x72\147\57\x32\60\60\61\57\x30\x34\x2f\x78\x6d\154\x65\156\143\43\164\162\x69\x70\x6c\x65\144\145\x73\x2d\x63\142\143";
    const AES128_CBC = "\x68\164\x74\160\x3a\57\57\167\167\x77\x2e\167\x33\x2e\x6f\x72\x67\57\x32\x30\60\61\x2f\x30\64\x2f\x78\x6d\x6c\145\x6e\143\x23\x61\145\163\61\x32\70\55\x63\142\143";
    const AES192_CBC = "\x68\x74\x74\160\x3a\57\57\167\167\167\56\x77\63\56\x6f\162\x67\57\62\x30\x30\x31\57\x30\x34\x2f\170\x6d\154\x65\156\x63\43\x61\145\x73\x31\x39\62\55\x63\x62\143";
    const AES256_CBC = "\x68\x74\x74\160\x3a\x2f\x2f\167\167\x77\56\167\63\x2e\157\162\147\57\x32\x30\60\61\57\x30\64\x2f\170\155\x6c\145\x6e\143\43\x61\x65\163\x32\x35\66\55\143\142\143";
    const RSA_1_5 = "\x68\x74\x74\x70\72\x2f\x2f\x77\167\x77\56\x77\x33\x2e\x6f\x72\147\57\x32\60\x30\61\57\x30\64\57\170\155\154\x65\156\143\x23\162\x73\x61\55\x31\x5f\65";
    const RSA_OAEP_MGF1P = "\150\164\164\x70\72\57\x2f\167\x77\x77\56\167\63\56\157\x72\x67\57\62\x30\x30\x31\x2f\x30\x34\x2f\170\x6d\x6c\x65\156\x63\x23\162\x73\x61\55\157\141\x65\x70\x2d\x6d\147\146\61\x70";
    const DSA_SHA1 = "\150\x74\x74\160\72\57\x2f\167\x77\167\x2e\167\x33\56\157\162\147\57\x32\x30\60\x30\x2f\x30\x39\x2f\170\x6d\154\x64\163\x69\x67\43\144\163\141\x2d\x73\x68\141\x31";
    const RSA_SHA1 = "\150\164\164\160\x3a\x2f\57\167\x77\167\56\x77\x33\56\x6f\162\x67\57\x32\x30\60\x30\x2f\60\71\x2f\x78\x6d\154\144\163\151\147\x23\x72\x73\x61\x2d\163\150\x61\x31";
    const RSA_SHA256 = "\150\164\x74\160\72\x2f\57\167\167\x77\x2e\167\63\x2e\157\x72\x67\57\62\x30\60\61\57\x30\x34\x2f\x78\155\x6c\x64\163\151\147\x2d\155\x6f\x72\145\x23\x72\x73\x61\55\x73\x68\x61\62\65\x36";
    const RSA_SHA384 = "\150\x74\x74\160\72\x2f\x2f\167\167\167\56\167\x33\56\157\x72\147\x2f\x32\60\x30\61\57\60\64\x2f\170\x6d\154\x64\163\151\x67\x2d\x6d\157\162\145\x23\162\x73\141\x2d\163\x68\141\x33\70\64";
    const RSA_SHA512 = "\150\x74\164\x70\x3a\57\x2f\167\x77\x77\56\x77\x33\56\157\x72\147\57\62\60\x30\61\x2f\x30\64\57\170\155\x6c\144\163\x69\147\55\x6d\x6f\162\145\43\x72\x73\x61\55\x73\150\x61\65\61\x32";
    const HMAC_SHA1 = "\x68\164\x74\x70\x3a\x2f\57\x77\167\x77\x2e\x77\63\56\x6f\x72\147\57\62\60\60\x30\x2f\x30\x39\57\170\155\x6c\144\163\151\x67\43\x68\x6d\x61\143\55\x73\150\x61\61";
    private $cryptParams = array();
    public $type = 0;
    public $key = null;
    public $passphrase = '';
    public $iv = null;
    public $name = null;
    public $keyChain = null;
    public $isEncrypted = false;
    public $encryptedCtx = null;
    public $guid = null;
    private $x509Certificate = null;
    private $X509Thumbprint = null;
    public function __construct($Uj, $WL = null)
    {
        switch ($Uj) {
            case self::TRIPLEDES_CBC:
                $this->cryptParams["\x6c\151\x62\x72\141\x72\x79"] = "\x6f\x70\x65\156\x73\x73\x6c";
                $this->cryptParams["\x63\151\x70\x68\x65\x72"] = "\144\145\x73\55\145\x64\x65\63\55\x63\x62\x63";
                $this->cryptParams["\164\x79\160\145"] = "\163\171\x6d\x6d\145\164\x72\151\x63";
                $this->cryptParams["\155\145\x74\150\x6f\x64"] = "\x68\x74\x74\160\x3a\57\x2f\x77\x77\167\x2e\167\x33\56\157\x72\147\57\x32\x30\x30\61\57\x30\x34\57\170\155\x6c\x65\156\x63\x23\164\x72\x69\160\154\x65\144\x65\163\55\x63\142\143";
                $this->cryptParams["\153\x65\171\163\x69\172\145"] = 24;
                $this->cryptParams["\x62\x6c\x6f\x63\x6b\x73\151\172\145"] = 8;
                goto TWV;
            case self::AES128_CBC:
                $this->cryptParams["\x6c\x69\x62\162\x61\x72\x79"] = "\157\160\x65\156\x73\x73\x6c";
                $this->cryptParams["\x63\151\x70\x68\145\x72"] = "\x61\145\163\x2d\x31\x32\70\x2d\143\x62\x63";
                $this->cryptParams["\x74\171\x70\145"] = "\163\171\x6d\155\145\164\x72\151\x63";
                $this->cryptParams["\155\145\164\x68\x6f\x64"] = "\x68\164\164\160\x3a\x2f\x2f\167\167\x77\56\x77\x33\x2e\x6f\x72\147\57\62\60\x30\61\x2f\60\64\57\170\155\x6c\x65\x6e\143\x23\141\145\163\x31\x32\70\55\x63\x62\x63";
                $this->cryptParams["\153\145\x79\x73\x69\x7a\x65"] = 16;
                $this->cryptParams["\142\x6c\x6f\143\153\163\x69\x7a\x65"] = 16;
                goto TWV;
            case self::AES192_CBC:
                $this->cryptParams["\x6c\151\142\162\141\162\x79"] = "\x6f\x70\145\156\x73\x73\154";
                $this->cryptParams["\x63\151\160\150\145\162"] = "\x61\x65\163\x2d\x31\71\62\x2d\x63\x62\x63";
                $this->cryptParams["\x74\x79\x70\145"] = "\x73\x79\x6d\x6d\145\x74\162\x69\x63";
                $this->cryptParams["\x6d\x65\x74\x68\x6f\144"] = "\150\x74\x74\x70\x3a\57\57\167\x77\x77\x2e\x77\63\56\x6f\162\147\x2f\x32\x30\x30\61\57\60\64\x2f\x78\155\x6c\x65\156\143\x23\141\x65\163\61\71\62\x2d\143\142\x63";
                $this->cryptParams["\x6b\x65\171\x73\151\x7a\145"] = 24;
                $this->cryptParams["\142\x6c\157\x63\x6b\x73\x69\172\145"] = 16;
                goto TWV;
            case self::AES256_CBC:
                $this->cryptParams["\x6c\x69\x62\x72\x61\x72\x79"] = "\157\160\145\x6e\x73\x73\154";
                $this->cryptParams["\143\151\160\150\145\162"] = "\141\x65\x73\x2d\62\x35\x36\55\x63\142\x63";
                $this->cryptParams["\x74\171\160\145"] = "\163\x79\155\x6d\x65\x74\x72\x69\143";
                $this->cryptParams["\155\145\164\x68\157\144"] = "\150\164\x74\x70\72\x2f\57\x77\167\x77\56\167\63\x2e\157\x72\147\x2f\62\x30\60\x31\57\60\64\57\x78\x6d\154\145\x6e\143\x23\x61\145\163\62\x35\66\55\x63\142\x63";
                $this->cryptParams["\x6b\145\x79\163\x69\172\145"] = 32;
                $this->cryptParams["\x62\154\157\143\x6b\163\151\x7a\x65"] = 16;
                goto TWV;
            case self::RSA_1_5:
                $this->cryptParams["\154\151\x62\x72\141\162\x79"] = "\x6f\x70\x65\156\x73\x73\x6c";
                $this->cryptParams["\x70\141\144\144\x69\x6e\147"] = OPENSSL_PKCS1_PADDING;
                $this->cryptParams["\155\145\164\150\157\144"] = "\150\164\164\160\72\57\57\x77\x77\x77\56\x77\x33\x2e\x6f\x72\147\x2f\x32\60\x30\x31\x2f\60\64\x2f\170\155\154\x65\156\143\43\162\x73\x61\55\61\x5f\65";
                if (!(is_array($WL) && !empty($WL["\164\171\160\x65"]))) {
                    goto hBB;
                }
                if (!($WL["\164\x79\160\145"] == "\160\165\x62\x6c\x69\143" || $WL["\164\x79\x70\x65"] == "\160\x72\x69\166\x61\164\145")) {
                    goto FBo;
                }
                $this->cryptParams["\164\171\x70\145"] = $WL["\x74\171\160\x65"];
                goto TWV;
                FBo:
                hBB:
                throw new Exception("\x43\145\162\164\x69\x66\151\x63\x61\x74\x65\40\42\x74\x79\x70\145\42\x20\x28\x70\162\151\166\x61\x74\x65\x2f\160\165\x62\154\151\x63\51\x20\x6d\165\163\x74\x20\x62\x65\x20\x70\141\163\163\x65\144\x20\x76\151\141\40\160\141\x72\x61\x6d\x65\164\x65\x72\163");
            case self::RSA_OAEP_MGF1P:
                $this->cryptParams["\x6c\151\x62\x72\x61\x72\x79"] = "\x6f\160\x65\x6e\x73\x73\x6c";
                $this->cryptParams["\x70\x61\x64\x64\x69\156\x67"] = OPENSSL_PKCS1_OAEP_PADDING;
                $this->cryptParams["\155\145\164\x68\157\144"] = "\150\164\164\160\x3a\x2f\x2f\x77\167\167\56\x77\63\56\x6f\162\x67\x2f\62\60\x30\x31\x2f\60\64\x2f\170\155\x6c\145\x6e\x63\43\x72\x73\141\x2d\x6f\x61\x65\x70\x2d\155\x67\146\x31\x70";
                $this->cryptParams["\150\x61\163\x68"] = null;
                if (!(is_array($WL) && !empty($WL["\x74\171\x70\145"]))) {
                    goto TFN;
                }
                if (!($WL["\164\x79\160\x65"] == "\160\165\142\154\x69\143" || $WL["\x74\171\x70\x65"] == "\160\x72\151\166\x61\x74\145")) {
                    goto hEh;
                }
                $this->cryptParams["\x74\171\x70\145"] = $WL["\164\171\x70\x65"];
                goto TWV;
                hEh:
                TFN:
                throw new Exception("\x43\145\162\164\x69\146\x69\143\x61\164\145\40\42\164\x79\x70\x65\42\x20\x28\x70\162\x69\x76\x61\164\145\x2f\160\165\x62\x6c\151\143\x29\40\155\x75\163\x74\x20\142\x65\40\160\x61\163\163\145\x64\40\x76\x69\x61\x20\x70\x61\162\x61\155\145\x74\x65\162\x73");
            case self::RSA_SHA1:
                $this->cryptParams["\x6c\x69\x62\x72\x61\162\x79"] = "\157\160\145\156\x73\163\x6c";
                $this->cryptParams["\x6d\x65\164\150\157\144"] = "\150\164\164\160\72\57\57\167\167\167\x2e\167\63\x2e\157\x72\x67\57\62\x30\x30\60\57\60\71\x2f\x78\x6d\154\144\x73\151\x67\43\x72\163\x61\55\163\150\x61\x31";
                $this->cryptParams["\x70\x61\144\144\151\156\147"] = OPENSSL_PKCS1_PADDING;
                if (!(is_array($WL) && !empty($WL["\164\171\x70\145"]))) {
                    goto xdc;
                }
                if (!($WL["\x74\x79\160\x65"] == "\160\165\142\x6c\x69\143" || $WL["\x74\171\160\x65"] == "\160\x72\x69\166\141\164\145")) {
                    goto M5E;
                }
                $this->cryptParams["\x74\171\x70\145"] = $WL["\164\171\160\145"];
                goto TWV;
                M5E:
                xdc:
                throw new Exception("\x43\x65\162\164\x69\146\151\x63\141\164\x65\x20\x22\164\x79\160\145\42\x20\x28\160\162\151\166\141\164\x65\57\x70\x75\142\x6c\x69\x63\x29\40\155\x75\x73\164\40\142\x65\40\160\x61\163\x73\x65\144\40\166\x69\x61\40\160\x61\x72\x61\155\145\164\145\x72\163");
            case self::RSA_SHA256:
                $this->cryptParams["\x6c\151\142\162\141\162\171"] = "\157\160\x65\x6e\x73\x73\154";
                $this->cryptParams["\155\145\164\150\x6f\x64"] = "\x68\x74\x74\x70\x3a\x2f\x2f\167\167\x77\56\167\x33\x2e\x6f\162\147\x2f\62\x30\60\61\57\60\64\x2f\x78\155\154\x64\163\151\147\55\155\x6f\162\x65\43\x72\x73\141\x2d\163\150\x61\62\65\x36";
                $this->cryptParams["\160\x61\x64\x64\151\156\147"] = OPENSSL_PKCS1_PADDING;
                $this->cryptParams["\x64\x69\x67\145\163\x74"] = "\123\x48\x41\62\65\66";
                if (!(is_array($WL) && !empty($WL["\x74\x79\160\145"]))) {
                    goto ZPz;
                }
                if (!($WL["\x74\x79\x70\x65"] == "\160\x75\142\154\151\143" || $WL["\164\x79\x70\145"] == "\160\162\x69\166\141\x74\145")) {
                    goto Nhs;
                }
                $this->cryptParams["\x74\x79\160\145"] = $WL["\164\x79\x70\x65"];
                goto TWV;
                Nhs:
                ZPz:
                throw new Exception("\103\145\x72\x74\x69\146\151\143\x61\x74\145\x20\42\x74\x79\x70\145\x22\x20\50\160\162\151\166\x61\x74\145\x2f\160\x75\142\154\151\143\x29\x20\x6d\x75\x73\x74\40\x62\145\40\x70\141\x73\163\x65\x64\40\x76\151\x61\x20\x70\141\162\141\x6d\x65\164\x65\x72\x73");
            case self::RSA_SHA384:
                $this->cryptParams["\154\x69\142\x72\141\162\x79"] = "\157\x70\145\156\x73\163\154";
                $this->cryptParams["\155\145\164\150\x6f\144"] = "\150\x74\x74\160\72\57\x2f\167\167\x77\x2e\167\63\56\x6f\x72\147\57\62\x30\x30\x31\57\x30\x34\x2f\x78\x6d\x6c\144\x73\x69\x67\55\155\157\x72\145\43\162\x73\x61\55\163\x68\x61\63\x38\x34";
                $this->cryptParams["\x70\x61\144\144\x69\x6e\x67"] = OPENSSL_PKCS1_PADDING;
                $this->cryptParams["\144\151\147\145\163\x74"] = "\x53\x48\x41\63\x38\x34";
                if (!(is_array($WL) && !empty($WL["\x74\x79\160\x65"]))) {
                    goto PaZ;
                }
                if (!($WL["\x74\x79\x70\x65"] == "\x70\165\142\154\x69\143" || $WL["\x74\x79\x70\x65"] == "\x70\162\x69\166\141\164\145")) {
                    goto pS6;
                }
                $this->cryptParams["\164\171\x70\x65"] = $WL["\164\171\160\145"];
                goto TWV;
                pS6:
                PaZ:
                throw new Exception("\x43\145\x72\164\x69\x66\x69\143\x61\x74\x65\40\x22\164\x79\x70\x65\x22\x20\50\160\162\x69\166\x61\x74\x65\57\x70\165\x62\154\151\x63\x29\x20\155\x75\x73\x74\x20\142\x65\40\x70\141\x73\x73\145\x64\x20\x76\x69\x61\x20\160\141\x72\141\155\145\164\x65\x72\163");
            case self::RSA_SHA512:
                $this->cryptParams["\154\x69\142\162\141\x72\171"] = "\x6f\160\x65\x6e\x73\163\154";
                $this->cryptParams["\155\x65\x74\150\157\144"] = "\x68\164\164\160\x3a\x2f\57\x77\x77\167\56\167\x33\x2e\x6f\x72\x67\57\x32\x30\60\x31\x2f\x30\x34\x2f\x78\x6d\x6c\144\x73\x69\x67\x2d\155\x6f\162\x65\43\162\163\x61\x2d\163\150\141\x35\61\62";
                $this->cryptParams["\160\141\x64\144\151\156\147"] = OPENSSL_PKCS1_PADDING;
                $this->cryptParams["\144\x69\x67\x65\163\x74"] = "\x53\x48\x41\x35\61\62";
                if (!(is_array($WL) && !empty($WL["\x74\171\160\145"]))) {
                    goto pxL;
                }
                if (!($WL["\164\171\x70\x65"] == "\160\x75\x62\x6c\151\143" || $WL["\x74\x79\160\145"] == "\x70\x72\151\166\141\x74\145")) {
                    goto JQG;
                }
                $this->cryptParams["\164\x79\160\145"] = $WL["\164\171\160\145"];
                goto TWV;
                JQG:
                pxL:
                throw new Exception("\103\145\162\164\x69\146\151\143\x61\x74\145\x20\42\164\171\160\145\x22\x20\50\160\x72\x69\166\141\x74\x65\x2f\160\x75\142\154\x69\x63\x29\40\x6d\x75\163\164\x20\x62\x65\x20\x70\x61\x73\163\x65\x64\x20\166\x69\x61\40\160\x61\162\141\x6d\145\x74\145\x72\163");
            case self::HMAC_SHA1:
                $this->cryptParams["\154\151\142\x72\141\162\171"] = $Uj;
                $this->cryptParams["\155\145\x74\150\x6f\x64"] = "\x68\164\x74\x70\72\57\x2f\x77\x77\167\56\x77\x33\x2e\157\x72\x67\x2f\62\x30\60\x30\57\60\x39\x2f\170\155\x6c\144\x73\151\147\43\150\155\141\143\55\163\x68\x61\x31";
                goto TWV;
            default:
                throw new Exception("\111\156\166\141\154\x69\144\40\113\x65\171\x20\x54\171\x70\x65");
        }
        mok:
        TWV:
        $this->type = $Uj;
    }
    public function getSymmetricKeySize()
    {
        if (isset($this->cryptParams["\153\x65\171\163\x69\172\145"])) {
            goto Mgd;
        }
        return null;
        Mgd:
        return $this->cryptParams["\x6b\x65\171\163\151\172\145"];
    }
    public function generateSessionKey()
    {
        if (isset($this->cryptParams["\x6b\145\171\x73\x69\172\x65"])) {
            goto Gew;
        }
        throw new Exception("\125\x6e\x6b\156\x6f\x77\x6e\40\x6b\145\171\40\x73\151\x7a\145\x20\x66\157\162\40\164\171\160\x65\40\x22" . $this->type . "\x22\x2e");
        Gew:
        $ts = $this->cryptParams["\153\x65\x79\163\x69\x7a\145"];
        $Z1 = openssl_random_pseudo_bytes($ts);
        if (!($this->type === self::TRIPLEDES_CBC)) {
            goto sO2;
        }
        $cu = 0;
        wJ4:
        if (!($cu < strlen($Z1))) {
            goto kvi;
        }
        $Z9 = ord($Z1[$cu]) & 254;
        $MV = 1;
        $Zb = 1;
        MaE:
        if (!($Zb < 8)) {
            goto FrD;
        }
        $MV ^= $Z9 >> $Zb & 1;
        YWX:
        $Zb++;
        goto MaE;
        FrD:
        $Z9 |= $MV;
        $Z1[$cu] = chr($Z9);
        vZA:
        $cu++;
        goto wJ4;
        kvi:
        sO2:
        $this->key = $Z1;
        return $Z1;
    }
    public static function getRawThumbprint($Go)
    {
        $WY = explode("\12", $Go);
        $K9 = '';
        $Ny = false;
        foreach ($WY as $CB) {
            if (!$Ny) {
                goto uKd;
            }
            if (!(strncmp($CB, "\55\x2d\x2d\55\55\x45\116\x44\40\103\x45\x52\124\111\x46\x49\103\x41\124\x45", 20) == 0)) {
                goto hqA;
            }
            goto v6t;
            hqA:
            $K9 .= trim($CB);
            goto k_D;
            uKd:
            if (!(strncmp($CB, "\55\x2d\x2d\55\55\x42\105\107\x49\x4e\40\x43\105\122\x54\111\106\x49\103\x41\124\x45", 22) == 0)) {
                goto nCc;
            }
            $Ny = true;
            nCc:
            k_D:
            ekF:
        }
        v6t:
        if (empty($K9)) {
            goto FE2;
        }
        return strtolower(sha1(base64_decode($K9)));
        FE2:
        return null;
    }
    public function loadKey($Z1, $lB = false, $Jk = false)
    {
        if ($lB) {
            goto jQa;
        }
        $this->key = $Z1;
        goto EP8;
        jQa:
        $this->key = file_get_contents($Z1);
        EP8:
        if ($Jk) {
            goto jSb;
        }
        $this->x509Certificate = null;
        goto zt_;
        jSb:
        $this->key = openssl_x509_read($this->key);
        openssl_x509_export($this->key, $Ze);
        $this->x509Certificate = $Ze;
        $this->key = $Ze;
        zt_:
        if (!($this->cryptParams["\x6c\x69\x62\x72\x61\x72\171"] == "\157\160\x65\156\163\163\154")) {
            goto Y9l;
        }
        switch ($this->cryptParams["\x74\171\160\145"]) {
            case "\160\x75\x62\x6c\x69\x63":
                if (!$Jk) {
                    goto JOP;
                }
                $this->X509Thumbprint = self::getRawThumbprint($this->key);
                JOP:
                $this->key = openssl_get_publickey($this->key);
                if ($this->key) {
                    goto K2Q;
                }
                throw new Exception("\125\x6e\x61\x62\154\145\x20\x74\x6f\x20\145\170\164\x72\141\143\164\40\160\165\x62\x6c\x69\x63\40\153\145\171");
                K2Q:
                goto A2K;
            case "\160\x72\151\x76\x61\164\145":
                $this->key = openssl_get_privatekey($this->key, $this->passphrase);
                goto A2K;
            case "\163\x79\155\x6d\145\164\x72\x69\x63":
                if (!(strlen($this->key) < $this->cryptParams["\x6b\x65\171\163\151\172\x65"])) {
                    goto fTj;
                }
                throw new Exception("\113\x65\171\x20\x6d\x75\163\x74\40\143\157\156\x74\141\151\x6e\x20\x61\164\x20\x6c\145\x61\x73\164\x20\62\x35\40\143\x68\x61\162\x61\143\x74\x65\162\x73\40\146\x6f\162\x20\164\150\x69\x73\40\143\151\160\150\x65\x72");
                fTj:
                goto A2K;
            default:
                throw new Exception("\125\x6e\x6b\x6e\157\x77\x6e\x20\x74\171\x70\145");
        }
        bcz:
        A2K:
        Y9l:
    }
    private function padISO10126($K9, $es)
    {
        if (!($es > 256)) {
            goto oal;
        }
        throw new Exception("\x42\154\157\x63\153\x20\x73\x69\x7a\x65\x20\x68\151\x67\150\x65\x72\x20\x74\x68\x61\156\x20\x32\x35\66\x20\x6e\x6f\x74\x20\x61\154\x6c\157\167\x65\x64");
        oal:
        $gF = $es - strlen($K9) % $es;
        $jp = chr($gF);
        return $K9 . str_repeat($jp, $gF);
    }
    private function unpadISO10126($K9)
    {
        $gF = substr($K9, -1);
        $EA = ord($gF);
        return substr($K9, 0, -$EA);
    }
    private function encryptSymmetric($K9)
    {
        $this->iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cryptParams["\143\151\x70\x68\x65\162"]));
        $K9 = $this->padISO10126($K9, $this->cryptParams["\142\x6c\x6f\x63\x6b\x73\x69\x7a\x65"]);
        $UT = openssl_encrypt($K9, $this->cryptParams["\143\x69\160\150\x65\162"], $this->key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $this->iv);
        if (!(false === $UT)) {
            goto dP7;
        }
        throw new Exception("\106\x61\151\x6c\x75\162\145\x20\x65\x6e\143\162\171\x70\164\x69\156\147\x20\x44\141\x74\141\x20\x28\157\160\x65\156\163\163\x6c\x20\163\x79\155\155\x65\x74\x72\x69\143\x29\x20\x2d\40" . openssl_error_string());
        dP7:
        return $this->iv . $UT;
    }
    private function decryptSymmetric($K9)
    {
        $dY = openssl_cipher_iv_length($this->cryptParams["\x63\x69\x70\150\x65\162"]);
        $this->iv = substr($K9, 0, $dY);
        $K9 = substr($K9, $dY);
        $q1 = openssl_decrypt($K9, $this->cryptParams["\143\151\x70\x68\145\162"], $this->key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $this->iv);
        if (!(false === $q1)) {
            goto TVF;
        }
        throw new Exception("\x46\x61\x69\x6c\x75\x72\145\40\x64\145\143\162\x79\160\164\x69\156\147\40\104\141\x74\x61\40\x28\x6f\x70\145\156\x73\163\x6c\40\163\171\155\x6d\145\x74\162\151\x63\51\40\55\40" . openssl_error_string());
        TVF:
        return $this->unpadISO10126($q1);
    }
    private function encryptPublic($K9)
    {
        if (openssl_public_encrypt($K9, $UT, $this->key, $this->cryptParams["\x70\x61\x64\144\x69\x6e\147"])) {
            goto GTW;
        }
        throw new Exception("\106\141\x69\x6c\165\x72\145\40\145\x6e\143\x72\171\160\164\x69\156\147\40\104\x61\164\x61\40\x28\x6f\160\x65\x6e\163\x73\154\40\160\x75\x62\x6c\x69\143\51\x20\55\x20" . openssl_error_string());
        GTW:
        return $UT;
    }
    private function decryptPublic($K9)
    {
        if (openssl_public_decrypt($K9, $q1, $this->key, $this->cryptParams["\160\141\x64\x64\151\156\147"])) {
            goto hEX;
        }
        throw new Exception("\x46\141\151\154\x75\162\145\40\144\145\143\162\171\x70\x74\x69\x6e\147\40\104\x61\x74\141\40\50\157\160\145\156\163\x73\x6c\x20\160\x75\x62\154\151\x63\51\40\x2d\x20" . openssl_error_string());
        hEX:
        return $q1;
    }
    private function encryptPrivate($K9)
    {
        if (openssl_private_encrypt($K9, $UT, $this->key, $this->cryptParams["\160\x61\144\x64\151\156\x67"])) {
            goto p2B;
        }
        throw new Exception("\x46\141\151\x6c\x75\x72\x65\40\145\x6e\143\162\171\x70\164\x69\156\147\x20\x44\141\x74\x61\40\50\x6f\x70\145\x6e\163\163\x6c\40\160\x72\151\x76\141\164\x65\51\x20\x2d\x20" . openssl_error_string());
        p2B:
        return $UT;
    }
    private function decryptPrivate($K9)
    {
        if (openssl_private_decrypt($K9, $q1, $this->key, $this->cryptParams["\160\x61\x64\144\151\x6e\x67"])) {
            goto mod;
        }
        throw new Exception("\x46\x61\x69\x6c\165\x72\x65\x20\144\x65\143\x72\171\x70\x74\x69\x6e\147\40\104\x61\164\141\40\50\157\x70\145\156\x73\163\x6c\x20\x70\x72\151\x76\141\164\x65\51\40\x2d\40" . openssl_error_string());
        mod:
        return $q1;
    }
    private function signOpenSSL($K9)
    {
        $jm = OPENSSL_ALGO_SHA1;
        if (empty($this->cryptParams["\x64\151\x67\x65\x73\164"])) {
            goto K8q;
        }
        $jm = $this->cryptParams["\x64\x69\x67\x65\x73\x74"];
        K8q:
        if (openssl_sign($K9, $sD, $this->key, $jm)) {
            goto SNM;
        }
        throw new Exception("\106\x61\151\x6c\x75\x72\x65\40\123\x69\x67\x6e\151\x6e\147\x20\104\x61\x74\141\72\40" . openssl_error_string() . "\40\x2d\x20" . $jm);
        SNM:
        return $sD;
    }
    private function verifyOpenSSL($K9, $sD)
    {
        $jm = OPENSSL_ALGO_SHA1;
        if (empty($this->cryptParams["\x64\x69\x67\x65\163\x74"])) {
            goto D75;
        }
        $jm = $this->cryptParams["\x64\x69\x67\x65\x73\164"];
        D75:
        return openssl_verify($K9, $sD, $this->key, $jm);
    }
    public function encryptData($K9)
    {
        if (!($this->cryptParams["\x6c\151\142\x72\x61\x72\x79"] === "\x6f\x70\x65\x6e\x73\163\154")) {
            goto QoH;
        }
        switch ($this->cryptParams["\x74\x79\x70\145"]) {
            case "\x73\171\155\155\x65\x74\x72\151\x63":
                return $this->encryptSymmetric($K9);
            case "\160\165\142\154\x69\143":
                return $this->encryptPublic($K9);
            case "\x70\x72\151\166\x61\164\145":
                return $this->encryptPrivate($K9);
        }
        Zyd:
        XL3:
        QoH:
    }
    public function decryptData($K9)
    {
        if (!($this->cryptParams["\x6c\x69\x62\162\141\162\171"] === "\157\x70\x65\156\163\163\154")) {
            goto ORE;
        }
        switch ($this->cryptParams["\x74\x79\x70\145"]) {
            case "\163\x79\x6d\155\145\164\x72\x69\143":
                return $this->decryptSymmetric($K9);
            case "\160\x75\142\154\151\x63":
                return $this->decryptPublic($K9);
            case "\160\162\151\x76\141\164\145":
                return $this->decryptPrivate($K9);
        }
        j_1:
        HLV:
        ORE:
    }
    public function signData($K9)
    {
        switch ($this->cryptParams["\x6c\151\x62\x72\x61\162\x79"]) {
            case "\157\160\145\x6e\163\x73\154":
                return $this->signOpenSSL($K9);
            case self::HMAC_SHA1:
                return hash_hmac("\163\x68\141\x31", $K9, $this->key, true);
        }
        mev:
        r1l:
    }
    public function verifySignature($K9, $sD)
    {
        switch ($this->cryptParams["\x6c\x69\142\x72\x61\162\171"]) {
            case "\x6f\x70\145\156\163\163\x6c":
                return $this->verifyOpenSSL($K9, $sD);
            case self::HMAC_SHA1:
                $ls = hash_hmac("\x73\150\141\x31", $K9, $this->key, true);
                return strcmp($sD, $ls) == 0;
        }
        SSw:
        gnC:
    }
    public function getAlgorith()
    {
        return $this->getAlgorithm();
    }
    public function getAlgorithm()
    {
        return $this->cryptParams["\155\145\x74\150\x6f\144"];
    }
    public static function makeAsnSegment($Uj, $uR)
    {
        switch ($Uj) {
            case 2:
                if (!(ord($uR) > 127)) {
                    goto ENb;
                }
                $uR = chr(0) . $uR;
                ENb:
                goto p8A;
            case 3:
                $uR = chr(0) . $uR;
                goto p8A;
        }
        uk2:
        p8A:
        $Um = strlen($uR);
        if ($Um < 128) {
            goto R8n;
        }
        if ($Um < 256) {
            goto J6O;
        }
        if ($Um < 65536) {
            goto l29;
        }
        $Cu = null;
        goto cZV;
        l29:
        $Cu = sprintf("\45\143\45\x63\45\143\45\x63\x25\163", $Uj, 130, $Um / 256, $Um % 256, $uR);
        cZV:
        goto UMl;
        J6O:
        $Cu = sprintf("\x25\143\x25\143\45\143\x25\x73", $Uj, 129, $Um, $uR);
        UMl:
        goto YHw;
        R8n:
        $Cu = sprintf("\45\x63\x25\143\x25\163", $Uj, $Um, $uR);
        YHw:
        return $Cu;
    }
    public static function convertRSA($pN, $Ui)
    {
        $vO = self::makeAsnSegment(2, $Ui);
        $Kv = self::makeAsnSegment(2, $pN);
        $H0 = self::makeAsnSegment(48, $Kv . $vO);
        $cZ = self::makeAsnSegment(3, $H0);
        $Aw = pack("\110\52", "\x33\60\x30\104\x30\66\60\71\x32\x41\x38\66\64\x38\x38\x36\x46\x37\60\104\60\x31\60\61\60\x31\60\x35\x30\x30");
        $Bj = self::makeAsnSegment(48, $Aw . $cZ);
        $YA = base64_encode($Bj);
        $ck = "\55\55\55\x2d\55\102\105\107\x49\116\x20\x50\x55\x42\114\111\x43\40\113\x45\131\55\55\55\55\x2d\xa";
        $lK = 0;
        aqS:
        if (!($bK = substr($YA, $lK, 64))) {
            goto kGc;
        }
        $ck = $ck . $bK . "\xa";
        $lK += 64;
        goto aqS;
        kGc:
        return $ck . "\55\x2d\x2d\x2d\55\x45\116\x44\x20\x50\x55\x42\114\111\103\40\113\105\131\55\55\55\x2d\55\xa";
    }
    public function serializeKey($TE)
    {
    }
    public function getX509Certificate()
    {
        return $this->x509Certificate;
    }
    public function getX509Thumbprint()
    {
        return $this->X509Thumbprint;
    }
    public static function fromEncryptedKeyElement(DOMElement $XV)
    {
        $iu = new XMLSecEnc();
        $iu->setNode($XV);
        if ($RS = $iu->locateKey()) {
            goto KMQ;
        }
        throw new Exception("\x55\x6e\x61\x62\x6c\x65\x20\x74\157\40\x6c\157\x63\x61\164\x65\x20\x61\x6c\147\157\162\x69\164\150\155\x20\x66\x6f\162\40\164\150\151\x73\x20\105\x6e\143\162\171\160\164\x65\144\40\x4b\x65\x79");
        KMQ:
        $RS->isEncrypted = true;
        $RS->encryptedCtx = $iu;
        XMLSecEnc::staticLocateKeyInfo($RS, $XV);
        return $RS;
    }
}
