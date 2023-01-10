<?php


include_once "\125\x74\151\x6c\x69\x74\151\145\x73\x2e\x70\150\160";
include_once "\x78\155\x6c\x73\x65\143\154\151\x62\163\x2e\x70\150\x70";
use RobRichards\XMLSecLibs\XMLSecurityKey;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecEnc;
class SAML2_LogoutRequest
{
    private $tagName;
    private $id;
    private $issuer;
    private $destination;
    private $issueInstant;
    private $certificates;
    private $validators;
    private $notOnOrAfter;
    private $encryptedNameId;
    private $nameId;
    private $sessionIndexes;
    public function __construct(DOMElement $nb = NULL)
    {
        $this->tagName = "\x4c\x6f\147\x6f\165\164\122\145\161\x75\145\x73\x74";
        $this->id = Utilities::generateID();
        $this->issueInstant = time();
        $this->certificates = array();
        $this->validators = array();
        if (!($nb === NULL)) {
            goto Ga;
        }
        return;
        Ga:
        if ($nb->hasAttribute("\111\x44")) {
            goto yk;
        }
        throw new Exception("\115\x69\163\163\151\x6e\x67\40\111\x44\x20\141\164\x74\162\151\142\x75\x74\x65\x20\157\156\x20\x53\x41\115\x4c\x20\x6d\x65\163\x73\x61\x67\x65\56");
        yk:
        $this->id = $nb->getAttribute("\x49\104");
        if (!($nb->getAttribute("\126\x65\x72\x73\x69\x6f\x6e") !== "\62\56\60")) {
            goto gI;
        }
        throw new Exception("\125\156\163\x75\160\x70\x6f\x72\164\x65\144\40\166\x65\162\x73\151\x6f\x6e\72\40" . $nb->getAttribute("\x56\x65\x72\x73\x69\x6f\156"));
        gI:
        $this->issueInstant = Utilities::xsDateTimeToTimestamp($nb->getAttribute("\111\x73\163\165\145\111\156\163\164\141\x6e\x74"));
        if (!$nb->hasAttribute("\104\145\x73\164\x69\156\141\164\x69\157\156")) {
            goto K1;
        }
        $this->destination = $nb->getAttribute("\104\145\x73\x74\x69\x6e\141\x74\151\x6f\x6e");
        K1:
        $OS = Utilities::xpQuery($nb, "\56\x2f\163\x61\x6d\154\x5f\x61\x73\163\145\162\x74\x69\x6f\156\x3a\111\x73\163\165\145\162");
        if (empty($OS)) {
            goto VN;
        }
        $this->issuer = trim($OS[0]->textContent);
        VN:
        try {
            $f3 = Utilities::validateElement($nb);
            if (!($f3 !== FALSE)) {
                goto iE;
            }
            $this->certificates = $f3["\x43\145\x72\x74\x69\x66\151\143\x61\x74\145\x73"];
            $this->validators[] = array("\x46\x75\156\143\x74\151\157\x6e" => array("\125\x74\x69\x6c\151\164\151\145\x73", "\x76\141\x6c\x69\x64\x61\164\145\123\x69\147\x6e\x61\x74\165\x72\145"), "\104\x61\x74\x61" => $f3);
            iE:
        } catch (Exception $ZE) {
        }
        $this->sessionIndexes = array();
        if (!$nb->hasAttribute("\116\157\x74\117\156\117\162\x41\x66\164\x65\x72")) {
            goto vE;
        }
        $this->notOnOrAfter = Utilities::xsDateTimeToTimestamp($nb->getAttribute("\x4e\x6f\164\x4f\156\117\x72\x41\146\164\x65\x72"));
        vE:
        $Xe = Utilities::xpQuery($nb, "\x2e\x2f\163\141\x6d\154\x5f\141\163\x73\x65\162\x74\151\157\x6e\x3a\x4e\141\x6d\x65\111\104\x20\x7c\x20\x2e\x2f\x73\x61\155\154\x5f\141\163\x73\x65\x72\164\x69\157\156\72\x45\156\x63\x72\x79\x70\164\145\x64\111\104\57\170\145\x6e\143\x3a\x45\156\143\162\171\160\164\x65\x64\104\141\164\141");
        if (empty($Xe)) {
            goto Hn;
        }
        if (count($Xe) > 1) {
            goto PD;
        }
        goto Mr;
        Hn:
        throw new Exception("\x4d\x69\163\x73\x69\x6e\x67\40\74\x73\141\x6d\154\72\116\x61\x6d\x65\x49\x44\x3e\x20\x6f\162\x20\x3c\x73\x61\155\154\x3a\x45\x6e\143\x72\x79\160\164\145\144\x49\104\76\x20\151\156\x20\x3c\x73\x61\x6d\154\160\72\114\157\147\157\x75\x74\x52\x65\x71\x75\x65\163\164\x3e\56");
        goto Mr;
        PD:
        throw new Exception("\115\157\x72\x65\40\x74\x68\141\x6e\x20\157\x6e\x65\x20\x3c\x73\141\155\154\72\116\141\x6d\x65\x49\104\x3e\x20\157\x72\x20\x3c\163\141\x6d\x6c\x3a\105\156\x63\x72\x79\160\164\145\x64\x44\x3e\x20\151\x6e\x20\x3c\x73\x61\155\154\160\72\x4c\157\x67\x6f\x75\x74\x52\x65\161\165\145\163\x74\x3e\x2e");
        Mr:
        $Xe = $Xe[0];
        if ($Xe->localName === "\x45\156\x63\x72\x79\160\x74\x65\144\104\141\164\x61") {
            goto wk;
        }
        $this->nameId = Utilities::parseNameId($Xe);
        goto Ge;
        wk:
        $this->encryptedNameId = $Xe;
        Ge:
        $td = Utilities::xpQuery($nb, "\56\x2f\163\141\155\x6c\x5f\x70\162\157\x74\x6f\x63\157\x6c\x3a\x53\x65\x73\x73\151\157\156\x49\156\144\x65\x78");
        foreach ($td as $Kw) {
            $this->sessionIndexes[] = trim($Kw->textContent);
            a3:
        }
        QT:
    }
    public function getNotOnOrAfter()
    {
        return $this->notOnOrAfter;
    }
    public function setNotOnOrAfter($ib)
    {
        $this->notOnOrAfter = $ib;
    }
    public function isNameIdEncrypted()
    {
        if (!($this->encryptedNameId !== NULL)) {
            goto t_;
        }
        return TRUE;
        t_:
        return FALSE;
    }
    public function encryptNameId(XMLSecurityKey $Z1)
    {
        $li = new DOMDocument();
        $oO = $li->createElement("\x72\x6f\x6f\164");
        $li->appendChild($oO);
        SAML2_Utils::addNameId($oO, $this->nameId);
        $Xe = $oO->firstChild;
        SAML2_Utils::getContainer()->debugMessage($Xe, "\145\156\143\x72\x79\x70\164");
        $yu = new XMLSecEnc();
        $yu->setNode($Xe);
        $yu->type = XMLSecEnc::Element;
        $oL = new XMLSecurityKey(XMLSecurityKey::AES128_CBC);
        $oL->generateSessionKey();
        $yu->encryptKey($Z1, $oL);
        $this->encryptedNameId = $yu->encryptNode($oL);
        $this->nameId = NULL;
    }
    public function decryptNameId(XMLSecurityKey $Z1, array $XT = array())
    {
        if (!($this->encryptedNameId === NULL)) {
            goto Xs;
        }
        return;
        Xs:
        $Xe = SAML2_Utils::decryptElement($this->encryptedNameId, $Z1, $XT);
        SAML2_Utils::getContainer()->debugMessage($Xe, "\144\145\143\x72\x79\160\164");
        $this->nameId = SAML2_Utils::parseNameId($Xe);
        $this->encryptedNameId = NULL;
    }
    public function getNameId()
    {
        if (!($this->encryptedNameId !== NULL)) {
            goto KP;
        }
        throw new Exception("\x41\x74\164\x65\x6d\160\164\x65\x64\x20\x74\157\40\x72\145\x74\162\x69\145\166\145\40\x65\156\143\162\171\x70\x74\145\144\40\x4e\x61\x6d\x65\x49\x44\40\167\151\x74\150\157\x75\164\x20\144\x65\x63\x72\171\160\x74\x69\x6e\147\40\151\x74\40\146\x69\162\x73\x74\56");
        KP:
        return $this->nameId;
    }
    public function setNameId($Xe)
    {
        $this->nameId = $Xe;
    }
    public function getSessionIndexes()
    {
        return $this->sessionIndexes;
    }
    public function setSessionIndexes(array $td)
    {
        $this->sessionIndexes = $td;
    }
    public function getSessionIndex()
    {
        if (!empty($this->sessionIndexes)) {
            goto PQ;
        }
        return NULL;
        PQ:
        return $this->sessionIndexes[0];
    }
    public function setSessionIndex($Kw)
    {
        if (is_null($Kw)) {
            goto jG;
        }
        $this->sessionIndexes = array($Kw);
        goto vt;
        jG:
        $this->sessionIndexes = array();
        vt:
    }
    public function getId()
    {
        return $this->id;
    }
    public function setId($kE)
    {
        $this->id = $kE;
    }
    public function getIssueInstant()
    {
        return $this->issueInstant;
    }
    public function setIssueInstant($vG)
    {
        $this->issueInstant = $vG;
    }
    public function getDestination()
    {
        return $this->destination;
    }
    public function setDestination($LW)
    {
        $this->destination = $LW;
    }
    public function getIssuer()
    {
        return $this->issuer;
    }
    public function setIssuer($OS)
    {
        $this->issuer = $OS;
    }
}
