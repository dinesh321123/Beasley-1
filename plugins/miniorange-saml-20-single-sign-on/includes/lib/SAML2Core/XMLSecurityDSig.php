<?php


namespace RobRichards\XMLSecLibs;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Exception;
use RobRichards\XMLSecLibs\Utils\XPath as XPath;
class XMLSecurityDSig
{
    const XMLDSIGNS = "\150\164\x74\160\72\57\x2f\167\167\x77\x2e\x77\x33\x2e\157\162\x67\57\x32\60\60\60\x2f\60\x39\57\x78\155\154\144\163\151\147\43";
    const SHA1 = "\x68\x74\164\x70\72\x2f\57\x77\167\167\x2e\x77\x33\x2e\x6f\162\147\57\x32\60\60\60\x2f\60\x39\57\x78\x6d\x6c\144\x73\151\147\x23\163\x68\141\x31";
    const SHA256 = "\150\x74\x74\160\72\x2f\x2f\x77\x77\x77\x2e\x77\x33\56\157\x72\147\57\62\60\60\61\x2f\x30\x34\x2f\170\x6d\154\x65\156\143\x23\x73\x68\141\x32\65\x36";
    const SHA384 = "\x68\164\164\160\x3a\57\57\167\167\x77\x2e\167\63\x2e\x6f\162\x67\x2f\62\x30\60\x31\x2f\x30\x34\x2f\x78\x6d\154\x64\163\151\147\55\x6d\x6f\162\145\43\163\x68\141\x33\x38\x34";
    const SHA512 = "\x68\164\164\160\72\x2f\57\167\167\167\56\167\x33\x2e\x6f\162\147\57\x32\60\60\61\x2f\x30\64\x2f\170\155\154\x65\156\143\43\x73\150\141\65\61\62";
    const RIPEMD160 = "\150\164\164\160\x3a\x2f\x2f\167\167\x77\x2e\x77\63\x2e\157\x72\x67\x2f\x32\60\60\x31\57\x30\x34\x2f\170\155\x6c\x65\156\143\x23\162\x69\160\145\155\x64\61\x36\x30";
    const C14N = "\150\x74\x74\x70\72\57\57\167\x77\167\x2e\167\63\56\x6f\162\147\57\124\122\x2f\62\60\x30\61\x2f\122\x45\x43\x2d\170\155\x6c\x2d\143\61\x34\x6e\55\62\x30\x30\61\x30\x33\x31\65";
    const C14N_COMMENTS = "\150\x74\164\160\72\x2f\x2f\167\167\167\56\x77\x33\56\157\162\147\x2f\x54\x52\57\x32\x30\x30\61\x2f\122\x45\103\x2d\x78\155\154\55\x63\61\64\x6e\x2d\62\60\60\x31\x30\63\x31\65\x23\127\151\164\x68\103\157\155\155\145\156\x74\163";
    const EXC_C14N = "\x68\164\x74\160\x3a\x2f\x2f\x77\167\x77\x2e\167\x33\56\157\162\x67\x2f\x32\x30\x30\61\x2f\61\x30\57\170\x6d\x6c\55\145\170\143\55\x63\x31\64\156\x23";
    const EXC_C14N_COMMENTS = "\150\164\164\x70\x3a\57\x2f\167\167\167\56\x77\63\56\x6f\x72\x67\57\62\x30\x30\61\57\x31\x30\57\170\155\x6c\55\145\170\143\x2d\143\x31\x34\x6e\x23\x57\x69\x74\x68\x43\157\x6d\155\x65\x6e\164\163";
    const template = "\74\x64\x73\72\x53\x69\x67\x6e\141\164\165\x72\145\40\x78\x6d\x6c\x6e\x73\x3a\144\163\75\x22\x68\x74\164\x70\x3a\57\57\167\167\167\56\x77\63\x2e\x6f\162\x67\x2f\x32\x30\60\x30\x2f\x30\71\x2f\170\x6d\154\144\163\x69\147\43\x22\x3e\xd\12\x20\40\x3c\144\x73\72\x53\x69\x67\x6e\145\x64\x49\x6e\x66\x6f\x3e\15\12\x20\x20\x20\40\74\x64\163\72\123\151\x67\x6e\x61\x74\165\162\145\x4d\145\164\x68\x6f\x64\40\x2f\76\xd\xa\40\40\x3c\x2f\x64\x73\72\123\151\147\156\x65\x64\x49\x6e\x66\157\76\15\12\74\57\144\163\72\123\151\x67\156\x61\x74\165\x72\145\x3e";
    const BASE_TEMPLATE = "\74\123\151\147\156\x61\x74\165\162\145\40\170\x6d\x6c\156\163\75\42\150\x74\x74\160\x3a\57\57\x77\x77\x77\x2e\167\63\56\x6f\x72\147\57\x32\x30\x30\60\57\x30\71\57\x78\155\x6c\144\163\151\147\43\42\x3e\15\xa\40\x20\x3c\123\x69\x67\x6e\x65\x64\x49\x6e\146\157\x3e\15\12\40\40\x20\40\x3c\x53\x69\147\x6e\x61\164\165\162\145\115\145\x74\x68\157\144\x20\57\76\15\xa\40\40\74\57\123\151\147\156\x65\x64\x49\x6e\146\157\76\15\12\74\x2f\x53\151\x67\x6e\141\164\x75\x72\x65\76";
    public $sigNode = null;
    public $idKeys = array();
    public $idNS = array();
    private $signedInfo = null;
    private $xPathCtx = null;
    private $canonicalMethod = null;
    private $prefix = '';
    private $searchpfx = "\163\x65\x63\x64\163\151\147";
    private $validatedNodes = null;
    public function __construct($fS = "\x64\163")
    {
        $xO = self::BASE_TEMPLATE;
        if (empty($fS)) {
            goto sv;
        }
        $this->prefix = $fS . "\x3a";
        $zm = array("\x3c\x53", "\74\57\123", "\x78\155\154\156\163\x3d");
        $b9 = array("\x3c{$fS}\72\x53", "\74\57{$fS}\72\x53", "\170\155\154\x6e\163\x3a{$fS}\x3d");
        $xO = str_replace($zm, $b9, $xO);
        sv:
        $a3 = new DOMDocument();
        $a3->loadXML($xO);
        $this->sigNode = $a3->documentElement;
    }
    private function resetXPathObj()
    {
        $this->xPathCtx = null;
    }
    private function getXPathObj()
    {
        if (!(empty($this->xPathCtx) && !empty($this->sigNode))) {
            goto Nm;
        }
        $VM = new DOMXPath($this->sigNode->ownerDocument);
        $VM->registerNamespace("\163\x65\143\144\163\151\147", self::XMLDSIGNS);
        $this->xPathCtx = $VM;
        Nm:
        return $this->xPathCtx;
    }
    public static function generateGUID($fS = "\x70\x66\170")
    {
        $nq = md5(uniqid(mt_rand(), true));
        $JT = $fS . substr($nq, 0, 8) . "\x2d" . substr($nq, 8, 4) . "\55" . substr($nq, 12, 4) . "\55" . substr($nq, 16, 4) . "\55" . substr($nq, 20, 12);
        return $JT;
    }
    public static function generate_GUID($fS = "\160\146\x78")
    {
        return self::generateGUID($fS);
    }
    public function locateSignature($F4, $L8 = 0)
    {
        if ($F4 instanceof DOMDocument) {
            goto Xo;
        }
        $ZR = $F4->ownerDocument;
        goto LI;
        Xo:
        $ZR = $F4;
        LI:
        if (!$ZR) {
            goto RR;
        }
        $VM = new DOMXPath($ZR);
        $VM->registerNamespace("\x73\x65\143\144\163\x69\x67", self::XMLDSIGNS);
        $wO = "\x2e\x2f\57\163\x65\143\x64\x73\x69\147\72\x53\x69\147\156\x61\164\x75\162\x65";
        $BC = $VM->query($wO, $F4);
        $this->sigNode = $BC->item($L8);
        return $this->sigNode;
        RR:
        return null;
    }
    public function createNewSignNode($UO, $wE = null)
    {
        $ZR = $this->sigNode->ownerDocument;
        if (!is_null($wE)) {
            goto pN;
        }
        $p8 = $ZR->createElementNS(self::XMLDSIGNS, $this->prefix . $UO);
        goto oC;
        pN:
        $p8 = $ZR->createElementNS(self::XMLDSIGNS, $this->prefix . $UO, $wE);
        oC:
        return $p8;
    }
    public function setCanonicalMethod($zd)
    {
        switch ($zd) {
            case "\150\x74\164\x70\72\57\57\167\167\167\x2e\x77\63\56\x6f\x72\x67\x2f\x54\x52\x2f\62\60\60\61\x2f\x52\105\x43\x2d\170\155\154\x2d\x63\61\x34\156\x2d\x32\60\x30\61\60\x33\x31\65":
            case "\x68\x74\x74\160\x3a\57\57\x77\167\x77\56\x77\63\x2e\x6f\162\x67\x2f\x54\122\x2f\62\x30\60\x31\57\x52\105\103\55\x78\x6d\154\55\143\x31\x34\156\55\62\60\x30\61\x30\x33\61\65\x23\x57\151\164\x68\103\157\155\x6d\x65\156\x74\163":
            case "\150\164\x74\160\72\57\57\x77\167\167\56\167\63\56\157\162\x67\57\x32\x30\60\x31\57\x31\60\x2f\x78\155\154\55\x65\170\x63\x2d\x63\x31\x34\156\x23":
            case "\150\x74\x74\x70\x3a\57\57\x77\x77\x77\x2e\167\x33\56\x6f\x72\x67\x2f\62\60\x30\x31\x2f\x31\60\57\170\155\154\x2d\x65\x78\x63\x2d\143\x31\64\156\x23\x57\x69\x74\150\103\x6f\x6d\155\x65\x6e\x74\x73":
                $this->canonicalMethod = $zd;
                goto cd;
            default:
                throw new Exception("\x49\x6e\166\x61\x6c\151\x64\40\103\x61\x6e\x6f\156\x69\143\x61\x6c\x20\115\x65\164\150\157\144");
        }
        yZ:
        cd:
        if (!($VM = $this->getXPathObj())) {
            goto Xh;
        }
        $wO = "\x2e\57" . $this->searchpfx . "\72\x53\x69\147\x6e\145\144\111\x6e\x66\157";
        $BC = $VM->query($wO, $this->sigNode);
        if (!($Ms = $BC->item(0))) {
            goto kM;
        }
        $wO = "\56\57" . $this->searchpfx . "\x43\141\156\x6f\x6e\151\x63\x61\154\x69\x7a\x61\x74\151\157\x6e\x4d\x65\x74\150\157\x64";
        $BC = $VM->query($wO, $Ms);
        if ($lR = $BC->item(0)) {
            goto ip;
        }
        $lR = $this->createNewSignNode("\103\141\156\x6f\x6e\151\x63\141\x6c\151\172\x61\x74\151\157\156\115\145\x74\x68\x6f\144");
        $Ms->insertBefore($lR, $Ms->firstChild);
        ip:
        $lR->setAttribute("\x41\x6c\x67\157\x72\151\x74\150\155", $this->canonicalMethod);
        kM:
        Xh:
    }
    private function canonicalizeData($p8, $Rj, $TJ = null, $Su = null)
    {
        $Lm = false;
        $aZ = false;
        switch ($Rj) {
            case "\150\164\x74\x70\72\57\x2f\x77\167\167\x2e\167\63\x2e\x6f\x72\147\57\124\122\57\62\x30\60\61\x2f\x52\105\103\x2d\x78\155\x6c\55\143\x31\64\x6e\x2d\x32\x30\x30\x31\x30\63\x31\x35":
                $Lm = false;
                $aZ = false;
                goto uH;
            case "\x68\164\164\160\72\57\x2f\167\x77\x77\56\x77\63\56\x6f\x72\x67\57\124\122\x2f\x32\60\x30\61\x2f\x52\x45\103\55\170\x6d\x6c\x2d\x63\61\x34\x6e\x2d\x32\x30\x30\61\60\63\x31\x35\x23\x57\151\x74\x68\103\157\155\155\x65\156\164\x73":
                $aZ = true;
                goto uH;
            case "\x68\x74\164\x70\72\x2f\x2f\x77\x77\x77\56\167\x33\x2e\x6f\162\x67\57\x32\60\x30\x31\57\x31\60\57\x78\155\x6c\55\145\x78\x63\55\143\x31\64\x6e\x23":
                $Lm = true;
                goto uH;
            case "\150\x74\164\160\x3a\57\57\x77\167\x77\x2e\x77\x33\56\x6f\162\147\x2f\62\x30\x30\61\57\61\60\x2f\170\155\x6c\x2d\x65\x78\143\55\143\x31\64\x6e\43\x57\151\164\x68\x43\x6f\155\155\x65\156\164\163":
                $Lm = true;
                $aZ = true;
                goto uH;
        }
        aP:
        uH:
        if (!(is_null($TJ) && $p8 instanceof DOMNode && $p8->ownerDocument !== null && $p8->isSameNode($p8->ownerDocument->documentElement))) {
            goto lk;
        }
        $XP = $p8;
        Il:
        if (!($oh = $XP->previousSibling)) {
            goto s4;
        }
        if (!($oh->nodeType == XML_PI_NODE || $oh->nodeType == XML_COMMENT_NODE && $aZ)) {
            goto M3;
        }
        goto s4;
        M3:
        $XP = $oh;
        goto Il;
        s4:
        if (!($oh == null)) {
            goto yD;
        }
        $p8 = $p8->ownerDocument;
        yD:
        lk:
        return $p8->C14N($Lm, $aZ, $TJ, $Su);
    }
    public function canonicalizeSignedInfo()
    {
        $ZR = $this->sigNode->ownerDocument;
        $Rj = null;
        if (!$ZR) {
            goto S5;
        }
        $VM = $this->getXPathObj();
        $wO = "\x2e\x2f\x73\x65\x63\x64\163\151\x67\72\x53\151\x67\x6e\145\144\111\156\x66\x6f";
        $BC = $VM->query($wO, $this->sigNode);
        if (!($sx = $BC->item(0))) {
            goto JC;
        }
        $wO = "\56\x2f\163\145\143\144\163\151\x67\x3a\x43\x61\156\157\x6e\x69\x63\141\154\151\172\141\x74\x69\157\156\115\145\x74\150\157\x64";
        $BC = $VM->query($wO, $sx);
        if (!($lR = $BC->item(0))) {
            goto nX;
        }
        $Rj = $lR->getAttribute("\x41\x6c\x67\157\162\151\x74\150\155");
        nX:
        $this->signedInfo = $this->canonicalizeData($sx, $Rj);
        return $this->signedInfo;
        JC:
        S5:
        return null;
    }
    public function calculateDigest($W1, $Tb, $jf = true)
    {
        switch ($W1) {
            case self::SHA1:
                $i_ = "\x73\x68\141\x31";
                goto Py;
            case self::SHA256:
                $i_ = "\x73\150\141\x32\x35\x36";
                goto Py;
            case self::SHA384:
                $i_ = "\163\150\141\63\x38\64";
                goto Py;
            case self::SHA512:
                $i_ = "\163\x68\141\65\x31\x32";
                goto Py;
            case self::RIPEMD160:
                $i_ = "\162\151\160\145\155\144\x31\x36\60";
                goto Py;
            default:
                throw new Exception("\103\x61\156\x6e\157\164\x20\166\141\154\151\144\x61\x74\145\x20\144\x69\147\x65\x73\164\72\40\125\x6e\163\165\160\160\157\162\164\x65\144\x20\101\x6c\x67\x6f\162\x69\x74\x68\x6d\x20\74{$W1}\x3e");
        }
        Cy:
        Py:
        $DK = hash($i_, $Tb, true);
        if (!$jf) {
            goto rH;
        }
        $DK = base64_encode($DK);
        rH:
        return $DK;
    }
    public function validateDigest($C_, $Tb)
    {
        $VM = new DOMXPath($C_->ownerDocument);
        $VM->registerNamespace("\x73\x65\143\144\163\x69\147", self::XMLDSIGNS);
        $wO = "\x73\x74\x72\151\x6e\x67\x28\56\57\163\145\x63\x64\x73\151\x67\72\x44\151\x67\145\163\x74\115\x65\164\150\157\144\57\100\101\154\147\157\x72\x69\164\150\155\51";
        $W1 = $VM->evaluate($wO, $C_);
        $NT = $this->calculateDigest($W1, $Tb, false);
        $wO = "\163\164\x72\151\x6e\147\x28\56\x2f\163\145\x63\x64\x73\151\147\x3a\x44\x69\147\x65\163\164\126\141\154\x75\145\51";
        $B9 = $VM->evaluate($wO, $C_);
        return $NT === base64_decode($B9);
    }
    public function processTransforms($C_, $E8, $eM = true)
    {
        $Tb = $E8;
        $VM = new DOMXPath($C_->ownerDocument);
        $VM->registerNamespace("\163\145\x63\x64\163\x69\x67", self::XMLDSIGNS);
        $wO = "\56\x2f\x73\x65\x63\x64\x73\x69\x67\x3a\x54\162\x61\x6e\x73\x66\157\x72\155\163\x2f\163\x65\x63\144\163\151\x67\72\124\x72\141\x6e\163\146\x6f\162\x6d";
        $Ta = $VM->query($wO, $C_);
        $Ka = "\150\164\164\x70\72\57\57\167\167\x77\x2e\x77\63\56\x6f\162\x67\x2f\124\x52\x2f\62\x30\60\61\x2f\x52\105\103\55\170\155\x6c\x2d\x63\x31\64\156\55\62\x30\x30\x31\x30\x33\x31\65";
        $TJ = null;
        $Su = null;
        foreach ($Ta as $Y4) {
            $Qg = $Y4->getAttribute("\x41\x6c\147\157\x72\151\x74\x68\155");
            switch ($Qg) {
                case "\150\164\x74\160\72\57\x2f\x77\167\167\56\167\x33\56\x6f\162\x67\x2f\62\60\x30\61\x2f\61\60\x2f\x78\155\x6c\x2d\x65\x78\143\x2d\143\61\64\156\x23":
                case "\x68\x74\x74\x70\72\x2f\57\x77\x77\167\x2e\x77\x33\x2e\x6f\x72\x67\57\x32\x30\x30\61\x2f\x31\x30\x2f\x78\x6d\x6c\x2d\145\170\x63\x2d\143\x31\x34\156\43\127\151\164\150\103\157\155\x6d\x65\x6e\x74\x73":
                    if (!$eM) {
                        goto Xj;
                    }
                    $Ka = $Qg;
                    goto ya;
                    Xj:
                    $Ka = "\x68\164\164\160\72\x2f\x2f\167\x77\167\x2e\x77\x33\56\157\x72\x67\57\x32\60\60\61\57\61\60\57\170\x6d\154\55\145\170\143\x2d\x63\61\64\x6e\43";
                    ya:
                    $p8 = $Y4->firstChild;
                    Gx:
                    if (!$p8) {
                        goto WX;
                    }
                    if (!($p8->localName == "\111\x6e\x63\x6c\165\x73\x69\x76\145\x4e\x61\155\x65\163\160\x61\x63\145\163")) {
                        goto J5;
                    }
                    if (!($Cz = $p8->getAttribute("\x50\162\x65\146\151\170\x4c\x69\163\164"))) {
                        goto ub;
                    }
                    $NC = array();
                    $sZ = explode("\40", $Cz);
                    foreach ($sZ as $Cz) {
                        $aG = trim($Cz);
                        if (empty($aG)) {
                            goto zK;
                        }
                        $NC[] = $aG;
                        zK:
                        n5:
                    }
                    UU:
                    if (!(count($NC) > 0)) {
                        goto FI;
                    }
                    $Su = $NC;
                    FI:
                    ub:
                    goto WX;
                    J5:
                    $p8 = $p8->nextSibling;
                    goto Gx;
                    WX:
                    goto vk;
                case "\x68\x74\x74\160\72\57\57\x77\x77\167\x2e\x77\63\x2e\157\x72\147\57\124\x52\57\62\60\60\61\57\x52\x45\x43\x2d\170\155\154\x2d\143\x31\x34\156\55\x32\x30\x30\61\60\63\x31\65":
                case "\150\164\164\x70\72\x2f\x2f\167\x77\167\56\167\x33\56\x6f\x72\147\x2f\x54\122\57\x32\x30\x30\x31\57\122\x45\103\x2d\x78\x6d\x6c\x2d\x63\x31\64\x6e\x2d\62\x30\60\61\x30\63\61\x35\x23\x57\151\164\x68\103\157\155\155\x65\156\164\x73":
                    if (!$eM) {
                        goto H9;
                    }
                    $Ka = $Qg;
                    goto CD;
                    H9:
                    $Ka = "\x68\x74\x74\x70\x3a\x2f\57\x77\x77\167\56\167\63\x2e\157\162\147\x2f\x54\122\x2f\x32\60\x30\61\57\122\x45\103\55\170\x6d\x6c\x2d\x63\x31\x34\x6e\55\x32\x30\x30\61\x30\63\61\65";
                    CD:
                    goto vk;
                case "\150\164\x74\x70\72\57\57\167\167\x77\x2e\x77\x33\x2e\157\x72\147\57\124\122\57\x31\71\x39\71\57\x52\105\103\55\170\160\x61\x74\x68\x2d\x31\x39\x39\x39\x31\x31\x31\x36":
                    $p8 = $Y4->firstChild;
                    a5:
                    if (!$p8) {
                        goto QZ;
                    }
                    if (!($p8->localName == "\130\x50\x61\x74\150")) {
                        goto tu;
                    }
                    $TJ = array();
                    $TJ["\x71\165\145\x72\x79"] = "\x28\x2e\x2f\57\56\40\x7c\40\x2e\57\57\100\x2a\40\174\40\x2e\x2f\x2f\x6e\x61\x6d\145\x73\160\141\143\x65\72\72\x2a\51\x5b" . $p8->nodeValue . "\x5d";
                    $gF["\x6e\x61\155\x65\163\160\141\x63\145\x73"] = array();
                    $Jl = $VM->query("\x2e\x2f\x6e\141\x6d\x65\x73\160\x61\x63\x65\x3a\x3a\52", $p8);
                    foreach ($Jl as $pn) {
                        if (!($pn->localName != "\170\155\154")) {
                            goto SC;
                        }
                        $TJ["\x6e\x61\x6d\145\163\x70\x61\143\145\163"][$pn->localName] = $pn->nodeValue;
                        SC:
                        o4:
                    }
                    tv:
                    goto QZ;
                    tu:
                    $p8 = $p8->nextSibling;
                    goto a5;
                    QZ:
                    goto vk;
            }
            jx:
            vk:
            Fg:
        }
        K7:
        if (!$Tb instanceof DOMNode) {
            goto Sl;
        }
        $Tb = $this->canonicalizeData($E8, $Ka, $TJ, $Su);
        Sl:
        return $Tb;
    }
    public function processRefNode($C_)
    {
        $D5 = null;
        $eM = true;
        if ($wR = $C_->getAttribute("\x55\x52\x49")) {
            goto Yk;
        }
        $eM = false;
        $D5 = $C_->ownerDocument;
        goto Ks;
        Yk:
        $Qq = parse_url($wR);
        if (!empty($Qq["\160\x61\164\150"])) {
            goto t9;
        }
        if ($lW = $Qq["\146\162\x61\x67\155\x65\156\164"]) {
            goto lv;
        }
        $D5 = $C_->ownerDocument;
        goto mV;
        lv:
        $eM = false;
        $b1 = new DOMXPath($C_->ownerDocument);
        if (!($this->idNS && is_array($this->idNS))) {
            goto XE;
        }
        foreach ($this->idNS as $kO => $N3) {
            $b1->registerNamespace($kO, $N3);
            KF:
        }
        CI:
        XE:
        $aD = "\x40\x49\144\x3d\42" . XPath::filterAttrValue($lW, XPath::DOUBLE_QUOTE) . "\42";
        if (!is_array($this->idKeys)) {
            goto EA;
        }
        foreach ($this->idKeys as $lQ) {
            $aD .= "\40\x6f\162\40\x40" . XPath::filterAttrName($lQ) . "\x3d\x22" . XPath::filterAttrValue($lW, XPath::DOUBLE_QUOTE) . "\42";
            AW:
        }
        rv:
        EA:
        $wO = "\x2f\57\x2a\133" . $aD . "\135";
        $D5 = $b1->query($wO)->item(0);
        mV:
        t9:
        Ks:
        $Tb = $this->processTransforms($C_, $D5, $eM);
        if ($this->validateDigest($C_, $Tb)) {
            goto bD;
        }
        return false;
        bD:
        if (!$D5 instanceof DOMNode) {
            goto wb;
        }
        if (!empty($lW)) {
            goto Kr;
        }
        $this->validatedNodes[] = $D5;
        goto gD;
        Kr:
        $this->validatedNodes[$lW] = $D5;
        gD:
        wb:
        return true;
    }
    public function getRefNodeID($C_)
    {
        if (!($wR = $C_->getAttribute("\x55\x52\111"))) {
            goto gF;
        }
        $Qq = parse_url($wR);
        if (!empty($Qq["\160\141\164\150"])) {
            goto sq;
        }
        if (!($lW = $Qq["\146\162\141\147\x6d\145\x6e\164"])) {
            goto fK;
        }
        return $lW;
        fK:
        sq:
        gF:
        return null;
    }
    public function getRefIDs()
    {
        $mB = array();
        $VM = $this->getXPathObj();
        $wO = "\x2e\x2f\x73\145\143\x64\x73\x69\x67\72\x53\x69\147\156\145\x64\x49\x6e\146\157\57\x73\145\143\x64\x73\151\147\x3a\122\x65\146\x65\x72\145\156\143\x65";
        $BC = $VM->query($wO, $this->sigNode);
        if (!($BC->length == 0)) {
            goto m6;
        }
        throw new Exception("\x52\145\x66\145\162\145\x6e\x63\145\x20\x6e\x6f\144\x65\x73\x20\x6e\157\x74\x20\146\157\165\x6e\144");
        m6:
        foreach ($BC as $C_) {
            $mB[] = $this->getRefNodeID($C_);
            IV:
        }
        da:
        return $mB;
    }
    public function validateReference()
    {
        $v7 = $this->sigNode->ownerDocument->documentElement;
        if ($v7->isSameNode($this->sigNode)) {
            goto Sb;
        }
        if (!($this->sigNode->parentNode != null)) {
            goto tp;
        }
        $this->sigNode->parentNode->removeChild($this->sigNode);
        tp:
        Sb:
        $VM = $this->getXPathObj();
        $wO = "\56\x2f\163\x65\x63\144\163\x69\x67\x3a\x53\x69\147\156\145\144\111\156\x66\x6f\57\x73\x65\143\x64\163\x69\147\72\122\x65\146\x65\x72\145\x6e\143\x65";
        $BC = $VM->query($wO, $this->sigNode);
        if (!($BC->length == 0)) {
            goto L_;
        }
        throw new Exception("\122\145\146\145\x72\x65\156\x63\x65\40\156\157\x64\145\x73\x20\x6e\x6f\x74\40\146\157\x75\x6e\144");
        L_:
        $this->validatedNodes = array();
        foreach ($BC as $C_) {
            if ($this->processRefNode($C_)) {
                goto At;
            }
            $this->validatedNodes = null;
            throw new Exception("\122\145\146\x65\x72\x65\156\143\145\x20\166\x61\x6c\151\144\141\x74\x69\x6f\156\x20\x66\x61\151\x6c\x65\x64");
            At:
            uu:
        }
        vW:
        return true;
    }
    private function addRefInternal($Ko, $p8, $Qg, $LO = null, $ID = null)
    {
        $fS = null;
        $bW = null;
        $nR = "\111\144";
        $LZ = true;
        $Vf = false;
        if (!is_array($ID)) {
            goto ym;
        }
        $fS = empty($ID["\160\x72\x65\x66\x69\170"]) ? null : $ID["\160\x72\x65\x66\151\170"];
        $bW = empty($ID["\x70\162\145\146\151\170\137\156\x73"]) ? null : $ID["\x70\162\145\146\151\x78\x5f\156\163"];
        $nR = empty($ID["\151\144\x5f\156\141\x6d\x65"]) ? "\111\144" : $ID["\x69\x64\137\x6e\141\x6d\145"];
        $LZ = !isset($ID["\157\x76\145\x72\x77\162\151\x74\145"]) ? true : (bool) $ID["\157\x76\x65\162\167\x72\151\164\145"];
        $Vf = !isset($ID["\x66\x6f\x72\143\x65\x5f\x75\x72\151"]) ? false : (bool) $ID["\x66\x6f\x72\x63\x65\x5f\x75\162\151"];
        ym:
        $K6 = $nR;
        if (empty($fS)) {
            goto g5;
        }
        $K6 = $fS . "\x3a" . $K6;
        g5:
        $C_ = $this->createNewSignNode("\122\145\x66\x65\162\x65\x6e\x63\145");
        $Ko->appendChild($C_);
        if (!$p8 instanceof DOMDocument) {
            goto Er;
        }
        if ($Vf) {
            goto s1;
        }
        goto t7;
        Er:
        $wR = null;
        if ($LZ) {
            goto qT;
        }
        $wR = $bW ? $p8->getAttributeNS($bW, $nR) : $p8->getAttribute($nR);
        qT:
        if (!empty($wR)) {
            goto NK;
        }
        $wR = self::generateGUID();
        $p8->setAttributeNS($bW, $K6, $wR);
        NK:
        $C_->setAttribute("\125\x52\x49", "\43" . $wR);
        goto t7;
        s1:
        $C_->setAttribute("\125\x52\x49", '');
        t7:
        $Er = $this->createNewSignNode("\124\x72\x61\156\x73\x66\157\x72\155\x73");
        $C_->appendChild($Er);
        if (is_array($LO)) {
            goto W3;
        }
        if (!empty($this->canonicalMethod)) {
            goto Qr;
        }
        goto yf;
        W3:
        foreach ($LO as $Y4) {
            $BN = $this->createNewSignNode("\124\162\141\x6e\x73\146\157\162\155");
            $Er->appendChild($BN);
            if (is_array($Y4) && !empty($Y4["\150\164\164\160\72\x2f\57\167\x77\167\56\x77\x33\x2e\157\x72\x67\57\x54\x52\x2f\x31\x39\71\x39\x2f\122\105\103\x2d\170\160\141\164\x68\55\61\x39\71\71\61\x31\61\x36"]) && !empty($Y4["\150\164\164\160\x3a\57\x2f\167\167\167\56\x77\x33\56\x6f\x72\147\x2f\124\x52\x2f\x31\71\x39\x39\x2f\x52\x45\103\x2d\x78\x70\x61\164\150\55\61\x39\71\71\x31\61\x31\66"]["\161\165\x65\162\x79"])) {
                goto Ik;
            }
            $BN->setAttribute("\x41\x6c\x67\157\x72\x69\164\x68\x6d", $Y4);
            goto wp;
            Ik:
            $BN->setAttribute("\101\154\147\157\x72\x69\x74\150\x6d", "\150\x74\164\160\72\57\57\167\x77\167\56\167\x33\56\x6f\x72\147\57\x54\x52\x2f\61\71\71\71\57\122\x45\103\55\170\x70\x61\164\150\55\61\71\x39\x39\61\x31\x31\66");
            $Z8 = $this->createNewSignNode("\130\120\141\164\x68", $Y4["\x68\x74\164\x70\72\57\x2f\167\x77\167\x2e\167\x33\56\157\x72\x67\57\x54\x52\57\x31\x39\x39\x39\57\122\x45\x43\x2d\x78\x70\x61\164\x68\55\61\71\71\x39\61\x31\61\x36"]["\x71\x75\145\162\171"]);
            $BN->appendChild($Z8);
            if (empty($Y4["\150\x74\164\x70\x3a\x2f\57\x77\167\167\x2e\x77\63\56\x6f\x72\147\57\x54\122\x2f\61\x39\71\x39\57\x52\x45\x43\55\x78\160\141\x74\150\x2d\61\71\71\x39\x31\61\x31\x36"]["\x6e\141\155\145\163\x70\141\143\145\163"])) {
                goto Ca;
            }
            foreach ($Y4["\150\164\x74\x70\72\x2f\57\x77\167\x77\x2e\x77\x33\x2e\x6f\x72\x67\x2f\124\122\57\x31\x39\71\x39\x2f\122\105\x43\55\x78\160\x61\164\x68\55\61\x39\71\x39\x31\x31\x31\x36"]["\x6e\x61\155\x65\163\160\x61\x63\x65\x73"] as $fS => $G3) {
                $Z8->setAttributeNS("\x68\164\x74\x70\72\x2f\57\x77\167\x77\x2e\167\x33\56\x6f\162\x67\x2f\62\60\60\60\57\x78\155\x6c\x6e\163\x2f", "\170\155\x6c\x6e\x73\x3a{$fS}", $G3);
                Xk:
            }
            BF:
            Ca:
            wp:
            yP:
        }
        Rr:
        goto yf;
        Qr:
        $BN = $this->createNewSignNode("\124\x72\x61\156\163\146\x6f\162\155");
        $Er->appendChild($BN);
        $BN->setAttribute("\101\x6c\x67\x6f\162\151\x74\150\155", $this->canonicalMethod);
        yf:
        $qF = $this->processTransforms($C_, $p8);
        $NT = $this->calculateDigest($Qg, $qF);
        $C3 = $this->createNewSignNode("\104\x69\147\x65\163\x74\115\x65\164\x68\x6f\x64");
        $C_->appendChild($C3);
        $C3->setAttribute("\101\x6c\x67\157\x72\151\x74\150\x6d", $Qg);
        $B9 = $this->createNewSignNode("\104\151\x67\145\163\164\x56\x61\x6c\x75\145", $NT);
        $C_->appendChild($B9);
    }
    public function addReference($p8, $Qg, $LO = null, $ID = null)
    {
        if (!($VM = $this->getXPathObj())) {
            goto dF;
        }
        $wO = "\x2e\x2f\x73\x65\x63\x64\163\151\x67\72\x53\x69\147\156\145\144\111\156\x66\157";
        $BC = $VM->query($wO, $this->sigNode);
        if (!($Rn = $BC->item(0))) {
            goto nG;
        }
        $this->addRefInternal($Rn, $p8, $Qg, $LO, $ID);
        nG:
        dF:
    }
    public function addReferenceList($gf, $Qg, $LO = null, $ID = null)
    {
        if (!($VM = $this->getXPathObj())) {
            goto ci;
        }
        $wO = "\56\57\x73\x65\143\144\x73\x69\147\x3a\x53\x69\147\x6e\145\144\111\156\x66\x6f";
        $BC = $VM->query($wO, $this->sigNode);
        if (!($Rn = $BC->item(0))) {
            goto M_;
        }
        foreach ($gf as $p8) {
            $this->addRefInternal($Rn, $p8, $Qg, $LO, $ID);
            k8:
        }
        hP:
        M_:
        ci:
    }
    public function addObject($Tb, $ej = null, $HT = null)
    {
        $Uj = $this->createNewSignNode("\117\x62\152\145\x63\x74");
        $this->sigNode->appendChild($Uj);
        if (empty($ej)) {
            goto wm;
        }
        $Uj->setAttribute("\x4d\151\x6d\x65\124\x79\160\x65", $ej);
        wm:
        if (empty($HT)) {
            goto Q_;
        }
        $Uj->setAttribute("\105\x6e\143\x6f\x64\x69\156\147", $HT);
        Q_:
        if ($Tb instanceof DOMElement) {
            goto cL;
        }
        $xM = $this->sigNode->ownerDocument->createTextNode($Tb);
        goto BO;
        cL:
        $xM = $this->sigNode->ownerDocument->importNode($Tb, true);
        BO:
        $Uj->appendChild($xM);
        return $Uj;
    }
    public function locateKey($p8 = null)
    {
        if (!empty($p8)) {
            goto nQ;
        }
        $p8 = $this->sigNode;
        nQ:
        if ($p8 instanceof DOMNode) {
            goto uF;
        }
        return null;
        uF:
        if (!($ZR = $p8->ownerDocument)) {
            goto Oo;
        }
        $VM = new DOMXPath($ZR);
        $VM->registerNamespace("\x73\x65\143\x64\163\151\x67", self::XMLDSIGNS);
        $wO = "\x73\164\x72\151\x6e\147\x28\56\57\x73\x65\143\x64\x73\x69\147\x3a\123\151\x67\x6e\145\x64\111\156\146\157\x2f\163\x65\143\x64\163\151\x67\72\123\x69\147\156\141\x74\x75\x72\145\115\x65\164\x68\x6f\144\x2f\100\101\154\x67\157\162\x69\x74\x68\x6d\x29";
        $Qg = $VM->evaluate($wO, $p8);
        if (!$Qg) {
            goto En;
        }
        try {
            $s8 = new XMLSecurityKey($Qg, array("\x74\x79\x70\145" => "\x70\x75\142\x6c\x69\143"));
        } catch (Exception $wD) {
            return null;
        }
        return $s8;
        En:
        Oo:
        return null;
    }
    public function verify($s8)
    {
        $ZR = $this->sigNode->ownerDocument;
        $VM = new DOMXPath($ZR);
        $VM->registerNamespace("\163\x65\143\144\163\x69\x67", self::XMLDSIGNS);
        $wO = "\163\164\x72\x69\x6e\147\x28\x2e\x2f\163\145\x63\144\163\151\147\x3a\x53\151\x67\x6e\141\164\165\162\145\x56\x61\x6c\x75\145\x29";
        $y5 = $VM->evaluate($wO, $this->sigNode);
        if (!empty($y5)) {
            goto tW;
        }
        throw new Exception("\x55\156\141\142\154\145\x20\164\157\40\154\x6f\x63\x61\x74\145\40\123\151\x67\x6e\141\164\165\x72\x65\x56\x61\154\x75\145");
        tW:
        return $s8->verifySignature($this->signedInfo, base64_decode($y5));
    }
    public function signData($s8, $Tb)
    {
        return $s8->signData($Tb);
    }
    public function sign($s8, $jy = null)
    {
        if (!($jy != null)) {
            goto mu;
        }
        $this->resetXPathObj();
        $this->appendSignature($jy);
        $this->sigNode = $jy->lastChild;
        mu:
        if (!($VM = $this->getXPathObj())) {
            goto dD;
        }
        $wO = "\x2e\x2f\x73\x65\x63\144\x73\x69\147\72\123\x69\x67\x6e\145\144\x49\156\146\x6f";
        $BC = $VM->query($wO, $this->sigNode);
        if (!($Rn = $BC->item(0))) {
            goto NS;
        }
        $wO = "\56\x2f\163\145\x63\x64\x73\151\147\72\x53\x69\147\x6e\x61\164\x75\162\145\115\145\164\x68\x6f\x64";
        $BC = $VM->query($wO, $Rn);
        $fU = $BC->item(0);
        $fU->setAttribute("\101\x6c\x67\157\x72\151\x74\x68\x6d", $s8->type);
        $Tb = $this->canonicalizeData($Rn, $this->canonicalMethod);
        $y5 = base64_encode($this->signData($s8, $Tb));
        $Sy = $this->createNewSignNode("\123\x69\147\156\141\x74\x75\162\145\126\x61\154\x75\145", $y5);
        if ($cs = $Rn->nextSibling) {
            goto DF;
        }
        $this->sigNode->appendChild($Sy);
        goto Wb;
        DF:
        $cs->parentNode->insertBefore($Sy, $cs);
        Wb:
        NS:
        dD:
    }
    public function appendCert()
    {
    }
    public function appendKey($s8, $Ez = null)
    {
        $s8->serializeKey($Ez);
    }
    public function insertSignature($p8, $Uh = null)
    {
        $Lx = $p8->ownerDocument;
        $Fy = $Lx->importNode($this->sigNode, true);
        if ($Uh == null) {
            goto rJ;
        }
        return $p8->insertBefore($Fy, $Uh);
        goto pa;
        rJ:
        return $p8->insertBefore($Fy);
        pa:
    }
    public function appendSignature($tC, $eW = false)
    {
        $Uh = $eW ? $tC->firstChild : null;
        return $this->insertSignature($tC, $Uh);
    }
    public static function get509XCert($lK, $wp = true)
    {
        $Ip = self::staticGet509XCerts($lK, $wp);
        if (empty($Ip)) {
            goto XJ;
        }
        return $Ip[0];
        XJ:
        return '';
    }
    public static function staticGet509XCerts($Ip, $wp = true)
    {
        if ($wp) {
            goto Cb;
        }
        return array($Ip);
        goto Hy;
        Cb:
        $Tb = '';
        $r7 = array();
        $HP = explode("\12", $Ip);
        $Py = false;
        foreach ($HP as $BE) {
            if (!$Py) {
                goto Wl;
            }
            if (!(strncmp($BE, "\55\55\x2d\55\55\x45\x4e\x44\40\103\x45\122\124\111\x46\x49\x43\101\124\105", 20) == 0)) {
                goto CJ;
            }
            $Py = false;
            $r7[] = $Tb;
            $Tb = '';
            goto Zq;
            CJ:
            $Tb .= trim($BE);
            goto YG;
            Wl:
            if (!(strncmp($BE, "\x2d\x2d\55\55\55\102\105\x47\x49\x4e\x20\x43\x45\x52\124\x49\x46\x49\103\101\x54\105", 22) == 0)) {
                goto zH;
            }
            $Py = true;
            zH:
            YG:
            Zq:
        }
        Ax:
        return $r7;
        Hy:
    }
    public static function staticAdd509Cert($Cw, $lK, $wp = true, $ZO = false, $VM = null, $ID = null)
    {
        if (!$ZO) {
            goto RP;
        }
        $lK = file_get_contents($lK);
        RP:
        if ($Cw instanceof DOMElement) {
            goto Gn;
        }
        throw new Exception("\x49\x6e\166\141\154\151\144\40\160\141\162\x65\x6e\164\x20\x4e\157\144\x65\x20\x70\x61\162\141\x6d\145\x74\145\x72");
        Gn:
        $NJ = $Cw->ownerDocument;
        if (!empty($VM)) {
            goto xr;
        }
        $VM = new DOMXPath($Cw->ownerDocument);
        $VM->registerNamespace("\x73\145\x63\144\163\x69\147", self::XMLDSIGNS);
        xr:
        $wO = "\x2e\57\x73\x65\143\x64\163\x69\147\72\x4b\x65\x79\111\156\146\x6f";
        $BC = $VM->query($wO, $Cw);
        $vh = $BC->item(0);
        $fI = '';
        if (!$vh) {
            goto NE;
        }
        $Cz = $vh->lookupPrefix(self::XMLDSIGNS);
        if (empty($Cz)) {
            goto Cr;
        }
        $fI = $Cz . "\72";
        Cr:
        goto lQ;
        NE:
        $Cz = $Cw->lookupPrefix(self::XMLDSIGNS);
        if (empty($Cz)) {
            goto ds;
        }
        $fI = $Cz . "\72";
        ds:
        $UR = false;
        $vh = $NJ->createElementNS(self::XMLDSIGNS, $fI . "\x4b\x65\x79\111\x6e\x66\157");
        $wO = "\56\x2f\x73\x65\143\x64\x73\x69\147\72\x4f\x62\152\x65\x63\164";
        $BC = $VM->query($wO, $Cw);
        if (!($Ju = $BC->item(0))) {
            goto CQ;
        }
        $Ju->parentNode->insertBefore($vh, $Ju);
        $UR = true;
        CQ:
        if ($UR) {
            goto o0;
        }
        $Cw->appendChild($vh);
        o0:
        lQ:
        $Ip = self::staticGet509XCerts($lK, $wp);
        $rI = $NJ->createElementNS(self::XMLDSIGNS, $fI . "\x58\x35\x30\x39\104\141\164\141");
        $vh->appendChild($rI);
        $kD = false;
        $Jg = false;
        if (!is_array($ID)) {
            goto el;
        }
        if (empty($ID["\x69\163\163\165\145\162\123\x65\162\151\141\x6c"])) {
            goto TC;
        }
        $kD = true;
        TC:
        if (empty($ID["\163\x75\142\152\145\x63\164\x4e\141\x6d\x65"])) {
            goto tw;
        }
        $Jg = true;
        tw:
        el:
        foreach ($Ip as $wg) {
            if (!($kD || $Jg)) {
                goto nT;
            }
            if (!($mw = openssl_x509_parse("\55\x2d\x2d\x2d\55\102\105\107\111\116\40\103\105\122\x54\x49\x46\x49\103\101\124\x45\x2d\55\x2d\55\x2d\xa" . chunk_split($wg, 64, "\xa") . "\55\x2d\x2d\x2d\55\x45\x4e\x44\40\x43\x45\x52\124\111\106\111\103\101\x54\x45\x2d\x2d\x2d\55\55\xa"))) {
                goto FK;
            }
            if (!($Jg && !empty($mw["\163\165\142\x6a\x65\x63\164"]))) {
                goto C5;
            }
            if (is_array($mw["\163\x75\142\x6a\145\x63\x74"])) {
                goto YW;
            }
            $Fb = $mw["\x69\x73\163\165\145\162"];
            goto vM;
            YW:
            $ga = array();
            foreach ($mw["\x73\165\142\152\x65\143\164"] as $XC => $wE) {
                if (is_array($wE)) {
                    goto uS;
                }
                array_unshift($ga, "{$XC}\x3d{$wE}");
                goto hN;
                uS:
                foreach ($wE as $LK) {
                    array_unshift($ga, "{$XC}\75{$LK}");
                    NJ:
                }
                Ap:
                hN:
                N1:
            }
            B4:
            $Fb = implode("\54", $ga);
            vM:
            $YF = $NJ->createElementNS(self::XMLDSIGNS, $fI . "\130\65\x30\x39\x53\x75\142\152\x65\x63\x74\x4e\x61\155\145", $Fb);
            $rI->appendChild($YF);
            C5:
            if (!($kD && !empty($mw["\x69\163\x73\x75\x65\162"]) && !empty($mw["\x73\145\162\x69\141\154\x4e\165\x6d\142\x65\x72"]))) {
                goto Gg;
            }
            if (is_array($mw["\x69\163\x73\165\145\x72"])) {
                goto NC;
            }
            $qt = $mw["\x69\x73\163\165\x65\162"];
            goto ZM;
            NC:
            $ga = array();
            foreach ($mw["\151\x73\163\165\145\x72"] as $XC => $wE) {
                array_unshift($ga, "{$XC}\75{$wE}");
                J1:
            }
            fv:
            $qt = implode("\x2c", $ga);
            ZM:
            $Y7 = $NJ->createElementNS(self::XMLDSIGNS, $fI . "\130\x35\60\71\x49\x73\x73\x75\145\162\123\x65\x72\151\141\154");
            $rI->appendChild($Y7);
            $LB = $NJ->createElementNS(self::XMLDSIGNS, $fI . "\130\x35\x30\x39\111\x73\163\165\145\x72\x4e\x61\155\145", $qt);
            $Y7->appendChild($LB);
            $LB = $NJ->createElementNS(self::XMLDSIGNS, $fI . "\130\65\x30\71\x53\145\162\x69\x61\x6c\x4e\165\x6d\x62\145\x72", $mw["\x73\x65\x72\x69\141\x6c\x4e\165\155\x62\145\162"]);
            $Y7->appendChild($LB);
            Gg:
            FK:
            nT:
            $Lp = $NJ->createElementNS(self::XMLDSIGNS, $fI . "\x58\65\x30\x39\103\145\x72\x74\151\x66\151\143\141\164\x65", $wg);
            $rI->appendChild($Lp);
            Ev:
        }
        f3:
    }
    public function add509Cert($lK, $wp = true, $ZO = false, $ID = null)
    {
        if (!($VM = $this->getXPathObj())) {
            goto DI;
        }
        self::staticAdd509Cert($this->sigNode, $lK, $wp, $ZO, $VM, $ID);
        DI:
    }
    public function appendToKeyInfo($p8)
    {
        $Cw = $this->sigNode;
        $NJ = $Cw->ownerDocument;
        $VM = $this->getXPathObj();
        if (!empty($VM)) {
            goto cB;
        }
        $VM = new DOMXPath($Cw->ownerDocument);
        $VM->registerNamespace("\x73\145\143\144\x73\x69\147", self::XMLDSIGNS);
        cB:
        $wO = "\x2e\57\163\145\x63\x64\163\x69\x67\72\x4b\x65\x79\111\x6e\x66\x6f";
        $BC = $VM->query($wO, $Cw);
        $vh = $BC->item(0);
        if ($vh) {
            goto KV;
        }
        $fI = '';
        $Cz = $Cw->lookupPrefix(self::XMLDSIGNS);
        if (empty($Cz)) {
            goto Ld;
        }
        $fI = $Cz . "\72";
        Ld:
        $UR = false;
        $vh = $NJ->createElementNS(self::XMLDSIGNS, $fI . "\x4b\x65\x79\x49\156\x66\x6f");
        $wO = "\x2e\x2f\163\x65\x63\144\x73\x69\x67\x3a\117\x62\152\145\143\x74";
        $BC = $VM->query($wO, $Cw);
        if (!($Ju = $BC->item(0))) {
            goto F_;
        }
        $Ju->parentNode->insertBefore($vh, $Ju);
        $UR = true;
        F_:
        if ($UR) {
            goto Hl;
        }
        $Cw->appendChild($vh);
        Hl:
        KV:
        $vh->appendChild($p8);
        return $vh;
    }
    public function getValidatedNodes()
    {
        return $this->validatedNodes;
    }
}
