<?php


namespace RobRichards\XMLSecLibs;

use DOMElement;
use Exception;
class XMLSecurityKey
{
    const TRIPLEDES_CBC = "\x68\x74\164\160\72\57\x2f\167\x77\x77\56\x77\63\56\157\x72\147\57\62\x30\x30\x31\57\60\x34\x2f\170\155\154\145\156\x63\43\164\x72\x69\x70\x6c\x65\x64\x65\163\55\x63\x62\143";
    const AES128_CBC = "\x68\164\x74\160\x3a\x2f\x2f\167\x77\x77\x2e\167\x33\56\157\x72\x67\x2f\62\x30\60\61\57\60\x34\57\170\x6d\154\145\x6e\143\x23\x61\145\x73\x31\x32\x38\x2d\x63\142\143";
    const AES192_CBC = "\x68\164\164\x70\72\57\57\167\x77\167\x2e\167\x33\x2e\157\162\x67\57\62\x30\x30\61\x2f\x30\64\x2f\x78\x6d\154\145\x6e\x63\x23\x61\x65\163\61\x39\62\x2d\x63\x62\143";
    const AES256_CBC = "\150\164\164\160\72\57\x2f\167\167\167\x2e\x77\x33\56\x6f\162\x67\x2f\x32\x30\x30\61\57\60\x34\x2f\x78\x6d\154\x65\x6e\143\x23\141\x65\163\x32\65\x36\x2d\x63\x62\x63";
    const RSA_1_5 = "\150\164\164\x70\x3a\x2f\57\x77\167\167\56\x77\63\56\x6f\162\147\57\62\x30\60\61\57\60\64\57\170\155\x6c\x65\x6e\x63\43\x72\x73\141\55\61\x5f\65";
    const RSA_OAEP_MGF1P = "\150\164\x74\x70\x3a\x2f\x2f\167\167\167\x2e\167\63\x2e\x6f\x72\147\57\62\x30\60\61\x2f\x30\x34\x2f\170\x6d\154\x65\156\143\x23\162\163\x61\x2d\x6f\x61\x65\x70\55\x6d\x67\x66\x31\160";
    const DSA_SHA1 = "\x68\164\164\160\72\x2f\x2f\167\x77\167\x2e\x77\63\56\x6f\x72\x67\x2f\62\60\x30\60\x2f\60\71\57\x78\x6d\x6c\144\x73\151\x67\x23\144\x73\141\x2d\163\x68\x61\61";
    const RSA_SHA1 = "\150\164\164\x70\x3a\x2f\x2f\167\x77\167\56\167\x33\x2e\157\162\x67\x2f\x32\60\60\60\57\60\x39\x2f\x78\x6d\x6c\x64\x73\x69\147\x23\x72\x73\141\x2d\163\150\141\61";
    const RSA_SHA256 = "\150\164\x74\160\x3a\x2f\x2f\167\x77\x77\x2e\167\x33\56\157\162\x67\x2f\62\60\60\x31\x2f\x30\64\57\170\x6d\154\x64\x73\151\x67\x2d\x6d\157\162\x65\43\x72\x73\x61\x2d\x73\x68\141\62\65\x36";
    const RSA_SHA384 = "\x68\164\x74\160\72\x2f\x2f\x77\167\x77\56\x77\x33\x2e\x6f\x72\x67\57\62\x30\x30\61\57\60\x34\57\x78\x6d\154\144\x73\x69\x67\55\x6d\157\x72\x65\43\162\163\141\55\x73\150\x61\63\70\x34";
    const RSA_SHA512 = "\x68\x74\164\160\72\x2f\57\167\x77\x77\x2e\167\x33\56\157\x72\147\x2f\62\x30\x30\x31\x2f\x30\64\57\170\155\154\144\x73\151\x67\x2d\155\x6f\x72\x65\x23\x72\x73\x61\55\163\x68\x61\65\x31\62";
    const HMAC_SHA1 = "\150\164\164\x70\x3a\x2f\57\167\x77\x77\x2e\x77\x33\x2e\157\162\147\57\x32\x30\x30\x30\x2f\60\71\57\170\155\x6c\x64\x73\x69\x67\43\x68\155\141\x63\55\163\x68\x61\61";
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
    public function __construct($Ts, $G7 = null)
    {
        switch ($Ts) {
            case self::TRIPLEDES_CBC:
                $this->cryptParams["\x6c\x69\142\162\x61\162\x79"] = "\157\x70\145\x6e\x73\163\154";
                $this->cryptParams["\143\x69\x70\x68\x65\162"] = "\144\145\163\x2d\145\x64\x65\x33\55\143\x62\143";
                $this->cryptParams["\x74\171\x70\x65"] = "\163\x79\x6d\155\x65\164\162\151\x63";
                $this->cryptParams["\x6d\x65\x74\150\157\x64"] = "\150\x74\x74\160\72\x2f\57\x77\167\167\56\167\63\x2e\157\162\x67\x2f\62\x30\x30\x31\57\x30\x34\x2f\170\155\x6c\x65\x6e\x63\x23\164\162\x69\160\154\x65\x64\145\163\x2d\143\142\143";
                $this->cryptParams["\x6b\x65\x79\163\x69\172\145"] = 24;
                $this->cryptParams["\x62\x6c\x6f\143\x6b\x73\x69\x7a\145"] = 8;
                goto AR;
            case self::AES128_CBC:
                $this->cryptParams["\x6c\x69\x62\x72\x61\x72\x79"] = "\157\160\145\156\163\163\154";
                $this->cryptParams["\143\x69\x70\x68\145\162"] = "\x61\x65\163\55\x31\62\x38\x2d\x63\142\143";
                $this->cryptParams["\x74\171\x70\145"] = "\163\171\x6d\155\x65\164\x72\x69\x63";
                $this->cryptParams["\x6d\145\164\x68\157\144"] = "\x68\x74\x74\x70\72\57\x2f\x77\x77\x77\x2e\167\63\56\x6f\162\x67\57\x32\60\x30\61\57\60\64\57\170\155\x6c\x65\x6e\143\x23\141\145\163\x31\62\70\55\x63\142\x63";
                $this->cryptParams["\153\145\171\x73\x69\x7a\145"] = 16;
                $this->cryptParams["\x62\154\x6f\x63\x6b\x73\x69\172\x65"] = 16;
                goto AR;
            case self::AES192_CBC:
                $this->cryptParams["\x6c\x69\142\162\141\x72\171"] = "\x6f\160\145\x6e\163\x73\x6c";
                $this->cryptParams["\143\151\160\150\145\162"] = "\x61\x65\x73\x2d\61\71\62\55\x63\142\143";
                $this->cryptParams["\x74\171\160\145"] = "\x73\x79\155\155\145\164\x72\x69\143";
                $this->cryptParams["\x6d\145\x74\150\157\144"] = "\x68\164\164\x70\72\57\x2f\167\167\167\x2e\x77\63\56\x6f\x72\x67\x2f\x32\60\x30\x31\x2f\60\x34\57\170\x6d\x6c\145\156\143\x23\141\145\163\61\x39\62\x2d\143\x62\x63";
                $this->cryptParams["\x6b\145\171\163\151\x7a\x65"] = 24;
                $this->cryptParams["\x62\154\x6f\x63\x6b\163\x69\x7a\x65"] = 16;
                goto AR;
            case self::AES256_CBC:
                $this->cryptParams["\x6c\151\x62\162\x61\x72\171"] = "\157\x70\145\156\x73\x73\154";
                $this->cryptParams["\x63\x69\160\x68\x65\x72"] = "\141\145\163\55\x32\x35\x36\x2d\x63\142\143";
                $this->cryptParams["\164\171\160\145"] = "\x73\171\x6d\x6d\145\x74\x72\x69\143";
                $this->cryptParams["\155\x65\x74\150\x6f\x64"] = "\150\164\x74\x70\x3a\x2f\57\x77\x77\x77\x2e\167\x33\x2e\x6f\x72\147\57\62\x30\60\61\57\x30\64\57\170\155\x6c\x65\x6e\143\x23\141\145\163\62\65\x36\55\143\x62\x63";
                $this->cryptParams["\153\145\171\163\151\172\x65"] = 32;
                $this->cryptParams["\x62\x6c\157\x63\x6b\x73\151\x7a\x65"] = 16;
                goto AR;
            case self::RSA_1_5:
                $this->cryptParams["\154\151\142\x72\x61\x72\x79"] = "\x6f\160\145\156\163\x73\x6c";
                $this->cryptParams["\x70\141\144\x64\x69\156\147"] = OPENSSL_PKCS1_PADDING;
                $this->cryptParams["\x6d\145\164\150\x6f\144"] = "\150\x74\164\x70\x3a\57\x2f\x77\167\x77\56\x77\63\56\157\162\x67\57\x32\x30\60\61\x2f\60\x34\x2f\170\155\154\x65\156\x63\x23\x72\x73\x61\x2d\61\x5f\65";
                if (!(is_array($G7) && !empty($G7["\x74\171\160\145"]))) {
                    goto wz;
                }
                if (!($G7["\x74\171\160\145"] == "\x70\165\x62\154\x69\x63" || $G7["\164\x79\x70\x65"] == "\160\162\x69\x76\x61\164\145")) {
                    goto ye;
                }
                $this->cryptParams["\164\171\160\x65"] = $G7["\164\x79\x70\145"];
                goto AR;
                ye:
                wz:
                throw new Exception("\103\145\162\164\151\x66\151\143\141\164\145\x20\42\164\171\x70\145\x22\40\x28\160\162\x69\x76\x61\164\x65\x2f\x70\165\x62\x6c\x69\x63\x29\x20\155\x75\163\x74\x20\142\145\40\x70\x61\x73\163\x65\x64\x20\x76\151\141\40\160\x61\162\x61\155\145\x74\145\x72\163");
            case self::RSA_OAEP_MGF1P:
                $this->cryptParams["\154\x69\142\x72\141\x72\171"] = "\157\x70\145\x6e\163\163\154";
                $this->cryptParams["\x70\141\144\144\x69\156\x67"] = OPENSSL_PKCS1_OAEP_PADDING;
                $this->cryptParams["\155\145\164\150\157\144"] = "\150\164\x74\x70\x3a\x2f\57\x77\167\x77\x2e\167\63\56\x6f\162\147\57\x32\x30\x30\x31\57\60\64\57\170\x6d\x6c\145\x6e\143\x23\x72\x73\141\55\x6f\141\145\160\x2d\x6d\x67\146\61\x70";
                $this->cryptParams["\x68\141\x73\x68"] = null;
                if (!(is_array($G7) && !empty($G7["\164\171\160\x65"]))) {
                    goto s5;
                }
                if (!($G7["\164\171\160\x65"] == "\160\165\142\154\151\143" || $G7["\164\171\x70\145"] == "\x70\x72\x69\166\141\x74\x65")) {
                    goto zu;
                }
                $this->cryptParams["\x74\x79\x70\145"] = $G7["\164\171\160\x65"];
                goto AR;
                zu:
                s5:
                throw new Exception("\103\145\x72\x74\151\x66\151\x63\x61\x74\x65\40\x22\x74\171\160\x65\42\40\50\x70\x72\x69\x76\x61\x74\x65\x2f\x70\x75\142\x6c\151\143\x29\40\155\x75\x73\x74\40\x62\145\40\x70\141\163\x73\145\144\40\x76\151\141\40\160\x61\162\x61\x6d\x65\x74\x65\x72\x73");
            case self::RSA_SHA1:
                $this->cryptParams["\154\x69\x62\162\x61\162\x79"] = "\x6f\160\145\x6e\x73\x73\x6c";
                $this->cryptParams["\x6d\x65\x74\150\157\x64"] = "\x68\x74\164\160\72\x2f\57\x77\x77\167\56\x77\63\x2e\157\162\x67\x2f\62\60\x30\x30\57\60\71\57\170\155\x6c\144\x73\x69\147\43\162\x73\141\x2d\163\x68\x61\x31";
                $this->cryptParams["\x70\x61\x64\x64\151\156\x67"] = OPENSSL_PKCS1_PADDING;
                if (!(is_array($G7) && !empty($G7["\164\x79\x70\x65"]))) {
                    goto nw;
                }
                if (!($G7["\x74\x79\160\x65"] == "\x70\x75\142\154\151\143" || $G7["\x74\x79\160\145"] == "\x70\x72\151\166\x61\164\145")) {
                    goto ZK;
                }
                $this->cryptParams["\164\x79\x70\145"] = $G7["\x74\x79\x70\x65"];
                goto AR;
                ZK:
                nw:
                throw new Exception("\x43\x65\162\x74\151\146\151\x63\141\x74\145\x20\42\x74\x79\160\x65\42\40\x28\x70\x72\151\166\141\164\x65\x2f\160\165\142\x6c\151\x63\x29\x20\x6d\165\163\164\x20\142\x65\x20\160\141\x73\163\x65\144\x20\x76\x69\x61\x20\x70\x61\162\141\x6d\145\x74\x65\x72\x73");
            case self::RSA_SHA256:
                $this->cryptParams["\x6c\x69\x62\162\x61\162\171"] = "\157\160\x65\x6e\x73\x73\x6c";
                $this->cryptParams["\155\145\164\150\x6f\x64"] = "\x68\x74\164\160\72\x2f\x2f\167\167\x77\x2e\167\x33\56\x6f\162\x67\57\x32\60\60\61\x2f\60\x34\x2f\170\155\154\144\163\x69\x67\55\155\x6f\x72\145\43\162\x73\141\x2d\x73\x68\141\x32\65\66";
                $this->cryptParams["\160\x61\144\144\x69\156\x67"] = OPENSSL_PKCS1_PADDING;
                $this->cryptParams["\x64\x69\x67\145\163\x74"] = "\123\x48\x41\62\x35\x36";
                if (!(is_array($G7) && !empty($G7["\x74\171\160\145"]))) {
                    goto S8;
                }
                if (!($G7["\164\171\x70\145"] == "\160\165\142\x6c\x69\143" || $G7["\164\x79\160\x65"] == "\160\x72\151\166\141\x74\x65")) {
                    goto Uv;
                }
                $this->cryptParams["\x74\x79\x70\x65"] = $G7["\164\171\160\145"];
                goto AR;
                Uv:
                S8:
                throw new Exception("\x43\145\x72\x74\x69\146\151\x63\x61\x74\145\40\x22\x74\171\160\145\x22\40\50\160\162\151\x76\x61\164\145\x2f\160\x75\x62\x6c\151\x63\x29\x20\155\x75\x73\164\40\x62\x65\40\160\141\163\x73\x65\x64\x20\x76\151\141\40\160\141\162\x61\x6d\x65\x74\x65\162\163");
            case self::RSA_SHA384:
                $this->cryptParams["\154\151\142\x72\x61\x72\171"] = "\157\160\145\x6e\163\163\154";
                $this->cryptParams["\x6d\x65\x74\x68\x6f\x64"] = "\150\x74\164\x70\x3a\x2f\57\x77\x77\167\x2e\x77\x33\56\157\162\147\x2f\x32\60\60\61\57\x30\x34\57\170\x6d\x6c\x64\x73\x69\x67\x2d\155\x6f\162\145\43\x72\x73\x61\55\x73\x68\x61\63\70\x34";
                $this->cryptParams["\x70\x61\144\x64\151\156\147"] = OPENSSL_PKCS1_PADDING;
                $this->cryptParams["\x64\151\x67\145\163\164"] = "\123\x48\101\63\x38\64";
                if (!(is_array($G7) && !empty($G7["\164\171\x70\x65"]))) {
                    goto jN;
                }
                if (!($G7["\164\x79\160\145"] == "\160\165\x62\x6c\151\143" || $G7["\164\171\x70\145"] == "\160\x72\x69\166\x61\x74\x65")) {
                    goto ZY;
                }
                $this->cryptParams["\x74\171\x70\145"] = $G7["\164\171\160\145"];
                goto AR;
                ZY:
                jN:
                throw new Exception("\x43\x65\162\164\151\x66\x69\143\x61\164\x65\x20\42\x74\x79\160\145\x22\40\x28\x70\x72\x69\166\x61\x74\x65\57\160\x75\142\154\151\x63\51\x20\x6d\x75\x73\x74\x20\142\x65\40\160\141\x73\x73\x65\144\40\x76\x69\x61\x20\160\x61\162\141\x6d\145\x74\145\162\x73");
            case self::RSA_SHA512:
                $this->cryptParams["\154\151\142\x72\x61\x72\171"] = "\x6f\160\x65\156\163\x73\154";
                $this->cryptParams["\x6d\x65\x74\150\x6f\x64"] = "\x68\x74\x74\160\72\57\x2f\x77\x77\x77\x2e\167\63\56\x6f\162\x67\57\62\60\60\x31\57\60\64\57\x78\x6d\x6c\144\163\151\x67\55\x6d\x6f\x72\x65\x23\x72\163\141\55\163\x68\x61\65\x31\x32";
                $this->cryptParams["\160\141\144\144\151\156\x67"] = OPENSSL_PKCS1_PADDING;
                $this->cryptParams["\x64\151\x67\145\x73\164"] = "\123\x48\x41\x35\x31\x32";
                if (!(is_array($G7) && !empty($G7["\164\x79\x70\145"]))) {
                    goto Dr;
                }
                if (!($G7["\164\171\160\x65"] == "\x70\x75\142\154\x69\x63" || $G7["\164\171\160\145"] == "\160\x72\x69\x76\141\x74\x65")) {
                    goto bI;
                }
                $this->cryptParams["\x74\x79\160\145"] = $G7["\164\171\x70\145"];
                goto AR;
                bI:
                Dr:
                throw new Exception("\x43\145\x72\x74\x69\x66\151\143\141\x74\x65\40\x22\x74\x79\x70\145\42\40\50\160\x72\x69\166\x61\x74\145\x2f\x70\165\x62\x6c\151\x63\x29\x20\x6d\165\163\164\x20\142\x65\x20\x70\141\163\x73\x65\144\40\x76\151\x61\40\160\x61\x72\x61\x6d\145\164\145\x72\163");
            case self::HMAC_SHA1:
                $this->cryptParams["\154\x69\142\162\141\162\171"] = $Ts;
                $this->cryptParams["\x6d\145\164\x68\x6f\x64"] = "\150\x74\164\160\x3a\57\x2f\167\x77\x77\x2e\167\x33\56\x6f\x72\x67\57\x32\60\x30\x30\57\60\x39\57\170\155\x6c\x64\163\151\147\x23\x68\155\141\x63\55\163\x68\141\x31";
                goto AR;
            default:
                throw new Exception("\x49\156\166\141\x6c\x69\x64\40\113\x65\171\x20\x54\x79\x70\145");
        }
        J6:
        AR:
        $this->type = $Ts;
    }
    public function getSymmetricKeySize()
    {
        if (isset($this->cryptParams["\x6b\x65\171\163\151\x7a\145"])) {
            goto qf;
        }
        return null;
        qf:
        return $this->cryptParams["\153\x65\171\163\x69\x7a\145"];
    }
    public function generateSessionKey()
    {
        if (isset($this->cryptParams["\x6b\145\171\x73\x69\172\145"])) {
            goto wE;
        }
        throw new Exception("\x55\x6e\x6b\156\157\x77\x6e\x20\x6b\145\x79\x20\163\151\172\145\x20\x66\x6f\162\40\164\171\x70\x65\40\x22" . $this->type . "\x22\56");
        wE:
        $po = $this->cryptParams["\x6b\145\171\x73\151\172\x65"];
        $XC = openssl_random_pseudo_bytes($po);
        if (!($this->type === self::TRIPLEDES_CBC)) {
            goto pq;
        }
        $vF = 0;
        Bk:
        if (!($vF < strlen($XC))) {
            goto FY;
        }
        $rU = ord($XC[$vF]) & 0xfe;
        $Pk = 1;
        $oR = 1;
        a2:
        if (!($oR < 8)) {
            goto oS;
        }
        $Pk ^= $rU >> $oR & 1;
        u3:
        $oR++;
        goto a2;
        oS:
        $rU |= $Pk;
        $XC[$vF] = chr($rU);
        Kp:
        $vF++;
        goto Bk;
        FY:
        pq:
        $this->key = $XC;
        return $XC;
    }
    public static function getRawThumbprint($lK)
    {
        $HP = explode("\xa", $lK);
        $Tb = '';
        $Py = false;
        foreach ($HP as $BE) {
            if (!$Py) {
                goto K5;
            }
            if (!(strncmp($BE, "\x2d\x2d\x2d\55\55\105\x4e\104\40\x43\105\x52\x54\111\106\x49\103\x41\x54\x45", 20) == 0)) {
                goto LA;
            }
            goto CM;
            LA:
            $Tb .= trim($BE);
            goto LR;
            K5:
            if (!(strncmp($BE, "\x2d\55\55\x2d\x2d\x42\x45\x47\111\x4e\x20\x43\105\122\124\x49\x46\111\x43\x41\x54\105", 22) == 0)) {
                goto D0;
            }
            $Py = true;
            D0:
            LR:
            VY:
        }
        CM:
        if (empty($Tb)) {
            goto vd;
        }
        return strtolower(sha1(base64_decode($Tb)));
        vd:
        return null;
    }
    public function loadKey($XC, $sz = false, $Tg = false)
    {
        if ($sz) {
            goto Y6;
        }
        $this->key = $XC;
        goto Wx;
        Y6:
        $this->key = file_get_contents($XC);
        Wx:
        if ($Tg) {
            goto PA;
        }
        $this->x509Certificate = null;
        goto Qs;
        PA:
        $this->key = openssl_x509_read($this->key);
        openssl_x509_export($this->key, $DA);
        $this->x509Certificate = $DA;
        $this->key = $DA;
        Qs:
        if (!($this->cryptParams["\x6c\151\142\162\x61\x72\x79"] == "\157\x70\145\x6e\163\163\154")) {
            goto AE;
        }
        switch ($this->cryptParams["\164\x79\x70\145"]) {
            case "\x70\x75\142\154\x69\143":
                if (!$Tg) {
                    goto tY;
                }
                $this->X509Thumbprint = self::getRawThumbprint($this->key);
                tY:
                $this->key = openssl_get_publickey($this->key);
                if ($this->key) {
                    goto Ru;
                }
                throw new Exception("\x55\156\141\142\154\x65\x20\164\157\40\x65\x78\164\x72\x61\x63\x74\40\x70\165\142\154\x69\x63\40\153\145\x79");
                Ru:
                goto Rw;
            case "\160\x72\151\166\141\x74\145":
                $this->key = openssl_get_privatekey($this->key, $this->passphrase);
                goto Rw;
            case "\163\171\x6d\155\x65\164\x72\x69\143":
                if (!(strlen($this->key) < $this->cryptParams["\153\145\171\x73\151\x7a\145"])) {
                    goto BE;
                }
                throw new Exception("\113\145\171\40\x6d\165\x73\164\x20\143\x6f\x6e\x74\x61\151\x6e\40\141\x74\x20\154\x65\141\x73\164\40\62\x35\40\x63\x68\x61\162\141\143\x74\145\162\163\x20\x66\157\162\40\164\150\x69\163\x20\x63\x69\x70\150\145\162");
                BE:
                goto Rw;
            default:
                throw new Exception("\x55\x6e\x6b\x6e\157\x77\x6e\x20\x74\171\160\145");
        }
        V5:
        Rw:
        AE:
    }
    private function padISO10126($Tb, $l0)
    {
        if (!($l0 > 256)) {
            goto r1;
        }
        throw new Exception("\102\x6c\157\x63\x6b\x20\163\151\172\145\40\x68\x69\x67\x68\145\162\40\x74\x68\x61\x6e\x20\62\65\66\40\x6e\x6f\x74\40\141\154\x6c\157\x77\x65\x64");
        r1:
        $iT = $l0 - strlen($Tb) % $l0;
        $aI = chr($iT);
        return $Tb . str_repeat($aI, $iT);
    }
    private function unpadISO10126($Tb)
    {
        $iT = substr($Tb, -1);
        $R6 = ord($iT);
        return substr($Tb, 0, -$R6);
    }
    private function encryptSymmetric($Tb)
    {
        $this->iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cryptParams["\x63\151\160\150\145\162"]));
        $Tb = $this->padISO10126($Tb, $this->cryptParams["\142\x6c\x6f\143\x6b\x73\151\172\145"]);
        $gH = openssl_encrypt($Tb, $this->cryptParams["\x63\x69\160\150\145\162"], $this->key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $this->iv);
        if (!(false === $gH)) {
            goto kH;
        }
        throw new Exception("\x46\x61\x69\x6c\165\162\145\40\x65\156\143\162\171\x70\164\151\x6e\147\x20\104\141\164\141\x20\x28\x6f\160\x65\156\163\x73\154\40\x73\x79\155\155\145\x74\162\x69\143\x29\x20\55\40" . openssl_error_string());
        kH:
        return $this->iv . $gH;
    }
    private function decryptSymmetric($Tb)
    {
        $cm = openssl_cipher_iv_length($this->cryptParams["\143\151\x70\150\145\x72"]);
        $this->iv = substr($Tb, 0, $cm);
        $Tb = substr($Tb, $cm);
        $K1 = openssl_decrypt($Tb, $this->cryptParams["\x63\x69\160\150\145\162"], $this->key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $this->iv);
        if (!(false === $K1)) {
            goto X6;
        }
        throw new Exception("\x46\141\x69\x6c\165\x72\x65\40\144\145\143\x72\x79\x70\164\x69\x6e\147\x20\104\141\x74\141\40\x28\157\x70\145\x6e\163\163\x6c\x20\x73\171\155\155\145\x74\162\151\143\51\40\55\40" . openssl_error_string());
        X6:
        return $this->unpadISO10126($K1);
    }
    private function encryptPublic($Tb)
    {
        if (openssl_public_encrypt($Tb, $gH, $this->key, $this->cryptParams["\x70\141\x64\144\x69\156\x67"])) {
            goto Qh;
        }
        throw new Exception("\x46\141\x69\x6c\165\162\145\40\145\x6e\143\162\x79\160\164\x69\156\x67\x20\104\x61\164\141\x20\50\x6f\x70\x65\x6e\x73\x73\154\40\160\165\x62\x6c\x69\143\51\x20\x2d\40" . openssl_error_string());
        Qh:
        return $gH;
    }
    private function decryptPublic($Tb)
    {
        if (openssl_public_decrypt($Tb, $K1, $this->key, $this->cryptParams["\x70\141\x64\144\x69\156\x67"])) {
            goto CG;
        }
        throw new Exception("\x46\x61\151\x6c\x75\162\x65\40\x64\145\143\x72\171\x70\x74\x69\156\x67\40\x44\141\164\x61\x20\50\157\x70\x65\x6e\163\163\x6c\x20\x70\x75\142\154\151\143\51\40\x2d\40" . openssl_error_string());
        CG:
        return $K1;
    }
    private function encryptPrivate($Tb)
    {
        if (openssl_private_encrypt($Tb, $gH, $this->key, $this->cryptParams["\x70\141\144\144\151\156\147"])) {
            goto pe;
        }
        throw new Exception("\x46\141\151\154\165\162\145\x20\145\156\x63\x72\x79\160\164\x69\x6e\x67\40\x44\x61\164\141\40\x28\x6f\x70\145\156\x73\163\154\40\160\x72\151\166\x61\164\x65\x29\40\55\40" . openssl_error_string());
        pe:
        return $gH;
    }
    private function decryptPrivate($Tb)
    {
        if (openssl_private_decrypt($Tb, $K1, $this->key, $this->cryptParams["\x70\x61\x64\144\x69\x6e\147"])) {
            goto W7;
        }
        throw new Exception("\106\141\151\x6c\165\x72\145\x20\x64\145\143\x72\171\x70\164\151\x6e\x67\40\104\x61\164\x61\40\50\x6f\x70\145\x6e\x73\x73\x6c\40\x70\x72\151\x76\141\164\145\x29\x20\x2d\40" . openssl_error_string());
        W7:
        return $K1;
    }
    private function signOpenSSL($Tb)
    {
        $nb = OPENSSL_ALGO_SHA1;
        if (empty($this->cryptParams["\x64\x69\147\x65\x73\x74"])) {
            goto os;
        }
        $nb = $this->cryptParams["\x64\x69\x67\x65\x73\x74"];
        os:
        if (openssl_sign($Tb, $l8, $this->key, $nb)) {
            goto NW;
        }
        throw new Exception("\106\x61\151\x6c\x75\x72\x65\40\x53\x69\147\156\151\156\147\40\104\x61\x74\141\72\x20" . openssl_error_string() . "\x20\55\x20" . $nb);
        NW:
        return $l8;
    }
    private function verifyOpenSSL($Tb, $l8)
    {
        $nb = OPENSSL_ALGO_SHA1;
        if (empty($this->cryptParams["\x64\151\x67\145\x73\164"])) {
            goto bl;
        }
        $nb = $this->cryptParams["\144\x69\147\x65\x73\164"];
        bl:
        return openssl_verify($Tb, $l8, $this->key, $nb);
    }
    public function encryptData($Tb)
    {
        if (!($this->cryptParams["\154\151\142\162\141\x72\171"] === "\157\160\x65\156\x73\x73\x6c")) {
            goto JV;
        }
        switch ($this->cryptParams["\x74\171\x70\145"]) {
            case "\163\171\x6d\155\x65\x74\162\151\x63":
                return $this->encryptSymmetric($Tb);
            case "\160\x75\142\154\x69\143":
                return $this->encryptPublic($Tb);
            case "\160\162\151\166\x61\x74\145":
                return $this->encryptPrivate($Tb);
        }
        pC:
        tC:
        JV:
    }
    public function decryptData($Tb)
    {
        if (!($this->cryptParams["\154\x69\142\x72\141\162\x79"] === "\x6f\x70\145\156\x73\163\x6c")) {
            goto hu;
        }
        switch ($this->cryptParams["\164\171\x70\x65"]) {
            case "\x73\171\x6d\x6d\x65\164\162\151\x63":
                return $this->decryptSymmetric($Tb);
            case "\x70\x75\x62\x6c\151\143":
                return $this->decryptPublic($Tb);
            case "\160\x72\x69\x76\x61\164\145":
                return $this->decryptPrivate($Tb);
        }
        O6:
        Kc:
        hu:
    }
    public function signData($Tb)
    {
        switch ($this->cryptParams["\x6c\151\142\x72\141\162\x79"]) {
            case "\x6f\160\x65\156\x73\163\x6c":
                return $this->signOpenSSL($Tb);
            case self::HMAC_SHA1:
                return hash_hmac("\x73\150\x61\x31", $Tb, $this->key, true);
        }
        qN:
        yK:
    }
    public function verifySignature($Tb, $l8)
    {
        switch ($this->cryptParams["\x6c\x69\x62\x72\x61\x72\x79"]) {
            case "\x6f\x70\x65\156\163\x73\154":
                return $this->verifyOpenSSL($Tb, $l8);
            case self::HMAC_SHA1:
                $wt = hash_hmac("\163\150\x61\x31", $Tb, $this->key, true);
                return strcmp($l8, $wt) == 0;
        }
        Xe:
        cP:
    }
    public function getAlgorith()
    {
        return $this->getAlgorithm();
    }
    public function getAlgorithm()
    {
        return $this->cryptParams["\x6d\145\164\150\157\144"];
    }
    public static function makeAsnSegment($Ts, $tb)
    {
        switch ($Ts) {
            case 0x2:
                if (!(ord($tb) > 0x7f)) {
                    goto oP;
                }
                $tb = chr(0) . $tb;
                oP:
                goto Lb;
            case 0x3:
                $tb = chr(0) . $tb;
                goto Lb;
        }
        B5:
        Lb:
        $s5 = strlen($tb);
        if ($s5 < 128) {
            goto Vd;
        }
        if ($s5 < 0x100) {
            goto wW;
        }
        if ($s5 < 0x10000) {
            goto Q5;
        }
        $BK = null;
        goto Go;
        Q5:
        $BK = sprintf("\45\143\x25\x63\45\143\x25\143\x25\x73", $Ts, 0x82, $s5 / 0x100, $s5 % 0x100, $tb);
        Go:
        goto FC;
        wW:
        $BK = sprintf("\45\x63\x25\143\x25\143\45\163", $Ts, 0x81, $s5, $tb);
        FC:
        goto Z1;
        Vd:
        $BK = sprintf("\45\x63\x25\x63\45\163", $Ts, $s5, $tb);
        Z1:
        return $BK;
    }
    public static function convertRSA($WI, $Rg)
    {
        $j9 = self::makeAsnSegment(0x2, $Rg);
        $DW = self::makeAsnSegment(0x2, $WI);
        $oB = self::makeAsnSegment(0x30, $DW . $j9);
        $dB = self::makeAsnSegment(0x3, $oB);
        $jN = pack("\x48\52", "\63\x30\x30\104\60\x36\x30\x39\62\101\70\x36\64\70\70\66\106\x37\x30\x44\x30\x31\x30\x31\60\61\60\65\x30\x30");
        $N9 = self::makeAsnSegment(0x30, $jN . $dB);
        $ZE = base64_encode($N9);
        $HT = "\x2d\55\x2d\55\55\102\105\x47\x49\x4e\40\120\x55\x42\x4c\111\103\x20\113\105\x59\x2d\x2d\55\55\x2d\xa";
        $RC = 0;
        Yz:
        if (!($I1 = substr($ZE, $RC, 64))) {
            goto a9;
        }
        $HT = $HT . $I1 . "\12";
        $RC += 64;
        goto Yz;
        a9:
        return $HT . "\x2d\x2d\55\55\55\105\116\104\x20\120\x55\x42\x4c\x49\103\40\113\105\131\55\x2d\55\x2d\x2d\12";
    }
    public function serializeKey($Ez)
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
    public static function fromEncryptedKeyElement(DOMElement $XP)
    {
        $r8 = new XMLSecEnc();
        $r8->setNode($XP);
        if ($s8 = $r8->locateKey()) {
            goto TK;
        }
        throw new Exception("\x55\x6e\141\142\154\145\40\164\157\40\x6c\x6f\x63\x61\x74\x65\40\x61\x6c\147\157\x72\x69\164\150\155\40\x66\x6f\x72\x20\164\150\151\x73\40\105\x6e\143\x72\171\x70\x74\145\144\40\113\x65\171");
        TK:
        $s8->isEncrypted = true;
        $s8->encryptedCtx = $r8;
        XMLSecEnc::staticLocateKeyInfo($s8, $XP);
        return $s8;
    }
}
