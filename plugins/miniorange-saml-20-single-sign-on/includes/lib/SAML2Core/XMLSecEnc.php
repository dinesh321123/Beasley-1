<?php


namespace RobRichards\XMLSecLibs;

use DOMDocument;
use DOMNode;
use DOMXPath;
use Exception;
use RobRichards\XMLSecLibs\Utils\XPath;
class XMLSecEnc
{
    const template = "\74\x78\145\x6e\x63\x3a\105\156\143\162\x79\x70\164\x65\x64\x44\x61\x74\x61\x20\x78\x6d\x6c\156\x73\x3a\x78\x65\156\143\x3d\x27\x68\164\x74\160\x3a\x2f\x2f\x77\167\167\x2e\167\x33\x2e\157\162\x67\57\x32\60\x30\x31\x2f\60\64\x2f\170\155\x6c\x65\156\x63\43\47\x3e\12\40\x20\x20\x3c\170\x65\156\x63\72\103\x69\x70\x68\145\162\x44\x61\164\x61\x3e\12\x20\x20\x20\40\x20\40\x3c\x78\145\x6e\143\72\103\151\x70\150\x65\x72\x56\x61\154\x75\x65\76\x3c\x2f\170\145\x6e\143\x3a\x43\151\160\x68\x65\162\x56\141\154\165\x65\76\12\x20\40\x20\74\x2f\170\x65\156\143\x3a\x43\x69\160\150\145\x72\x44\141\164\x61\x3e\12\x3c\57\170\145\156\143\72\x45\x6e\143\x72\x79\x70\x74\145\144\104\x61\164\141\x3e";
    const Element = "\x68\164\164\160\x3a\57\57\167\x77\167\56\x77\63\56\x6f\162\147\x2f\x32\60\60\61\57\60\x34\x2f\170\x6d\x6c\145\156\143\x23\105\x6c\145\155\145\156\164";
    const Content = "\150\x74\164\160\x3a\57\57\x77\x77\167\56\x77\63\56\x6f\x72\147\57\62\x30\x30\61\57\x30\64\x2f\x78\x6d\154\x65\x6e\x63\x23\103\157\156\164\145\x6e\164";
    const URI = 3;
    const XMLENCNS = "\x68\164\164\160\x3a\57\57\x77\x77\167\x2e\x77\x33\x2e\157\162\147\57\62\x30\60\x31\57\x30\64\57\170\x6d\x6c\x65\156\143\43";
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
    public function addReference($dC, $nC, $Uj)
    {
        if ($nC instanceof DOMNode) {
            goto dLL;
        }
        throw new Exception("\x24\156\x6f\144\145\x20\151\163\40\x6e\x6f\164\x20\x6f\x66\x20\164\x79\160\145\40\x44\x4f\x4d\x4e\157\144\x65");
        dLL:
        $Mo = $this->encdoc;
        $this->_resetTemplate();
        $H2 = $this->encdoc;
        $this->encdoc = $Mo;
        $jN = XMLSecurityDSig::generateGUID();
        $XV = $H2->documentElement;
        $XV->setAttribute("\x49\144", $jN);
        $this->references[$dC] = array("\x6e\x6f\x64\145" => $nC, "\x74\171\160\145" => $Uj, "\145\x6e\x63\156\x6f\x64\145" => $H2, "\162\x65\146\165\162\151" => $jN);
    }
    public function setNode($nC)
    {
        $this->rawNode = $nC;
    }
    public function encryptNode($RS, $ao = true)
    {
        $K9 = '';
        if (!empty($this->rawNode)) {
            goto Dyg;
        }
        throw new Exception("\x4e\x6f\144\x65\40\x74\x6f\x20\x65\156\x63\162\171\160\164\40\x68\141\x73\x20\x6e\x6f\x74\x20\142\145\x65\x6e\x20\x73\145\x74");
        Dyg:
        if ($RS instanceof XMLSecurityKey) {
            goto qxu;
        }
        throw new Exception("\111\x6e\x76\141\x6c\x69\x64\x20\113\x65\171");
        qxu:
        $li = $this->rawNode->ownerDocument;
        $oR = new DOMXPath($this->encdoc);
        $Hf = $oR->query("\57\170\145\x6e\x63\x3a\105\156\143\x72\171\160\164\x65\144\x44\141\164\141\57\x78\x65\x6e\143\x3a\103\x69\160\150\145\162\104\141\164\x61\x2f\x78\x65\x6e\x63\x3a\103\151\160\x68\145\162\126\x61\x6c\x75\x65");
        $mg = $Hf->item(0);
        if (!($mg == null)) {
            goto xlD;
        }
        throw new Exception("\105\x72\162\157\162\x20\154\157\x63\x61\x74\x69\156\147\40\103\151\x70\x68\x65\162\126\x61\x6c\x75\145\x20\145\154\145\x6d\145\x6e\x74\x20\167\x69\164\150\x69\x6e\40\x74\x65\x6d\160\154\x61\164\x65");
        xlD:
        switch ($this->type) {
            case self::Element:
                $K9 = $li->saveXML($this->rawNode);
                $this->encdoc->documentElement->setAttribute("\x54\x79\x70\145", self::Element);
                goto roM;
            case self::Content:
                $yJ = $this->rawNode->childNodes;
                foreach ($yJ as $GZ) {
                    $K9 .= $li->saveXML($GZ);
                    NRv:
                }
                nUq:
                $this->encdoc->documentElement->setAttribute("\124\171\160\x65", self::Content);
                goto roM;
            default:
                throw new Exception("\x54\171\160\145\40\151\x73\x20\x63\165\x72\162\x65\x6e\164\154\171\x20\156\x6f\164\40\x73\165\160\x70\157\x72\164\145\x64");
        }
        UPl:
        roM:
        $Mf = $this->encdoc->documentElement->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\x78\x65\156\143\72\x45\x6e\x63\162\171\x70\164\x69\157\x6e\x4d\x65\x74\x68\x6f\x64"));
        $Mf->setAttribute("\101\x6c\147\157\162\x69\164\x68\155", $RS->getAlgorithm());
        $mg->parentNode->parentNode->insertBefore($Mf, $mg->parentNode->parentNode->firstChild);
        $ew = base64_encode($RS->encryptData($K9));
        $zF = $this->encdoc->createTextNode($ew);
        $mg->appendChild($zF);
        if ($ao) {
            goto TTI;
        }
        return $this->encdoc->documentElement;
        goto zyv;
        TTI:
        switch ($this->type) {
            case self::Element:
                if (!($this->rawNode->nodeType == XML_DOCUMENT_NODE)) {
                    goto pzd;
                }
                return $this->encdoc;
                pzd:
                $L9 = $this->rawNode->ownerDocument->importNode($this->encdoc->documentElement, true);
                $this->rawNode->parentNode->replaceChild($L9, $this->rawNode);
                return $L9;
            case self::Content:
                $L9 = $this->rawNode->ownerDocument->importNode($this->encdoc->documentElement, true);
                Vhv:
                if (!$this->rawNode->firstChild) {
                    goto fJ0;
                }
                $this->rawNode->removeChild($this->rawNode->firstChild);
                goto Vhv;
                fJ0:
                $this->rawNode->appendChild($L9);
                return $L9;
        }
        qr7:
        DPr:
        zyv:
    }
    public function encryptReferences($RS)
    {
        $Oq = $this->rawNode;
        $T7 = $this->type;
        foreach ($this->references as $dC => $Px) {
            $this->encdoc = $Px["\145\156\143\x6e\x6f\144\x65"];
            $this->rawNode = $Px["\x6e\157\x64\145"];
            $this->type = $Px["\164\171\x70\x65"];
            try {
                $nK = $this->encryptNode($RS);
                $this->references[$dC]["\145\156\143\156\157\144\x65"] = $nK;
            } catch (Exception $ZE) {
                $this->rawNode = $Oq;
                $this->type = $T7;
                throw $ZE;
            }
            I77:
        }
        P25:
        $this->rawNode = $Oq;
        $this->type = $T7;
    }
    public function getCipherValue()
    {
        if (!empty($this->rawNode)) {
            goto MFW;
        }
        throw new Exception("\116\x6f\x64\x65\40\x74\x6f\40\144\x65\143\162\171\x70\164\x20\150\x61\x73\x20\156\x6f\164\40\x62\145\x65\x6e\x20\x73\x65\x74");
        MFW:
        $li = $this->rawNode->ownerDocument;
        $oR = new DOMXPath($li);
        $oR->registerNamespace("\x78\x6d\x6c\145\x6e\143\162", self::XMLENCNS);
        $wQ = "\x2e\57\x78\155\154\145\156\143\162\x3a\103\x69\160\150\145\x72\104\x61\x74\x61\x2f\170\x6d\x6c\x65\x6e\143\162\72\x43\151\160\150\x65\x72\x56\141\154\165\x65";
        $Bm = $oR->query($wQ, $this->rawNode);
        $nC = $Bm->item(0);
        if ($nC) {
            goto c3t;
        }
        return null;
        c3t:
        return base64_decode($nC->nodeValue);
    }
    public function decryptNode($RS, $ao = true)
    {
        if ($RS instanceof XMLSecurityKey) {
            goto qVW;
        }
        throw new Exception("\x49\156\x76\141\x6c\151\x64\x20\113\x65\171");
        qVW:
        $JP = $this->getCipherValue();
        if ($JP) {
            goto WZd;
        }
        throw new Exception("\103\141\156\x6e\157\164\40\154\157\x63\141\164\x65\x20\145\x6e\x63\162\x79\x70\x74\145\144\40\x64\141\164\141");
        goto Upf;
        WZd:
        $q1 = $RS->decryptData($JP);
        if ($ao) {
            goto ofO;
        }
        return $q1;
        goto FSD;
        ofO:
        switch ($this->type) {
            case self::Element:
                $gh = new DOMDocument();
                $gh->loadXML($q1);
                if (!($this->rawNode->nodeType == XML_DOCUMENT_NODE)) {
                    goto iGb;
                }
                return $gh;
                iGb:
                $L9 = $this->rawNode->ownerDocument->importNode($gh->documentElement, true);
                $this->rawNode->parentNode->replaceChild($L9, $this->rawNode);
                return $L9;
            case self::Content:
                if ($this->rawNode->nodeType == XML_DOCUMENT_NODE) {
                    goto kAj;
                }
                $li = $this->rawNode->ownerDocument;
                goto dDo;
                kAj:
                $li = $this->rawNode;
                dDo:
                $i8 = $li->createDocumentFragment();
                $i8->appendXML($q1);
                $TE = $this->rawNode->parentNode;
                $TE->replaceChild($i8, $this->rawNode);
                return $TE;
            default:
                return $q1;
        }
        BJv:
        mgC:
        FSD:
        Upf:
    }
    public function encryptKey($Zv, $AG, $Km = true)
    {
        if (!(!$Zv instanceof XMLSecurityKey || !$AG instanceof XMLSecurityKey)) {
            goto Zjx;
        }
        throw new Exception("\x49\x6e\166\x61\x6c\x69\144\40\113\x65\171");
        Zjx:
        $yV = base64_encode($Zv->encryptData($AG->key));
        $oO = $this->encdoc->documentElement;
        $ya = $this->encdoc->createElementNS(self::XMLENCNS, "\x78\x65\x6e\x63\72\105\x6e\x63\162\x79\160\x74\145\144\x4b\145\x79");
        if ($Km) {
            goto JAQ;
        }
        $this->encKey = $ya;
        goto qfq;
        JAQ:
        $lV = $oO->insertBefore($this->encdoc->createElementNS("\x68\x74\164\x70\x3a\x2f\x2f\167\x77\167\56\x77\x33\56\x6f\162\147\57\x32\60\x30\x30\57\60\x39\x2f\x78\155\154\x64\163\x69\147\x23", "\x64\163\151\147\x3a\113\x65\x79\x49\x6e\146\x6f"), $oO->firstChild);
        $lV->appendChild($ya);
        qfq:
        $Mf = $ya->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\x78\145\156\x63\72\105\x6e\x63\x72\171\x70\x74\151\157\x6e\115\145\x74\x68\x6f\x64"));
        $Mf->setAttribute("\101\154\147\157\162\x69\164\150\155", $Zv->getAlgorith());
        if (empty($Zv->name)) {
            goto bUi;
        }
        $lV = $ya->appendChild($this->encdoc->createElementNS("\150\x74\x74\160\x3a\57\x2f\x77\167\167\x2e\167\63\x2e\x6f\x72\147\57\x32\60\x30\x30\57\x30\71\57\x78\155\x6c\144\x73\x69\x67\x23", "\144\x73\151\147\x3a\x4b\x65\171\111\x6e\146\157"));
        $lV->appendChild($this->encdoc->createElementNS("\150\164\x74\160\72\57\x2f\167\x77\x77\56\167\x33\56\x6f\162\147\57\x32\60\x30\60\x2f\x30\x39\57\x78\155\154\x64\163\x69\147\43", "\144\x73\x69\x67\x3a\113\x65\171\x4e\141\155\x65", $Zv->name));
        bUi:
        $eN = $ya->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\170\145\x6e\x63\x3a\x43\151\160\x68\145\x72\x44\141\164\141"));
        $eN->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\170\x65\156\143\x3a\x43\x69\160\x68\145\162\126\141\x6c\x75\145", $yV));
        if (!(is_array($this->references) && count($this->references) > 0)) {
            goto ieU;
        }
        $vL = $ya->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\x78\145\x6e\x63\x3a\x52\145\x66\x65\x72\145\x6e\143\x65\114\x69\163\x74"));
        foreach ($this->references as $dC => $Px) {
            $jN = $Px["\x72\x65\146\x75\x72\151"];
            $Nl = $vL->appendChild($this->encdoc->createElementNS(self::XMLENCNS, "\x78\x65\156\x63\72\x44\x61\164\x61\122\145\x66\145\162\145\156\x63\145"));
            $Nl->setAttribute("\125\x52\x49", "\43" . $jN);
            rkD:
        }
        vY4:
        ieU:
        return;
    }
    public function decryptKey($ya)
    {
        if ($ya->isEncrypted) {
            goto Nf9;
        }
        throw new Exception("\x4b\145\171\x20\x69\x73\x20\156\157\x74\x20\x45\x6e\x63\162\171\160\164\x65\x64");
        Nf9:
        if (!empty($ya->key)) {
            goto teV;
        }
        throw new Exception("\113\x65\171\40\x69\163\x20\155\151\x73\163\151\156\147\x20\x64\141\164\x61\x20\x74\x6f\40\160\x65\162\146\157\x72\x6d\40\x74\x68\x65\x20\x64\x65\x63\x72\x79\x70\164\151\157\156");
        teV:
        return $this->decryptNode($ya, false);
    }
    public function locateEncryptedData($XV)
    {
        if ($XV instanceof DOMDocument) {
            goto U7V;
        }
        $li = $XV->ownerDocument;
        goto rsm;
        U7V:
        $li = $XV;
        rsm:
        if (!$li) {
            goto YPh;
        }
        $vV = new DOMXPath($li);
        $wQ = "\57\x2f\x2a\x5b\x6c\157\143\x61\154\55\x6e\x61\x6d\x65\50\x29\x3d\47\x45\156\x63\162\171\x70\x74\145\x64\104\141\164\x61\x27\x20\141\156\x64\x20\x6e\x61\x6d\x65\163\160\141\143\145\55\165\162\x69\x28\x29\75\x27" . self::XMLENCNS . "\x27\135";
        $Bm = $vV->query($wQ);
        return $Bm->item(0);
        YPh:
        return null;
    }
    public function locateKey($nC = null)
    {
        if (!empty($nC)) {
            goto pyA;
        }
        $nC = $this->rawNode;
        pyA:
        if ($nC instanceof DOMNode) {
            goto jDX;
        }
        return null;
        jDX:
        if (!($li = $nC->ownerDocument)) {
            goto yOS;
        }
        $vV = new DOMXPath($li);
        $vV->registerNamespace("\x78\155\x6c\163\x65\143\x65\156\x63", self::XMLENCNS);
        $wQ = "\56\x2f\x2f\170\155\154\x73\145\143\145\156\x63\72\x45\156\143\x72\171\160\164\151\x6f\x6e\x4d\x65\x74\150\157\144";
        $Bm = $vV->query($wQ, $nC);
        if (!($jJ = $Bm->item(0))) {
            goto zkn;
        }
        $Kn = $jJ->getAttribute("\101\x6c\147\x6f\162\x69\164\x68\155");
        try {
            $RS = new XMLSecurityKey($Kn, array("\x74\x79\x70\145" => "\160\x72\151\166\141\x74\145"));
        } catch (Exception $ZE) {
            return null;
        }
        return $RS;
        zkn:
        yOS:
        return null;
    }
    public static function staticLocateKeyInfo($fa = null, $nC = null)
    {
        if (!(empty($nC) || !$nC instanceof DOMNode)) {
            goto ric;
        }
        return null;
        ric:
        $li = $nC->ownerDocument;
        if ($li) {
            goto pl3;
        }
        return null;
        pl3:
        $vV = new DOMXPath($li);
        $vV->registerNamespace("\170\155\x6c\163\x65\143\145\x6e\x63", self::XMLENCNS);
        $vV->registerNamespace("\x78\x6d\x6c\163\x65\143\144\x73\151\147", XMLSecurityDSig::XMLDSIGNS);
        $wQ = "\56\x2f\x78\x6d\154\x73\145\x63\144\x73\151\x67\x3a\x4b\145\x79\x49\156\146\157";
        $Bm = $vV->query($wQ, $nC);
        $jJ = $Bm->item(0);
        if ($jJ) {
            goto QNd;
        }
        return $fa;
        QNd:
        foreach ($jJ->childNodes as $GZ) {
            switch ($GZ->localName) {
                case "\113\145\x79\116\x61\x6d\145":
                    if (empty($fa)) {
                        goto Soy;
                    }
                    $fa->name = $GZ->nodeValue;
                    Soy:
                    goto TXa;
                case "\x4b\145\171\126\141\154\x75\x65":
                    foreach ($GZ->childNodes as $FE) {
                        switch ($FE->localName) {
                            case "\x44\123\x41\113\145\171\x56\x61\x6c\x75\x65":
                                throw new Exception("\104\123\101\x4b\x65\x79\x56\141\x6c\165\145\x20\143\165\162\x72\x65\156\164\154\171\x20\156\157\x74\40\x73\165\x70\x70\157\162\x74\x65\x64");
                            case "\122\x53\101\x4b\145\x79\x56\141\154\165\145":
                                $pN = null;
                                $Ui = null;
                                if (!($Z4 = $FE->getElementsByTagName("\x4d\157\144\165\154\x75\x73")->item(0))) {
                                    goto UkL;
                                }
                                $pN = base64_decode($Z4->nodeValue);
                                UkL:
                                if (!($qr = $FE->getElementsByTagName("\x45\170\160\157\156\145\156\164")->item(0))) {
                                    goto kya;
                                }
                                $Ui = base64_decode($qr->nodeValue);
                                kya:
                                if (!(empty($pN) || empty($Ui))) {
                                    goto FvY;
                                }
                                throw new Exception("\x4d\151\163\x73\151\x6e\x67\40\x4d\157\144\x75\154\x75\163\x20\157\x72\x20\105\170\160\157\156\145\x6e\x74");
                                FvY:
                                $IA = XMLSecurityKey::convertRSA($pN, $Ui);
                                $fa->loadKey($IA);
                                goto cWp;
                        }
                        vuV:
                        cWp:
                        tZh:
                    }
                    WjD:
                    goto TXa;
                case "\x52\145\164\162\x69\x65\166\141\154\x4d\145\x74\150\x6f\144":
                    $Uj = $GZ->getAttribute("\124\x79\160\145");
                    if (!($Uj !== "\150\164\x74\x70\x3a\x2f\57\x77\x77\167\x2e\x77\63\x2e\x6f\162\x67\x2f\62\x30\60\61\x2f\x30\x34\57\170\155\154\x65\x6e\x63\43\105\156\x63\x72\x79\x70\x74\145\x64\x4b\145\171")) {
                        goto YVh;
                    }
                    goto TXa;
                    YVh:
                    $Zk = $GZ->getAttribute("\125\122\111");
                    if (!($Zk[0] !== "\x23")) {
                        goto yVD;
                    }
                    goto TXa;
                    yVD:
                    $kE = substr($Zk, 1);
                    $wQ = "\x2f\57\x78\155\154\163\145\143\x65\x6e\x63\x3a\105\x6e\143\162\171\160\x74\145\x64\x4b\145\x79\133\100\x49\144\x3d\x22" . XPath::filterAttrValue($kE, XPath::DOUBLE_QUOTE) . "\42\x5d";
                    $Iv = $vV->query($wQ)->item(0);
                    if ($Iv) {
                        goto tZ8;
                    }
                    throw new Exception("\x55\x6e\x61\x62\x6c\145\x20\x74\157\40\154\157\x63\141\164\x65\40\105\156\x63\x72\x79\160\164\x65\144\x4b\x65\x79\40\x77\x69\164\x68\40\x40\111\144\x3d\x27{$kE}\x27\56");
                    tZ8:
                    return XMLSecurityKey::fromEncryptedKeyElement($Iv);
                case "\105\x6e\x63\x72\x79\160\x74\x65\x64\113\x65\x79":
                    return XMLSecurityKey::fromEncryptedKeyElement($GZ);
                case "\x58\x35\60\71\x44\141\164\x61":
                    if (!($QG = $GZ->getElementsByTagName("\x58\x35\x30\71\x43\145\x72\x74\x69\x66\151\143\141\164\145"))) {
                        goto Igj;
                    }
                    if (!($QG->length > 0)) {
                        goto d45;
                    }
                    $aF = $QG->item(0)->textContent;
                    $aF = str_replace(array("\xd", "\xa", "\40"), '', $aF);
                    $aF = "\x2d\55\55\55\x2d\102\x45\107\111\116\40\x43\x45\x52\124\x49\106\111\103\101\124\x45\55\55\x2d\x2d\x2d\12" . chunk_split($aF, 64, "\12") . "\x2d\55\55\x2d\x2d\105\116\x44\x20\103\105\122\124\x49\106\111\x43\101\x54\105\55\x2d\55\55\55\12";
                    $fa->loadKey($aF, false, true);
                    d45:
                    Igj:
                    goto TXa;
            }
            xCT:
            TXa:
            Sz_:
        }
        l4N:
        return $fa;
    }
    public function locateKeyInfo($fa = null, $nC = null)
    {
        if (!empty($nC)) {
            goto IWw;
        }
        $nC = $this->rawNode;
        IWw:
        return self::staticLocateKeyInfo($fa, $nC);
    }
}
