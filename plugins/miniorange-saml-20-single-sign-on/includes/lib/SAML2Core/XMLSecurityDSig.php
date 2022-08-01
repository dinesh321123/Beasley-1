<?php


namespace RobRichards\XMLSecLibs;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Exception;
use RobRichards\XMLSecLibs\Utils\XPath;
class XMLSecurityDSig
{
    const XMLDSIGNS = "\150\164\164\160\72\x2f\57\x77\x77\167\x2e\167\63\56\x6f\162\x67\57\x32\60\60\60\x2f\60\71\57\x78\155\x6c\x64\163\151\147\x23";
    const SHA1 = "\150\164\164\x70\72\57\57\x77\x77\167\x2e\x77\x33\56\157\x72\x67\x2f\62\60\60\x30\57\x30\71\57\x78\x6d\154\x64\163\x69\x67\x23\163\x68\141\61";
    const SHA256 = "\150\164\x74\x70\72\x2f\x2f\x77\x77\167\56\167\63\x2e\x6f\x72\x67\x2f\62\x30\x30\61\x2f\x30\64\57\170\155\154\145\x6e\x63\43\163\x68\x61\x32\x35\x36";
    const SHA384 = "\x68\x74\x74\x70\x3a\57\x2f\167\167\167\x2e\x77\63\56\157\x72\147\57\x32\x30\x30\61\x2f\x30\64\x2f\x78\x6d\154\x64\163\x69\x67\55\x6d\157\x72\145\x23\163\150\141\x33\x38\x34";
    const SHA512 = "\x68\164\x74\x70\x3a\x2f\x2f\x77\167\167\56\x77\x33\x2e\x6f\x72\x67\57\62\60\x30\x31\x2f\x30\64\57\170\x6d\154\145\156\x63\x23\163\150\x61\65\x31\62";
    const RIPEMD160 = "\x68\164\164\x70\x3a\57\x2f\x77\167\167\x2e\167\63\56\x6f\x72\147\x2f\x32\60\60\x31\x2f\60\x34\x2f\170\155\154\x65\x6e\143\43\162\x69\x70\x65\155\x64\61\x36\60";
    const C14N = "\x68\164\x74\x70\x3a\x2f\x2f\167\167\x77\56\167\63\x2e\x6f\x72\147\57\124\122\x2f\62\x30\60\61\57\x52\x45\103\x2d\x78\x6d\x6c\x2d\x63\61\64\x6e\x2d\x32\60\x30\61\x30\63\61\65";
    const C14N_COMMENTS = "\150\164\x74\x70\72\x2f\57\x77\167\x77\x2e\167\x33\56\x6f\x72\x67\57\124\x52\57\x32\60\60\61\57\x52\x45\x43\x2d\x78\155\x6c\x2d\x63\x31\64\156\x2d\62\x30\60\61\x30\x33\x31\65\x23\127\x69\x74\x68\103\157\x6d\155\x65\156\x74\163";
    const EXC_C14N = "\150\x74\164\x70\72\x2f\x2f\167\x77\167\56\167\x33\56\157\162\x67\57\x32\60\60\x31\x2f\61\x30\x2f\x78\155\154\x2d\x65\x78\x63\x2d\143\61\x34\156\x23";
    const EXC_C14N_COMMENTS = "\150\x74\x74\x70\x3a\57\57\167\167\x77\x2e\x77\63\x2e\157\162\147\x2f\x32\60\60\x31\x2f\x31\60\x2f\x78\x6d\154\x2d\x65\x78\143\55\x63\x31\x34\156\43\127\151\x74\x68\x43\157\155\155\x65\x6e\x74\x73";
    const template = "\x3c\144\x73\x3a\123\x69\147\156\141\x74\165\x72\x65\x20\170\x6d\x6c\156\x73\72\x64\x73\75\42\x68\x74\x74\x70\72\x2f\x2f\x77\167\x77\x2e\x77\63\x2e\157\162\x67\57\62\60\60\x30\57\60\71\x2f\170\155\x6c\144\163\x69\x67\43\42\x3e\xa\40\x20\74\144\x73\72\123\151\147\156\x65\x64\x49\156\146\x6f\x3e\12\x20\40\x20\40\74\x64\163\x3a\x53\x69\147\156\x61\164\x75\x72\x65\115\x65\x74\150\157\x64\x20\x2f\76\12\x20\40\x3c\57\144\x73\72\x53\x69\x67\156\145\x64\111\156\146\x6f\x3e\12\74\x2f\x64\163\72\123\151\147\156\141\x74\165\x72\145\x3e";
    const BASE_TEMPLATE = "\x3c\x53\151\147\x6e\141\164\x75\162\x65\40\x78\x6d\x6c\x6e\x73\x3d\42\x68\164\164\160\72\57\x2f\167\167\167\56\167\x33\x2e\x6f\x72\147\57\x32\x30\x30\x30\x2f\x30\71\x2f\x78\x6d\154\144\163\151\147\x23\x22\x3e\xa\40\40\x3c\123\151\x67\156\145\x64\x49\156\x66\x6f\x3e\12\x20\40\40\x20\x3c\123\x69\x67\x6e\141\x74\x75\162\x65\x4d\145\164\150\x6f\x64\x20\x2f\76\xa\40\40\x3c\x2f\x53\151\x67\156\145\x64\x49\156\x66\157\76\12\74\x2f\123\151\147\x6e\x61\164\165\x72\x65\x3e";
    public $sigNode = null;
    public $idKeys = array();
    public $idNS = array();
    private $signedInfo = null;
    private $xPathCtx = null;
    private $canonicalMethod = null;
    private $prefix = '';
    private $searchpfx = "\x73\145\143\x64\163\x69\x67";
    private $validatedNodes = null;
    public function __construct($x0 = "\144\x73")
    {
        $gl = self::BASE_TEMPLATE;
        if (empty($x0)) {
            goto jQ8;
        }
        $this->prefix = $x0 . "\72";
        $rx = array("\74\x53", "\x3c\x2f\x53", "\170\x6d\154\x6e\x73\x3d");
        $ao = array("\74{$x0}\x3a\123", "\74\57{$x0}\x3a\x53", "\x78\x6d\x6c\x6e\x73\x3a{$x0}\75");
        $gl = str_replace($rx, $ao, $gl);
        jQ8:
        $pF = new DOMDocument();
        $pF->loadXML($gl);
        $this->sigNode = $pF->documentElement;
    }
    private function resetXPathObj()
    {
        $this->xPathCtx = null;
    }
    private function getXPathObj()
    {
        if (!(empty($this->xPathCtx) && !empty($this->sigNode))) {
            goto xiS;
        }
        $vV = new DOMXPath($this->sigNode->ownerDocument);
        $vV->registerNamespace("\x73\x65\143\144\163\x69\147", self::XMLDSIGNS);
        $this->xPathCtx = $vV;
        xiS:
        return $this->xPathCtx;
    }
    public static function generateGUID($x0 = "\x70\x66\170")
    {
        $uC = md5(uniqid(mt_rand(), true));
        $bu = $x0 . substr($uC, 0, 8) . "\x2d" . substr($uC, 8, 4) . "\x2d" . substr($uC, 12, 4) . "\55" . substr($uC, 16, 4) . "\x2d" . substr($uC, 20, 12);
        return $bu;
    }
    public static function generate_GUID($x0 = "\x70\x66\170")
    {
        return self::generateGUID($x0);
    }
    public function locateSignature($DO, $jX = 0)
    {
        if ($DO instanceof DOMDocument) {
            goto T_c;
        }
        $li = $DO->ownerDocument;
        goto Nf6;
        T_c:
        $li = $DO;
        Nf6:
        if (!$li) {
            goto moM;
        }
        $vV = new DOMXPath($li);
        $vV->registerNamespace("\x73\145\x63\144\x73\151\x67", self::XMLDSIGNS);
        $wQ = "\x2e\x2f\57\x73\145\143\144\x73\x69\147\x3a\x53\x69\x67\x6e\141\164\165\x72\145";
        $Bm = $vV->query($wQ, $DO);
        $this->sigNode = $Bm->item($jX);
        return $this->sigNode;
        moM:
        return null;
    }
    public function createNewSignNode($dC, $zF = null)
    {
        $li = $this->sigNode->ownerDocument;
        if (!is_null($zF)) {
            goto zwc;
        }
        $nC = $li->createElementNS(self::XMLDSIGNS, $this->prefix . $dC);
        goto Csg;
        zwc:
        $nC = $li->createElementNS(self::XMLDSIGNS, $this->prefix . $dC, $zF);
        Csg:
        return $nC;
    }
    public function setCanonicalMethod($tO)
    {
        switch ($tO) {
            case "\150\164\x74\160\x3a\57\57\x77\167\x77\x2e\x77\x33\x2e\x6f\x72\147\x2f\x54\122\57\x32\60\60\61\57\x52\105\x43\55\170\155\154\x2d\x63\x31\64\156\55\x32\x30\x30\61\x30\63\61\65":
            case "\150\164\164\x70\x3a\57\x2f\x77\x77\167\56\x77\63\56\x6f\x72\x67\x2f\x54\x52\57\62\60\x30\61\x2f\x52\105\103\55\170\155\x6c\x2d\x63\61\64\x6e\x2d\x32\60\60\61\x30\x33\x31\x35\x23\127\x69\x74\x68\x43\157\x6d\155\145\x6e\x74\163":
            case "\150\x74\x74\x70\72\57\57\x77\167\x77\56\167\63\56\x6f\x72\x67\57\x32\60\x30\61\57\x31\60\57\170\x6d\x6c\x2d\x65\170\x63\55\x63\x31\x34\x6e\43":
            case "\150\x74\x74\x70\x3a\x2f\x2f\167\167\167\x2e\167\63\56\x6f\162\147\x2f\x32\x30\x30\61\x2f\x31\x30\x2f\x78\155\x6c\55\x65\x78\x63\x2d\143\61\64\156\x23\x57\x69\x74\150\x43\x6f\x6d\x6d\145\x6e\x74\x73":
                $this->canonicalMethod = $tO;
                goto hMr;
            default:
                throw new Exception("\111\x6e\x76\141\x6c\x69\144\x20\103\141\x6e\157\x6e\151\x63\141\154\x20\x4d\145\164\150\157\144");
        }
        l8e:
        hMr:
        if (!($vV = $this->getXPathObj())) {
            goto D2N;
        }
        $wQ = "\56\x2f" . $this->searchpfx . "\72\x53\151\x67\x6e\145\x64\111\x6e\146\x6f";
        $Bm = $vV->query($wQ, $this->sigNode);
        if (!($D6 = $Bm->item(0))) {
            goto nZd;
        }
        $wQ = "\56\x2f" . $this->searchpfx . "\103\x61\156\157\156\151\143\141\x6c\151\x7a\141\164\x69\157\x6e\115\145\164\150\157\x64";
        $Bm = $vV->query($wQ, $D6);
        if ($U7 = $Bm->item(0)) {
            goto rXi;
        }
        $U7 = $this->createNewSignNode("\x43\x61\x6e\x6f\x6e\151\x63\141\x6c\x69\x7a\x61\164\151\157\156\115\x65\x74\150\x6f\x64");
        $D6->insertBefore($U7, $D6->firstChild);
        rXi:
        $U7->setAttribute("\101\x6c\147\x6f\x72\151\x74\x68\x6d", $this->canonicalMethod);
        nZd:
        D2N:
    }
    private function canonicalizeData($nC, $b3, $pJ = null, $at = null)
    {
        $GE = false;
        $Vu = false;
        switch ($b3) {
            case "\150\164\164\x70\72\x2f\57\x77\x77\x77\56\167\x33\x2e\157\x72\147\x2f\x54\x52\57\62\x30\x30\61\57\x52\105\x43\55\170\155\x6c\x2d\x63\61\64\156\x2d\x32\x30\x30\61\60\63\61\65":
                $GE = false;
                $Vu = false;
                goto U79;
            case "\x68\164\x74\160\72\57\x2f\x77\x77\x77\56\x77\63\x2e\x6f\x72\147\57\x54\x52\x2f\x32\60\x30\61\x2f\x52\105\103\55\x78\x6d\x6c\55\x63\61\64\x6e\55\62\60\x30\61\x30\x33\x31\65\43\x57\151\164\x68\103\x6f\x6d\x6d\145\156\x74\x73":
                $Vu = true;
                goto U79;
            case "\x68\x74\164\x70\72\57\57\167\x77\x77\56\167\x33\x2e\x6f\x72\x67\57\x32\x30\x30\61\x2f\61\x30\57\x78\155\154\x2d\x65\170\x63\x2d\x63\x31\x34\156\x23":
                $GE = true;
                goto U79;
            case "\x68\164\x74\x70\72\x2f\57\x77\x77\167\56\x77\63\56\157\162\147\57\x32\x30\x30\x31\57\61\x30\57\170\155\x6c\55\145\170\143\x2d\x63\61\x34\156\43\x57\151\164\150\x43\x6f\x6d\x6d\x65\x6e\x74\x73":
                $GE = true;
                $Vu = true;
                goto U79;
        }
        DsN:
        U79:
        if (!(is_null($pJ) && $nC instanceof DOMNode && $nC->ownerDocument !== null && $nC->isSameNode($nC->ownerDocument->documentElement))) {
            goto Chm;
        }
        $XV = $nC;
        gby:
        if (!($iU = $XV->previousSibling)) {
            goto G_J;
        }
        if (!($iU->nodeType == XML_PI_NODE || $iU->nodeType == XML_COMMENT_NODE && $Vu)) {
            goto jI4;
        }
        goto G_J;
        jI4:
        $XV = $iU;
        goto gby;
        G_J:
        if (!($iU == null)) {
            goto KHi;
        }
        $nC = $nC->ownerDocument;
        KHi:
        Chm:
        return $nC->C14N($GE, $Vu, $pJ, $at);
    }
    public function canonicalizeSignedInfo()
    {
        $li = $this->sigNode->ownerDocument;
        $b3 = null;
        if (!$li) {
            goto Rmp;
        }
        $vV = $this->getXPathObj();
        $wQ = "\56\57\x73\145\143\144\x73\x69\147\x3a\x53\x69\x67\156\x65\144\x49\156\x66\x6f";
        $Bm = $vV->query($wQ, $this->sigNode);
        if (!($vd = $Bm->item(0))) {
            goto p2a;
        }
        $wQ = "\56\57\163\x65\143\x64\x73\x69\147\x3a\x43\x61\x6e\157\156\x69\143\x61\154\151\172\141\x74\x69\x6f\x6e\x4d\x65\164\150\157\x64";
        $Bm = $vV->query($wQ, $vd);
        if (!($U7 = $Bm->item(0))) {
            goto ftK;
        }
        $b3 = $U7->getAttribute("\101\x6c\x67\157\x72\151\164\x68\155");
        ftK:
        $this->signedInfo = $this->canonicalizeData($vd, $b3);
        return $this->signedInfo;
        p2a:
        Rmp:
        return null;
    }
    public function calculateDigest($u5, $K9, $Pq = true)
    {
        switch ($u5) {
            case self::SHA1:
                $jj = "\x73\150\141\61";
                goto Ede;
            case self::SHA256:
                $jj = "\x73\150\141\62\x35\x36";
                goto Ede;
            case self::SHA384:
                $jj = "\x73\150\x61\x33\x38\x34";
                goto Ede;
            case self::SHA512:
                $jj = "\163\150\141\65\x31\x32";
                goto Ede;
            case self::RIPEMD160:
                $jj = "\162\x69\160\x65\155\x64\61\x36\x30";
                goto Ede;
            default:
                throw new Exception("\x43\x61\156\156\157\x74\x20\166\x61\154\x69\x64\141\x74\x65\40\144\x69\x67\x65\x73\x74\x3a\x20\125\156\x73\165\x70\160\x6f\x72\x74\145\x64\40\x41\154\147\x6f\162\x69\164\150\x6d\x20\74{$u5}\76");
        }
        iGy:
        Ede:
        $pf = hash($jj, $K9, true);
        if (!$Pq) {
            goto S8C;
        }
        $pf = base64_encode($pf);
        S8C:
        return $pf;
    }
    public function validateDigest($Ln, $K9)
    {
        $vV = new DOMXPath($Ln->ownerDocument);
        $vV->registerNamespace("\x73\x65\x63\144\163\151\147", self::XMLDSIGNS);
        $wQ = "\x73\x74\x72\x69\x6e\x67\x28\x2e\57\163\x65\143\144\163\151\147\x3a\104\151\147\145\163\164\115\x65\x74\150\157\144\57\100\x41\x6c\147\x6f\x72\151\x74\x68\155\51";
        $u5 = $vV->evaluate($wQ, $Ln);
        $d_ = $this->calculateDigest($u5, $K9, false);
        $wQ = "\163\x74\162\x69\156\147\50\56\57\163\x65\143\x64\x73\151\147\x3a\104\151\x67\x65\x73\x74\x56\141\154\165\x65\x29";
        $UP = $vV->evaluate($wQ, $Ln);
        return $d_ === base64_decode($UP);
    }
    public function processTransforms($Ln, $Di, $F5 = true)
    {
        $K9 = $Di;
        $vV = new DOMXPath($Ln->ownerDocument);
        $vV->registerNamespace("\163\145\143\144\x73\x69\x67", self::XMLDSIGNS);
        $wQ = "\56\x2f\x73\x65\x63\144\x73\x69\147\x3a\124\x72\141\x6e\x73\x66\x6f\162\155\x73\57\163\145\x63\144\x73\151\x67\x3a\124\x72\x61\x6e\163\146\157\162\155";
        $rE = $vV->query($wQ, $Ln);
        $Nn = "\150\164\x74\x70\72\57\57\x77\167\x77\x2e\167\63\x2e\157\162\x67\x2f\x54\x52\x2f\62\x30\60\61\x2f\122\105\x43\x2d\x78\155\154\55\143\61\64\156\x2d\62\x30\x30\61\x30\63\61\x35";
        $pJ = null;
        $at = null;
        foreach ($rE as $z7) {
            $ko = $z7->getAttribute("\101\x6c\147\157\162\x69\164\x68\155");
            switch ($ko) {
                case "\150\x74\164\x70\x3a\x2f\x2f\x77\x77\x77\x2e\x77\x33\x2e\157\x72\147\57\x32\x30\60\x31\57\61\x30\x2f\x78\155\154\55\x65\170\143\x2d\x63\x31\x34\156\x23":
                case "\150\x74\x74\x70\72\57\x2f\x77\167\167\56\167\x33\x2e\x6f\162\147\x2f\62\60\x30\61\57\x31\60\x2f\x78\x6d\154\55\145\x78\x63\x2d\x63\x31\64\156\43\127\151\164\x68\103\157\x6d\155\x65\156\x74\x73":
                    if (!$F5) {
                        goto BYD;
                    }
                    $Nn = $ko;
                    goto v2Q;
                    BYD:
                    $Nn = "\150\x74\164\160\72\x2f\57\167\x77\x77\56\167\63\x2e\x6f\162\147\57\x32\60\60\x31\57\x31\x30\57\x78\155\x6c\55\x65\x78\143\55\x63\61\x34\156\x23";
                    v2Q:
                    $nC = $z7->firstChild;
                    mn5:
                    if (!$nC) {
                        goto npU;
                    }
                    if (!($nC->localName == "\111\x6e\143\x6c\x75\163\x69\x76\145\116\x61\155\145\x73\x70\x61\143\x65\x73")) {
                        goto a_T;
                    }
                    if (!($NO = $nC->getAttribute("\120\x72\145\x66\151\x78\114\151\x73\x74"))) {
                        goto xta;
                    }
                    $kK = array();
                    $Gh = explode("\40", $NO);
                    foreach ($Gh as $NO) {
                        $kZ = trim($NO);
                        if (empty($kZ)) {
                            goto OAe;
                        }
                        $kK[] = $kZ;
                        OAe:
                        cBm:
                    }
                    kZu:
                    if (!(count($kK) > 0)) {
                        goto UwF;
                    }
                    $at = $kK;
                    UwF:
                    xta:
                    goto npU;
                    a_T:
                    $nC = $nC->nextSibling;
                    goto mn5;
                    npU:
                    goto yXW;
                case "\x68\164\x74\x70\x3a\x2f\x2f\167\167\x77\x2e\x77\x33\x2e\157\162\147\57\124\122\x2f\x32\60\x30\x31\x2f\122\105\x43\x2d\170\155\154\x2d\143\x31\x34\x6e\x2d\62\60\60\x31\x30\x33\x31\x35":
                case "\x68\x74\164\x70\x3a\x2f\57\x77\167\167\56\167\x33\56\157\162\x67\57\124\122\x2f\62\60\x30\61\x2f\122\105\x43\55\x78\x6d\x6c\55\x63\x31\64\x6e\55\62\60\60\61\60\63\x31\65\x23\127\151\x74\x68\x43\x6f\x6d\x6d\x65\x6e\x74\163":
                    if (!$F5) {
                        goto umc;
                    }
                    $Nn = $ko;
                    goto L54;
                    umc:
                    $Nn = "\150\164\x74\x70\x3a\x2f\x2f\167\167\167\x2e\x77\63\56\157\162\x67\57\x54\x52\x2f\x32\x30\60\x31\57\122\105\x43\x2d\170\x6d\x6c\55\x63\61\x34\x6e\55\x32\x30\60\61\x30\x33\61\65";
                    L54:
                    goto yXW;
                case "\150\164\x74\160\72\x2f\57\x77\x77\167\56\167\x33\x2e\157\162\147\x2f\x54\x52\x2f\61\71\x39\71\57\122\x45\103\55\x78\160\x61\x74\x68\55\61\71\71\x39\61\x31\x31\x36":
                    $nC = $z7->firstChild;
                    VzC:
                    if (!$nC) {
                        goto YZU;
                    }
                    if (!($nC->localName == "\130\120\141\x74\x68")) {
                        goto xCs;
                    }
                    $pJ = array();
                    $pJ["\161\165\145\x72\171"] = "\x28\56\57\x2f\56\x20\x7c\40\56\57\57\100\52\x20\174\40\x2e\x2f\x2f\156\141\x6d\x65\x73\x70\141\143\x65\x3a\72\52\51\133" . $nC->nodeValue . "\x5d";
                    $gt["\156\141\155\x65\x73\160\141\x63\145\163"] = array();
                    $kM = $vV->query("\56\57\x6e\141\x6d\x65\163\x70\x61\x63\x65\x3a\72\52", $nC);
                    foreach ($kM as $MX) {
                        if (!($MX->localName != "\170\x6d\154")) {
                            goto Neo;
                        }
                        $pJ["\156\x61\x6d\145\163\160\141\x63\145\x73"][$MX->localName] = $MX->nodeValue;
                        Neo:
                        Jh_:
                    }
                    P84:
                    goto YZU;
                    xCs:
                    $nC = $nC->nextSibling;
                    goto VzC;
                    YZU:
                    goto yXW;
            }
            C_h:
            yXW:
            pmG:
        }
        ZGq:
        if (!$K9 instanceof DOMNode) {
            goto E06;
        }
        $K9 = $this->canonicalizeData($Di, $Nn, $pJ, $at);
        E06:
        return $K9;
    }
    public function processRefNode($Ln)
    {
        $Pf = null;
        $F5 = true;
        if ($Zk = $Ln->getAttribute("\x55\x52\x49")) {
            goto BKr;
        }
        $F5 = false;
        $Pf = $Ln->ownerDocument;
        goto FQQ;
        BKr:
        $hW = parse_url($Zk);
        if (!empty($hW["\x70\141\164\150"])) {
            goto A6L;
        }
        if ($Es = $hW["\x66\x72\x61\147\x6d\x65\156\x74"]) {
            goto CU1;
        }
        $Pf = $Ln->ownerDocument;
        goto VAW;
        CU1:
        $F5 = false;
        $oR = new DOMXPath($Ln->ownerDocument);
        if (!($this->idNS && is_array($this->idNS))) {
            goto UPY;
        }
        foreach ($this->idNS as $mS => $EQ) {
            $oR->registerNamespace($mS, $EQ);
            PUe:
        }
        jiH:
        UPY:
        $TU = "\x40\111\144\x3d\42" . XPath::filterAttrValue($Es, XPath::DOUBLE_QUOTE) . "\42";
        if (!is_array($this->idKeys)) {
            goto r3_;
        }
        foreach ($this->idKeys as $mp) {
            $TU .= "\x20\x6f\x72\x20\100" . XPath::filterAttrName($mp) . "\75\x22" . XPath::filterAttrValue($Es, XPath::DOUBLE_QUOTE) . "\42";
            Bvi:
        }
        zmT:
        r3_:
        $wQ = "\x2f\x2f\x2a\x5b" . $TU . "\x5d";
        $Pf = $oR->query($wQ)->item(0);
        VAW:
        A6L:
        FQQ:
        $K9 = $this->processTransforms($Ln, $Pf, $F5);
        if ($this->validateDigest($Ln, $K9)) {
            goto Hy8;
        }
        return false;
        Hy8:
        if (!$Pf instanceof DOMNode) {
            goto ZII;
        }
        if (!empty($Es)) {
            goto Rxj;
        }
        $this->validatedNodes[] = $Pf;
        goto KSl;
        Rxj:
        $this->validatedNodes[$Es] = $Pf;
        KSl:
        ZII:
        return true;
    }
    public function getRefNodeID($Ln)
    {
        if (!($Zk = $Ln->getAttribute("\x55\x52\x49"))) {
            goto R5V;
        }
        $hW = parse_url($Zk);
        if (!empty($hW["\160\x61\164\150"])) {
            goto Yx4;
        }
        if (!($Es = $hW["\x66\162\141\x67\x6d\x65\156\164"])) {
            goto HFt;
        }
        return $Es;
        HFt:
        Yx4:
        R5V:
        return null;
    }
    public function getRefIDs()
    {
        $zZ = array();
        $vV = $this->getXPathObj();
        $wQ = "\x2e\x2f\x73\145\143\x64\163\151\147\x3a\123\x69\147\x6e\x65\144\111\156\x66\157\x2f\163\x65\143\144\163\x69\147\x3a\x52\145\x66\x65\162\145\156\143\145";
        $Bm = $vV->query($wQ, $this->sigNode);
        if (!($Bm->length == 0)) {
            goto BCI;
        }
        throw new Exception("\x52\x65\x66\145\162\x65\156\143\x65\40\x6e\x6f\144\x65\x73\40\156\157\x74\40\146\x6f\165\x6e\x64");
        BCI:
        foreach ($Bm as $Ln) {
            $zZ[] = $this->getRefNodeID($Ln);
            UWm:
        }
        m1h:
        return $zZ;
    }
    public function validateReference()
    {
        $zf = $this->sigNode->ownerDocument->documentElement;
        if ($zf->isSameNode($this->sigNode)) {
            goto Htj;
        }
        if (!($this->sigNode->parentNode != null)) {
            goto PHr;
        }
        $this->sigNode->parentNode->removeChild($this->sigNode);
        PHr:
        Htj:
        $vV = $this->getXPathObj();
        $wQ = "\x2e\57\163\145\143\144\163\x69\x67\72\123\151\x67\x6e\x65\x64\x49\156\x66\157\x2f\163\145\x63\x64\163\x69\x67\x3a\122\x65\x66\x65\162\x65\x6e\143\145";
        $Bm = $vV->query($wQ, $this->sigNode);
        if (!($Bm->length == 0)) {
            goto xGW;
        }
        throw new Exception("\122\145\x66\x65\x72\145\156\143\x65\40\156\x6f\144\145\x73\x20\156\157\164\40\x66\x6f\165\x6e\x64");
        xGW:
        $this->validatedNodes = array();
        foreach ($Bm as $Ln) {
            if ($this->processRefNode($Ln)) {
                goto nke;
            }
            $this->validatedNodes = null;
            throw new Exception("\x52\145\x66\145\162\145\x6e\143\x65\x20\166\141\154\x69\x64\x61\164\151\157\156\40\x66\141\151\154\x65\x64");
            nke:
            aU2:
        }
        q5e:
        return true;
    }
    private function addRefInternal($NA, $nC, $ko, $qI = null, $qK = null)
    {
        $x0 = null;
        $wU = null;
        $AN = "\111\x64";
        $aA = true;
        $Su = false;
        if (!is_array($qK)) {
            goto ziE;
        }
        $x0 = empty($qK["\x70\162\145\146\x69\x78"]) ? null : $qK["\x70\162\x65\x66\151\170"];
        $wU = empty($qK["\160\162\145\146\x69\x78\x5f\156\163"]) ? null : $qK["\x70\x72\x65\x66\151\x78\x5f\156\163"];
        $AN = empty($qK["\x69\x64\x5f\156\x61\155\145"]) ? "\x49\144" : $qK["\151\x64\x5f\156\141\x6d\145"];
        $aA = !isset($qK["\x6f\x76\145\x72\x77\162\x69\164\145"]) ? true : (bool) $qK["\x6f\166\145\162\x77\162\151\164\x65"];
        $Su = !isset($qK["\146\x6f\162\143\x65\x5f\x75\x72\x69"]) ? false : (bool) $qK["\x66\x6f\162\x63\x65\137\165\162\x69"];
        ziE:
        $oU = $AN;
        if (empty($x0)) {
            goto zej;
        }
        $oU = $x0 . "\x3a" . $oU;
        zej:
        $Ln = $this->createNewSignNode("\x52\145\x66\145\162\x65\156\x63\145");
        $NA->appendChild($Ln);
        if (!$nC instanceof DOMDocument) {
            goto P7E;
        }
        if ($Su) {
            goto PV9;
        }
        goto XkC;
        P7E:
        $Zk = null;
        if ($aA) {
            goto K50;
        }
        $Zk = $wU ? $nC->getAttributeNS($wU, $AN) : $nC->getAttribute($AN);
        K50:
        if (!empty($Zk)) {
            goto mWn;
        }
        $Zk = self::generateGUID();
        $nC->setAttributeNS($wU, $oU, $Zk);
        mWn:
        $Ln->setAttribute("\x55\x52\x49", "\x23" . $Zk);
        goto XkC;
        PV9:
        $Ln->setAttribute("\x55\122\x49", '');
        XkC:
        $ei = $this->createNewSignNode("\124\x72\x61\156\163\146\x6f\x72\x6d\163");
        $Ln->appendChild($ei);
        if (is_array($qI)) {
            goto kx4;
        }
        if (!empty($this->canonicalMethod)) {
            goto pry;
        }
        goto RO1;
        kx4:
        foreach ($qI as $z7) {
            $Pt = $this->createNewSignNode("\x54\162\x61\156\x73\x66\157\162\x6d");
            $ei->appendChild($Pt);
            if (is_array($z7) && !empty($z7["\x68\x74\x74\x70\x3a\x2f\x2f\167\x77\167\56\x77\63\56\x6f\x72\147\57\124\122\57\x31\71\71\x39\57\x52\105\103\55\x78\x70\x61\164\150\55\61\x39\71\x39\x31\x31\x31\x36"]) && !empty($z7["\x68\164\x74\160\72\57\57\x77\x77\x77\56\x77\x33\56\157\162\147\57\x54\122\x2f\x31\71\x39\71\x2f\122\105\x43\x2d\170\160\141\x74\150\x2d\x31\71\71\x39\x31\61\61\x36"]["\x71\165\x65\162\x79"])) {
                goto Tbn;
            }
            $Pt->setAttribute("\x41\x6c\x67\x6f\x72\151\x74\150\155", $z7);
            goto rc_;
            Tbn:
            $Pt->setAttribute("\x41\154\147\x6f\162\x69\x74\x68\x6d", "\x68\x74\x74\x70\x3a\x2f\x2f\167\167\167\56\167\63\56\x6f\x72\147\x2f\124\122\57\61\x39\x39\71\x2f\122\105\103\55\170\x70\x61\164\x68\55\x31\71\x39\x39\61\61\x31\x36");
            $Gu = $this->createNewSignNode("\130\x50\141\x74\150", $z7["\150\x74\164\x70\72\57\57\167\167\x77\x2e\167\63\x2e\157\x72\147\57\x54\x52\x2f\61\x39\x39\x39\57\122\x45\103\55\170\160\141\x74\150\55\x31\71\x39\71\x31\x31\61\x36"]["\161\x75\145\162\x79"]);
            $Pt->appendChild($Gu);
            if (empty($z7["\150\164\x74\x70\72\57\57\x77\167\167\56\167\x33\x2e\x6f\x72\147\x2f\x54\x52\57\x31\x39\71\71\x2f\122\105\x43\x2d\x78\x70\x61\x74\150\55\61\71\x39\x39\x31\61\x31\x36"]["\156\x61\x6d\145\163\x70\141\143\x65\163"])) {
                goto JaH;
            }
            foreach ($z7["\150\x74\164\x70\72\x2f\57\x77\x77\x77\56\x77\x33\x2e\x6f\162\147\x2f\x54\122\x2f\61\71\x39\x39\x2f\x52\x45\x43\55\170\160\141\x74\x68\x2d\61\71\71\x39\61\61\61\66"]["\x6e\141\155\145\x73\160\x61\x63\x65\x73"] as $x0 => $z0) {
                $Gu->setAttributeNS("\150\x74\x74\x70\72\x2f\x2f\167\167\x77\x2e\x77\63\56\x6f\x72\147\57\x32\60\x30\60\x2f\x78\155\x6c\x6e\163\57", "\170\x6d\x6c\156\163\72{$x0}", $z0);
                yEi:
            }
            ruD:
            JaH:
            rc_:
            cK7:
        }
        udO:
        goto RO1;
        pry:
        $Pt = $this->createNewSignNode("\124\162\x61\156\163\146\157\162\155");
        $ei->appendChild($Pt);
        $Pt->setAttribute("\x41\154\147\157\162\x69\x74\150\x6d", $this->canonicalMethod);
        RO1:
        $gA = $this->processTransforms($Ln, $nC);
        $d_ = $this->calculateDigest($ko, $gA);
        $Cg = $this->createNewSignNode("\104\151\x67\145\x73\x74\x4d\x65\164\150\157\144");
        $Ln->appendChild($Cg);
        $Cg->setAttribute("\101\154\147\157\162\x69\164\150\x6d", $ko);
        $UP = $this->createNewSignNode("\x44\x69\147\x65\163\x74\x56\x61\x6c\x75\145", $d_);
        $Ln->appendChild($UP);
    }
    public function addReference($nC, $ko, $qI = null, $qK = null)
    {
        if (!($vV = $this->getXPathObj())) {
            goto YVr;
        }
        $wQ = "\x2e\x2f\x73\145\143\x64\x73\x69\x67\72\x53\x69\147\156\x65\144\111\x6e\x66\x6f";
        $Bm = $vV->query($wQ, $this->sigNode);
        if (!($D3 = $Bm->item(0))) {
            goto d1Y;
        }
        $this->addRefInternal($D3, $nC, $ko, $qI, $qK);
        d1Y:
        YVr:
    }
    public function addReferenceList($Uu, $ko, $qI = null, $qK = null)
    {
        if (!($vV = $this->getXPathObj())) {
            goto Oj_;
        }
        $wQ = "\56\57\163\145\x63\144\163\151\x67\x3a\123\151\x67\156\x65\x64\x49\156\x66\157";
        $Bm = $vV->query($wQ, $this->sigNode);
        if (!($D3 = $Bm->item(0))) {
            goto vT9;
        }
        foreach ($Uu as $nC) {
            $this->addRefInternal($D3, $nC, $ko, $qI, $qK);
            U0E:
        }
        as4:
        vT9:
        Oj_:
    }
    public function addObject($K9, $or = null, $ck = null)
    {
        $tH = $this->createNewSignNode("\117\142\152\145\x63\164");
        $this->sigNode->appendChild($tH);
        if (empty($or)) {
            goto ugU;
        }
        $tH->setAttribute("\115\151\155\x65\x54\x79\160\x65", $or);
        ugU:
        if (empty($ck)) {
            goto UAt;
        }
        $tH->setAttribute("\x45\156\143\x6f\x64\151\156\147", $ck);
        UAt:
        if ($K9 instanceof DOMElement) {
            goto n0m;
        }
        $O_ = $this->sigNode->ownerDocument->createTextNode($K9);
        goto sRp;
        n0m:
        $O_ = $this->sigNode->ownerDocument->importNode($K9, true);
        sRp:
        $tH->appendChild($O_);
        return $tH;
    }
    public function locateKey($nC = null)
    {
        if (!empty($nC)) {
            goto AQ3;
        }
        $nC = $this->sigNode;
        AQ3:
        if ($nC instanceof DOMNode) {
            goto XkI;
        }
        return null;
        XkI:
        if (!($li = $nC->ownerDocument)) {
            goto ZBf;
        }
        $vV = new DOMXPath($li);
        $vV->registerNamespace("\163\145\143\x64\x73\x69\x67", self::XMLDSIGNS);
        $wQ = "\x73\164\162\151\156\x67\x28\x2e\x2f\163\x65\x63\144\x73\x69\147\x3a\123\x69\147\156\x65\144\x49\x6e\146\157\57\163\145\x63\x64\x73\151\x67\72\123\x69\x67\156\141\x74\x75\x72\145\115\x65\x74\150\157\144\57\x40\x41\154\x67\157\x72\x69\164\150\x6d\51";
        $ko = $vV->evaluate($wQ, $nC);
        if (!$ko) {
            goto k4X;
        }
        try {
            $RS = new XMLSecurityKey($ko, array("\x74\171\160\x65" => "\x70\x75\142\154\151\x63"));
        } catch (Exception $ZE) {
            return null;
        }
        return $RS;
        k4X:
        ZBf:
        return null;
    }
    public function verify($RS)
    {
        $li = $this->sigNode->ownerDocument;
        $vV = new DOMXPath($li);
        $vV->registerNamespace("\163\x65\x63\144\163\x69\147", self::XMLDSIGNS);
        $wQ = "\163\164\162\151\x6e\x67\50\x2e\57\163\x65\143\144\x73\x69\147\72\123\151\147\156\141\164\x75\162\145\x56\141\x6c\x75\x65\x29";
        $RX = $vV->evaluate($wQ, $this->sigNode);
        if (!empty($RX)) {
            goto Efd;
        }
        throw new Exception("\x55\156\x61\142\154\145\x20\164\x6f\x20\x6c\157\143\x61\x74\x65\40\x53\x69\x67\156\141\164\x75\162\x65\x56\141\x6c\x75\x65");
        Efd:
        return $RS->verifySignature($this->signedInfo, base64_decode($RX));
    }
    public function signData($RS, $K9)
    {
        return $RS->signData($K9);
    }
    public function sign($RS, $Wb = null)
    {
        if (!($Wb != null)) {
            goto tZT;
        }
        $this->resetXPathObj();
        $this->appendSignature($Wb);
        $this->sigNode = $Wb->lastChild;
        tZT:
        if (!($vV = $this->getXPathObj())) {
            goto uz7;
        }
        $wQ = "\56\57\163\145\143\x64\163\151\147\72\x53\151\x67\x6e\145\x64\111\x6e\x66\x6f";
        $Bm = $vV->query($wQ, $this->sigNode);
        if (!($D3 = $Bm->item(0))) {
            goto WVR;
        }
        $wQ = "\x2e\57\x73\145\143\x64\x73\x69\x67\x3a\123\x69\147\156\x61\x74\165\162\145\x4d\x65\164\x68\x6f\144";
        $Bm = $vV->query($wQ, $D3);
        $cn = $Bm->item(0);
        $cn->setAttribute("\101\154\x67\x6f\162\151\x74\x68\x6d", $RS->type);
        $K9 = $this->canonicalizeData($D3, $this->canonicalMethod);
        $RX = base64_encode($this->signData($RS, $K9));
        $uQ = $this->createNewSignNode("\x53\x69\x67\156\141\164\165\x72\145\126\141\x6c\x75\x65", $RX);
        if ($Xl = $D3->nextSibling) {
            goto am0;
        }
        $this->sigNode->appendChild($uQ);
        goto ZD2;
        am0:
        $Xl->parentNode->insertBefore($uQ, $Xl);
        ZD2:
        WVR:
        uz7:
    }
    public function appendCert()
    {
    }
    public function appendKey($RS, $TE = null)
    {
        $RS->serializeKey($TE);
    }
    public function insertSignature($nC, $BE = null)
    {
        $uz = $nC->ownerDocument;
        $wb = $uz->importNode($this->sigNode, true);
        if ($BE == null) {
            goto he_;
        }
        return $nC->insertBefore($wb, $BE);
        goto Jzn;
        he_:
        return $nC->insertBefore($wb);
        Jzn:
    }
    public function appendSignature($nw, $dL = false)
    {
        $BE = $dL ? $nw->firstChild : null;
        return $this->insertSignature($nw, $BE);
    }
    public static function get509XCert($Go, $XY = true)
    {
        $dB = self::staticGet509XCerts($Go, $XY);
        if (empty($dB)) {
            goto Luh;
        }
        return $dB[0];
        Luh:
        return '';
    }
    public static function staticGet509XCerts($dB, $XY = true)
    {
        if ($XY) {
            goto Rja;
        }
        return array($dB);
        goto IXu;
        Rja:
        $K9 = '';
        $iP = array();
        $WY = explode("\12", $dB);
        $Ny = false;
        foreach ($WY as $CB) {
            if (!$Ny) {
                goto pPJ;
            }
            if (!(strncmp($CB, "\x2d\55\55\55\x2d\105\x4e\104\x20\103\x45\122\124\x49\106\111\x43\x41\x54\x45", 20) == 0)) {
                goto rTm;
            }
            $Ny = false;
            $iP[] = $K9;
            $K9 = '';
            goto Ow4;
            rTm:
            $K9 .= trim($CB);
            goto Yuf;
            pPJ:
            if (!(strncmp($CB, "\55\x2d\55\55\55\x42\105\x47\111\116\40\103\105\122\124\111\106\111\103\101\x54\x45", 22) == 0)) {
                goto iyG;
            }
            $Ny = true;
            iyG:
            Yuf:
            Ow4:
        }
        DVN:
        return $iP;
        IXu:
    }
    public static function staticAdd509Cert($m3, $Go, $XY = true, $N7 = false, $vV = null, $qK = null)
    {
        if (!$N7) {
            goto mII;
        }
        $Go = file_get_contents($Go);
        mII:
        if ($m3 instanceof DOMElement) {
            goto zY0;
        }
        throw new Exception("\x49\x6e\166\141\154\151\x64\40\160\x61\x72\x65\x6e\x74\40\x4e\x6f\144\x65\40\x70\141\162\x61\155\145\164\145\x72");
        zY0:
        $sQ = $m3->ownerDocument;
        if (!empty($vV)) {
            goto dnV;
        }
        $vV = new DOMXPath($m3->ownerDocument);
        $vV->registerNamespace("\x73\145\x63\x64\x73\151\x67", self::XMLDSIGNS);
        dnV:
        $wQ = "\x2e\x2f\163\145\143\x64\x73\151\x67\x3a\113\x65\x79\111\x6e\x66\157";
        $Bm = $vV->query($wQ, $m3);
        $lV = $Bm->item(0);
        $I1 = '';
        if (!$lV) {
            goto dls;
        }
        $NO = $lV->lookupPrefix(self::XMLDSIGNS);
        if (empty($NO)) {
            goto FMD;
        }
        $I1 = $NO . "\x3a";
        FMD:
        goto VOw;
        dls:
        $NO = $m3->lookupPrefix(self::XMLDSIGNS);
        if (empty($NO)) {
            goto fMO;
        }
        $I1 = $NO . "\x3a";
        fMO:
        $lg = false;
        $lV = $sQ->createElementNS(self::XMLDSIGNS, $I1 . "\x4b\145\x79\111\x6e\146\157");
        $wQ = "\x2e\57\x73\x65\x63\x64\163\x69\147\x3a\x4f\142\152\145\143\x74";
        $Bm = $vV->query($wQ, $m3);
        if (!($Hp = $Bm->item(0))) {
            goto YGY;
        }
        $Hp->parentNode->insertBefore($lV, $Hp);
        $lg = true;
        YGY:
        if ($lg) {
            goto S8g;
        }
        $m3->appendChild($lV);
        S8g:
        VOw:
        $dB = self::staticGet509XCerts($Go, $XY);
        $Q5 = $sQ->createElementNS(self::XMLDSIGNS, $I1 . "\130\65\60\71\104\141\x74\141");
        $lV->appendChild($Q5);
        $cD = false;
        $al = false;
        if (!is_array($qK)) {
            goto xJK;
        }
        if (empty($qK["\151\x73\163\165\x65\x72\123\x65\x72\151\x61\x6c"])) {
            goto NAk;
        }
        $cD = true;
        NAk:
        if (empty($qK["\x73\165\x62\x6a\145\143\164\x4e\141\x6d\x65"])) {
            goto MyD;
        }
        $al = true;
        MyD:
        xJK:
        foreach ($dB as $FL) {
            if (!($cD || $al)) {
                goto iVt;
            }
            if (!($jO = openssl_x509_parse("\x2d\x2d\55\x2d\55\102\x45\x47\x49\x4e\40\x43\105\x52\x54\x49\x46\x49\103\x41\124\x45\x2d\55\55\x2d\55\12" . chunk_split($FL, 64, "\12") . "\55\55\55\x2d\55\105\x4e\104\x20\103\105\122\124\111\x46\111\103\x41\124\105\55\x2d\55\55\x2d\12"))) {
                goto b7t;
            }
            if (!($al && !empty($jO["\x73\165\142\x6a\x65\x63\x74"]))) {
                goto zEL;
            }
            if (is_array($jO["\x73\x75\x62\x6a\x65\143\x74"])) {
                goto chH;
            }
            $xr = $jO["\151\x73\163\165\145\x72"];
            goto C01;
            chH:
            $CH = array();
            foreach ($jO["\x73\165\x62\152\145\x63\x74"] as $Z1 => $zF) {
                if (is_array($zF)) {
                    goto bxM;
                }
                array_unshift($CH, "{$Z1}\75{$zF}");
                goto Zou;
                bxM:
                foreach ($zF as $Cc) {
                    array_unshift($CH, "{$Z1}\75{$Cc}");
                    Vsk:
                }
                bss:
                Zou:
                os4:
            }
            koz:
            $xr = implode("\54", $CH);
            C01:
            $lD = $sQ->createElementNS(self::XMLDSIGNS, $I1 . "\130\x35\60\71\x53\165\142\152\x65\143\x74\x4e\x61\x6d\145", $xr);
            $Q5->appendChild($lD);
            zEL:
            if (!($cD && !empty($jO["\x69\x73\x73\x75\145\x72"]) && !empty($jO["\x73\x65\162\151\x61\x6c\x4e\x75\155\142\x65\x72"]))) {
                goto dQA;
            }
            if (is_array($jO["\x69\x73\163\165\145\162"])) {
                goto amv;
            }
            $tE = $jO["\151\163\163\x75\145\162"];
            goto dqx;
            amv:
            $CH = array();
            foreach ($jO["\151\163\163\165\145\x72"] as $Z1 => $zF) {
                array_unshift($CH, "{$Z1}\x3d{$zF}");
                xmd:
            }
            Ho1:
            $tE = implode("\x2c", $CH);
            dqx:
            $Kk = $sQ->createElementNS(self::XMLDSIGNS, $I1 . "\130\x35\60\71\x49\x73\163\165\145\x72\123\x65\x72\x69\141\x6c");
            $Q5->appendChild($Kk);
            $CR = $sQ->createElementNS(self::XMLDSIGNS, $I1 . "\x58\x35\60\x39\111\x73\x73\165\145\162\x4e\141\x6d\x65", $tE);
            $Kk->appendChild($CR);
            $CR = $sQ->createElementNS(self::XMLDSIGNS, $I1 . "\x58\65\x30\71\123\x65\162\x69\141\x6c\116\165\155\x62\145\x72", $jO["\163\x65\x72\x69\141\x6c\116\165\155\x62\145\162"]);
            $Kk->appendChild($CR);
            dQA:
            b7t:
            iVt:
            $EP = $sQ->createElementNS(self::XMLDSIGNS, $I1 . "\130\65\60\71\103\x65\x72\164\151\146\151\143\141\164\x65", $FL);
            $Q5->appendChild($EP);
            TZq:
        }
        gbm:
    }
    public function add509Cert($Go, $XY = true, $N7 = false, $qK = null)
    {
        if (!($vV = $this->getXPathObj())) {
            goto rjN;
        }
        self::staticAdd509Cert($this->sigNode, $Go, $XY, $N7, $vV, $qK);
        rjN:
    }
    public function appendToKeyInfo($nC)
    {
        $m3 = $this->sigNode;
        $sQ = $m3->ownerDocument;
        $vV = $this->getXPathObj();
        if (!empty($vV)) {
            goto EgC;
        }
        $vV = new DOMXPath($m3->ownerDocument);
        $vV->registerNamespace("\x73\145\x63\x64\x73\151\x67", self::XMLDSIGNS);
        EgC:
        $wQ = "\x2e\x2f\x73\x65\143\x64\x73\151\x67\72\113\145\171\111\x6e\146\x6f";
        $Bm = $vV->query($wQ, $m3);
        $lV = $Bm->item(0);
        if ($lV) {
            goto zpS;
        }
        $I1 = '';
        $NO = $m3->lookupPrefix(self::XMLDSIGNS);
        if (empty($NO)) {
            goto HP1;
        }
        $I1 = $NO . "\x3a";
        HP1:
        $lg = false;
        $lV = $sQ->createElementNS(self::XMLDSIGNS, $I1 . "\x4b\145\x79\x49\156\x66\x6f");
        $wQ = "\x2e\57\x73\x65\143\144\163\151\x67\x3a\117\142\x6a\x65\x63\164";
        $Bm = $vV->query($wQ, $m3);
        if (!($Hp = $Bm->item(0))) {
            goto h1r;
        }
        $Hp->parentNode->insertBefore($lV, $Hp);
        $lg = true;
        h1r:
        if ($lg) {
            goto DCg;
        }
        $m3->appendChild($lV);
        DCg:
        zpS:
        $lV->appendChild($nC);
        return $lV;
    }
    public function getValidatedNodes()
    {
        return $this->validatedNodes;
    }
}
