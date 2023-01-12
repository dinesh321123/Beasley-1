<?php


namespace RobRichards\XMLSecLibs;

use DOMDocument;
use DOMNode;
use DOMXPath;
use Exception;
use RobRichards\XMLSecLibs\Utils\XPath as XPath;
class XMLSecEnc
{
    const template = "\74\x78\145\x6e\143\x3a\x45\156\143\162\171\160\164\145\x64\104\x61\x74\x61\x20\170\x6d\154\156\x73\72\170\x65\156\143\x3d\47\150\164\x74\x70\72\57\x2f\167\167\167\x2e\x77\63\x2e\157\x72\147\x2f\x32\60\x30\x31\x2f\60\64\x2f\x78\155\154\145\156\143\x23\47\76\xd\xa\40\40\40\74\x78\x65\156\x63\72\x43\x69\160\150\145\x72\x44\141\164\x61\76\xd\12\40\40\x20\x20\40\x20\x3c\x78\x65\x6e\x63\x3a\x43\151\160\x68\x65\x72\126\x61\x6c\165\x65\76\x3c\x2f\x78\x65\156\x63\72\103\x69\x70\x68\x65\162\x56\141\x6c\165\145\76\xd\12\40\x20\40\74\x2f\170\145\x6e\x63\72\103\151\x70\150\145\x72\x44\x61\x74\141\x3e\15\xa\74\57\170\145\x6e\x63\72\x45\x6e\143\x72\171\x70\164\x65\144\x44\141\x74\x61\x3e";
    const Element = "\150\164\x74\160\72\57\57\167\167\x77\56\167\63\x2e\157\x72\147\x2f\62\60\x30\x31\57\60\x34\57\x78\155\154\145\x6e\x63\x23\105\154\x65\155\145\x6e\164";
    const Content = "\x68\164\x74\x70\x3a\57\57\167\167\x77\x2e\167\63\56\157\x72\x67\57\62\60\60\61\x2f\x30\64\x2f\x78\x6d\154\x65\156\143\43\x43\x6f\156\x74\x65\x6e\164";
    const URI = 3;
    const XMLENCNS = "\x68\164\164\x70\72\57\x2f\x77\167\167\x2e\x77\63\x2e\x6f\x72\x67\x2f\62\x30\60\x31\57\x30\64\x2f\170\155\x6c\145\156\143\43";
    private $encdoc = null;
    private $rawNode = null;
    public $type = null;
    public $encKey = null;
    private $references = array();
    public function __construct()
    {
        $this->_resetTemplate();
    }
    private function _resetTemplate()
    {
        $this->encdoc = new DOMDocument();
        $this->encdoc->loadXML(self::template);
    }
    public function addReference($UO, $p8, $Ts)
    {
        if ($p8 instanceof DOMNode) {
            goto G9;
        }
        throw new Exception("\x24\x6e\x6f\144\x65\40\151\163\x20\156\157\164\40\x6f\146\40\164\171\x70\x65\x20\x44\x4f\x4d\x4e\x6f\x64\145");
        G9:
        $xZ = $this->encdoc;
        $this->_resetTemplate();
        $sr = $this->encdoc;
        $this->encdoc = $xZ;
        $y3 = XMLSecurityDSig::generateGUID();
        $XP = $sr->documentElement;
        $XP->setAttribute("\111\144", $y3);
        $this->references[$UO] = array("\x6e\x6f\144\145" => $p8, "\164\x79\x70\145" => $Ts, "\145\156\x63\156\157\x64\145" => $sr, "\x72\x65\x66\x75\162\x69" => $y3);
    }
    public function setNode($p8)
    {
        $this->rawNode = $p8;
    }
    public function encryptNode($s8, $b9 = true)
    {
        $Tb = '';
        if (!empty($this->rawNode)) {
            goto Ah;
        }
        throw new Exception("\x4e\157\x64\x65\40\164\x6f\40\x65\x6e\143\x72\x79\x70\x74\x20\x68\141\163\40\x6e\x6f\164\40\142\x65\x65\156\40\x73\145\x74");
        Ah:
        if ($s8 instanceof XMLSecurityKey) {
            goto OM;
        }
        throw new Exception("\x49\x6e\166\x61\x6c\151\x64\40\x4b\145\171");
        OM:
        $ZR = $this->rawNode->ownerDocument;
        $b1 = new DOMXPath($this->encdoc);
        $JD = $b1->query("\57\x78\x65\156\x63\72\105\156\x63\x72\171\x70\164\x65\144\x44\141\x74\141\x2f\x78\x65\x6e\x63\x3a\103\151\x70\x68\x65\162\x44\x61\164\141\57\x78\145\x6e\143\x3a\103\151\x70\150\x65\x72\126\141\x6c\x75\x65");
        $Dl = $JD->item(0);
        if (!($Dl == null)) {
            goto Y0;
        }
        throw new Exception("\105\x72\x72\157\162\x20\154\157\143\x61\x74\x69\156\147\40\103\x69\160\x68\x65\162\x56\x61\x6c\x75\145\x20\x65\x6c\x65\x6d\x65\x6e\164\x20\x77\x69\x74\x68\x69\156\x20\x74\x65\155\160\x6c\141\164\x65");
        Y0:
        switch ($this->type) {
            case self::Element:
                $Tb = $ZR->saveXML($this->rawNode);
                $this->encdoc->documentElement->setAttribute("\124\x79\160\145", self::Element);
                goto Yr;
            case self::Content:
                $AE = $this->rawNode->childNodes;
                foreach ($AE as $Qm) {
                    $Tb .= $ZR->saveXML($Qm);
                    yc:
                }
                Lk:
                $this->encdoc->documentElement->setAttribute("\x54\171\x70\x65", self::Content);
                goto Yr;
            default:
                throw new Exception("\x54\171\x70\x65\40\151\163\x20\x63\165\162\162\x65\156\x74\154\171\40\156\157\164\40\163\165\160\160\x6f\162\x74\145\x64");
        }
        EJ:
        Yr:
        $GW = $this->encdoc->documentElement->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\170\145\156\x63\x3a\105\156\143\x72\x79\x70\x74\x69\x6f\156\115\145\x74\x68\157\x64"));
        $GW->setAttribute("\x41\x6c\x67\157\162\151\164\150\155", $s8->getAlgorithm());
        $Dl->parentNode->parentNode->insertBefore($GW, $Dl->parentNode->parentNode->firstChild);
        $PC = base64_encode($s8->encryptData($Tb));
        $wE = $this->encdoc->createTextNode($PC);
        $Dl->appendChild($wE);
        if ($b9) {
            goto Nz;
        }
        return $this->encdoc->documentElement;
        goto Kl;
        Nz:
        switch ($this->type) {
            case self::Element:
                if (!($this->rawNode->nodeType == XML_DOCUMENT_NODE)) {
                    goto G2;
                }
                return $this->encdoc;
                G2:
                $yB = $this->rawNode->ownerDocument->importNode($this->encdoc->documentElement, true);
                $this->rawNode->parentNode->replaceChild($yB, $this->rawNode);
                return $yB;
            case self::Content:
                $yB = $this->rawNode->ownerDocument->importNode($this->encdoc->documentElement, true);
                Ud:
                if (!$this->rawNode->firstChild) {
                    goto Ck;
                }
                $this->rawNode->removeChild($this->rawNode->firstChild);
                goto Ud;
                Ck:
                $this->rawNode->appendChild($yB);
                return $yB;
        }
        HB:
        mO:
        Kl:
    }
    public function encryptReferences($s8)
    {
        $ap = $this->rawNode;
        $ZM = $this->type;
        foreach ($this->references as $UO => $No) {
            $this->encdoc = $No["\145\x6e\x63\x6e\x6f\x64\145"];
            $this->rawNode = $No["\156\157\x64\145"];
            $this->type = $No["\x74\x79\160\145"];
            try {
                $t2 = $this->encryptNode($s8);
                $this->references[$UO]["\145\156\x63\156\x6f\144\145"] = $t2;
            } catch (Exception $wD) {
                $this->rawNode = $ap;
                $this->type = $ZM;
                throw $wD;
            }
            q1:
        }
        JL:
        $this->rawNode = $ap;
        $this->type = $ZM;
    }
    public function getCipherValue()
    {
        if (!empty($this->rawNode)) {
            goto q8;
        }
        throw new Exception("\x4e\157\144\x65\40\164\157\40\144\x65\x63\x72\x79\160\x74\40\150\141\x73\x20\x6e\157\164\x20\142\145\x65\x6e\40\163\145\164");
        q8:
        $ZR = $this->rawNode->ownerDocument;
        $b1 = new DOMXPath($ZR);
        $b1->registerNamespace("\170\x6d\154\145\x6e\143\x72", self::XMLENCNS);
        $wO = "\x2e\x2f\170\155\154\145\156\x63\162\72\x43\x69\x70\150\145\162\x44\x61\x74\x61\x2f\x78\155\x6c\145\x6e\143\162\x3a\x43\151\x70\x68\x65\162\x56\x61\154\165\145";
        $BC = $b1->query($wO, $this->rawNode);
        $p8 = $BC->item(0);
        if ($p8) {
            goto sM;
        }
        return null;
        sM:
        return base64_decode($p8->nodeValue);
    }
    public function decryptNode($s8, $b9 = true)
    {
        if ($s8 instanceof XMLSecurityKey) {
            goto Iz;
        }
        throw new Exception("\x49\x6e\x76\141\x6c\151\x64\40\x4b\145\x79");
        Iz:
        $Ns = $this->getCipherValue();
        if ($Ns) {
            goto xm;
        }
        throw new Exception("\103\x61\156\x6e\157\164\x20\x6c\x6f\x63\141\x74\x65\40\x65\x6e\143\x72\171\160\x74\x65\x64\40\x64\141\164\x61");
        goto EQ;
        xm:
        $K1 = $s8->decryptData($Ns);
        if ($b9) {
            goto v3;
        }
        return $K1;
        goto iY;
        v3:
        switch ($this->type) {
            case self::Element:
                $tu = new DOMDocument();
                $tu->loadXML($K1);
                if (!($this->rawNode->nodeType == XML_DOCUMENT_NODE)) {
                    goto qA;
                }
                return $tu;
                qA:
                $yB = $this->rawNode->ownerDocument->importNode($tu->documentElement, true);
                $this->rawNode->parentNode->replaceChild($yB, $this->rawNode);
                return $yB;
            case self::Content:
                if ($this->rawNode->nodeType == XML_DOCUMENT_NODE) {
                    goto C0;
                }
                $ZR = $this->rawNode->ownerDocument;
                goto Pg;
                C0:
                $ZR = $this->rawNode;
                Pg:
                $Ki = $ZR->createDocumentFragment();
                $Ki->appendXML($K1);
                $Ez = $this->rawNode->parentNode;
                $Ez->replaceChild($Ki, $this->rawNode);
                return $Ez;
            default:
                return $K1;
        }
        uv:
        pd:
        iY:
        EQ:
    }
    public function encryptKey($TF, $da, $UL = true)
    {
        if (!(!$TF instanceof XMLSecurityKey || !$da instanceof XMLSecurityKey)) {
            goto bo;
        }
        throw new Exception("\x49\x6e\x76\x61\154\151\x64\40\x4b\145\x79");
        bo:
        $EM = base64_encode($TF->encryptData($da->key));
        $W2 = $this->encdoc->documentElement;
        $dR = $this->encdoc->createElementNS(self::XMLENCNS, "\170\x65\x6e\x63\72\x45\156\143\162\x79\160\x74\145\144\x4b\145\x79");
        if ($UL) {
            goto Z6;
        }
        $this->encKey = $dR;
        goto kQ;
        Z6:
        $vh = $W2->insertBefore($this->encdoc->createElementNS("\x68\164\164\160\72\x2f\x2f\x77\x77\x77\56\x77\x33\56\157\x72\147\x2f\62\x30\x30\60\57\x30\x39\57\x78\x6d\154\x64\x73\151\x67\43", "\144\x73\x69\x67\72\x4b\x65\171\x49\x6e\146\157"), $W2->firstChild);
        $vh->appendChild($dR);
        kQ:
        $GW = $dR->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\x78\145\x6e\143\x3a\105\156\143\x72\171\160\164\x69\157\x6e\115\x65\164\x68\157\x64"));
        $GW->setAttribute("\101\x6c\147\x6f\x72\151\x74\150\x6d", $TF->getAlgorith());
        if (empty($TF->name)) {
            goto n6;
        }
        $vh = $dR->appendChild($this->encdoc->createElementNS("\150\164\164\x70\72\x2f\x2f\x77\x77\x77\56\167\x33\56\157\x72\x67\x2f\62\x30\x30\x30\x2f\x30\x39\57\x78\155\x6c\x64\x73\x69\147\x23", "\x64\163\151\147\x3a\x4b\x65\x79\111\156\146\x6f"));
        $vh->appendChild($this->encdoc->createElementNS("\x68\x74\x74\160\x3a\x2f\x2f\x77\x77\167\x2e\167\x33\56\x6f\x72\x67\57\x32\60\x30\x30\57\60\71\57\x78\x6d\x6c\x64\163\x69\x67\43", "\x64\x73\x69\147\x3a\113\145\x79\116\141\x6d\145", $TF->name));
        n6:
        $zS = $dR->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\170\145\156\x63\72\103\151\x70\x68\x65\162\104\x61\x74\141"));
        $zS->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\x78\x65\x6e\143\72\x43\x69\x70\150\145\162\126\x61\154\x75\x65", $EM));
        if (!(is_array($this->references) && count($this->references) > 0)) {
            goto qZ;
        }
        $Vi = $dR->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\x78\145\x6e\x63\72\x52\145\146\x65\x72\145\x6e\143\145\x4c\151\163\164"));
        foreach ($this->references as $UO => $No) {
            $y3 = $No["\x72\145\146\x75\162\151"];
            $Ci = $Vi->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\170\x65\x6e\143\72\104\141\x74\x61\122\145\146\145\x72\x65\156\x63\145"));
            $Ci->setAttribute("\125\x52\111", "\x23" . $y3);
            nW:
        }
        t0:
        qZ:
        return;
    }
    public function decryptKey($dR)
    {
        if ($dR->isEncrypted) {
            goto Dz;
        }
        throw new Exception("\x4b\145\171\40\x69\163\40\x6e\x6f\164\x20\x45\x6e\143\162\171\160\164\145\144");
        Dz:
        if (!empty($dR->key)) {
            goto D1;
        }
        throw new Exception("\x4b\x65\171\x20\151\163\x20\155\151\163\x73\x69\156\x67\x20\x64\x61\164\141\x20\164\157\40\x70\x65\x72\x66\x6f\x72\x6d\40\164\150\145\40\x64\x65\x63\162\171\x70\x74\x69\157\x6e");
        D1:
        return $this->decryptNode($dR, false);
    }
    public function locateEncryptedData($XP)
    {
        if ($XP instanceof DOMDocument) {
            goto ZB;
        }
        $ZR = $XP->ownerDocument;
        goto hq;
        ZB:
        $ZR = $XP;
        hq:
        if (!$ZR) {
            goto fz;
        }
        $VM = new DOMXPath($ZR);
        $wO = "\57\57\x2a\x5b\154\157\143\141\154\55\156\x61\x6d\145\x28\51\x3d\x27\105\156\x63\162\x79\160\164\x65\x64\x44\x61\164\x61\x27\x20\141\x6e\x64\40\x6e\141\155\x65\163\x70\x61\x63\x65\x2d\x75\162\151\50\51\75\x27" . self::XMLENCNS . "\x27\x5d";
        $BC = $VM->query($wO);
        return $BC->item(0);
        fz:
        return null;
    }
    public function locateKey($p8 = null)
    {
        if (!empty($p8)) {
            goto jp;
        }
        $p8 = $this->rawNode;
        jp:
        if ($p8 instanceof DOMNode) {
            goto lP;
        }
        return null;
        lP:
        if (!($ZR = $p8->ownerDocument)) {
            goto Jl;
        }
        $VM = new DOMXPath($ZR);
        $VM->registerNamespace("\x78\155\154\163\x65\143\145\x6e\x63", self::XMLENCNS);
        $wO = "\x2e\57\x2f\x78\x6d\154\x73\145\x63\145\156\x63\72\105\x6e\x63\162\171\x70\x74\x69\x6f\x6e\115\145\x74\x68\157\x64";
        $BC = $VM->query($wO, $p8);
        if (!($Vx = $BC->item(0))) {
            goto dy;
        }
        $Eu = $Vx->getAttribute("\101\154\147\x6f\162\151\164\150\155");
        try {
            $s8 = new XMLSecurityKey($Eu, array("\164\171\160\x65" => "\160\x72\x69\x76\141\x74\145"));
        } catch (Exception $wD) {
            return null;
        }
        return $s8;
        dy:
        Jl:
        return null;
    }
    public static function staticLocateKeyInfo($x0 = null, $p8 = null)
    {
        if (!(empty($p8) || !$p8 instanceof DOMNode)) {
            goto Qq;
        }
        return null;
        Qq:
        $ZR = $p8->ownerDocument;
        if ($ZR) {
            goto Ij;
        }
        return null;
        Ij:
        $VM = new DOMXPath($ZR);
        $VM->registerNamespace("\170\x6d\154\163\x65\143\x65\x6e\143", self::XMLENCNS);
        $VM->registerNamespace("\x78\x6d\x6c\x73\x65\x63\x64\x73\x69\x67", XMLSecurityDSig::XMLDSIGNS);
        $wO = "\56\57\170\x6d\x6c\163\145\143\x64\x73\x69\x67\72\113\x65\171\x49\x6e\146\x6f";
        $BC = $VM->query($wO, $p8);
        $Vx = $BC->item(0);
        if ($Vx) {
            goto qU;
        }
        return $x0;
        qU:
        foreach ($Vx->childNodes as $Qm) {
            switch ($Qm->localName) {
                case "\x4b\145\171\x4e\141\x6d\145":
                    if (empty($x0)) {
                        goto QS;
                    }
                    $x0->name = $Qm->nodeValue;
                    QS:
                    goto tV;
                case "\x4b\x65\x79\x56\x61\x6c\165\x65":
                    foreach ($Qm->childNodes as $uZ) {
                        switch ($uZ->localName) {
                            case "\x44\123\x41\x4b\x65\171\x56\x61\x6c\165\x65":
                                throw new Exception("\104\x53\x41\113\145\171\126\141\154\165\x65\40\x63\x75\162\162\x65\x6e\164\154\171\x20\x6e\157\164\40\163\165\x70\160\x6f\x72\x74\x65\x64");
                            case "\x52\123\101\113\x65\171\126\x61\x6c\165\145":
                                $WI = null;
                                $Rg = null;
                                if (!($bw = $uZ->getElementsByTagName("\x4d\157\x64\165\154\165\163")->item(0))) {
                                    goto y3;
                                }
                                $WI = base64_decode($bw->nodeValue);
                                y3:
                                if (!($ws = $uZ->getElementsByTagName("\105\170\160\x6f\156\x65\x6e\x74")->item(0))) {
                                    goto Fo;
                                }
                                $Rg = base64_decode($ws->nodeValue);
                                Fo:
                                if (!(empty($WI) || empty($Rg))) {
                                    goto xC;
                                }
                                throw new Exception("\x4d\x69\163\163\151\156\x67\40\x4d\x6f\144\165\x6c\165\x73\x20\x6f\162\x20\x45\x78\x70\x6f\156\145\x6e\164");
                                xC:
                                $xA = XMLSecurityKey::convertRSA($WI, $Rg);
                                $x0->loadKey($xA);
                                goto Ei;
                        }
                        KP:
                        Ei:
                        M6:
                    }
                    NQ:
                    goto tV;
                case "\x52\x65\x74\x72\x69\145\166\x61\154\x4d\145\x74\150\x6f\144":
                    $Ts = $Qm->getAttribute("\124\x79\x70\x65");
                    if (!($Ts !== "\150\x74\x74\160\x3a\57\57\167\167\x77\x2e\x77\x33\x2e\x6f\x72\147\57\x32\x30\60\61\57\x30\64\x2f\170\x6d\154\145\156\x63\43\x45\156\143\162\171\160\x74\x65\x64\113\145\171")) {
                        goto Mt;
                    }
                    goto tV;
                    Mt:
                    $wR = $Qm->getAttribute("\125\x52\x49");
                    if (!($wR[0] !== "\x23")) {
                        goto fl;
                    }
                    goto tV;
                    fl:
                    $Wz = substr($wR, 1);
                    $wO = "\57\x2f\170\155\154\x73\145\143\x65\x6e\143\x3a\105\156\143\162\x79\160\x74\145\x64\x4b\145\171\133\x40\111\x64\75\42" . XPath::filterAttrValue($Wz, XPath::DOUBLE_QUOTE) . "\x22\135";
                    $ER = $VM->query($wO)->item(0);
                    if ($ER) {
                        goto yE;
                    }
                    throw new Exception("\125\x6e\141\142\154\145\x20\x74\157\40\x6c\x6f\x63\x61\164\145\40\105\x6e\x63\x72\171\160\164\145\x64\x4b\145\171\40\167\151\164\x68\40\x40\111\144\75\47{$Wz}\x27\56");
                    yE:
                    return XMLSecurityKey::fromEncryptedKeyElement($ER);
                case "\105\x6e\143\162\x79\160\x74\x65\x64\113\145\171":
                    return XMLSecurityKey::fromEncryptedKeyElement($Qm);
                case "\130\65\60\x39\x44\x61\164\141":
                    if (!($yM = $Qm->getElementsByTagName("\x58\x35\x30\71\103\145\162\164\x69\146\151\143\x61\164\x65"))) {
                        goto kR;
                    }
                    if (!($yM->length > 0)) {
                        goto Zo;
                    }
                    $TD = $yM->item(0)->textContent;
                    $TD = str_replace(array("\15", "\12", "\40"), '', $TD);
                    $TD = "\x2d\x2d\x2d\55\55\x42\105\107\111\x4e\40\x43\x45\x52\124\111\106\x49\x43\101\x54\x45\x2d\55\55\55\55\12" . chunk_split($TD, 64, "\xa") . "\x2d\x2d\55\x2d\55\x45\x4e\104\40\x43\105\x52\124\x49\106\111\x43\x41\x54\x45\x2d\55\55\55\x2d\12";
                    $x0->loadKey($TD, false, true);
                    Zo:
                    kR:
                    goto tV;
            }
            nr:
            tV:
            E2:
        }
        RY:
        return $x0;
    }
    public function locateKeyInfo($x0 = null, $p8 = null)
    {
        if (!empty($p8)) {
            goto OD;
        }
        $p8 = $this->rawNode;
        OD:
        return self::staticLocateKeyInfo($x0, $p8);
    }
}
