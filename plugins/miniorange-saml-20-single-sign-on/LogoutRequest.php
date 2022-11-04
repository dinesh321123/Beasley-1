<?php


include_once "\125\x74\151\x6c\151\164\x69\145\163\56\x70\x68\x70";
include_once "\170\x6d\154\x73\x65\x63\x6c\151\142\163\56\x70\150\x70";
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
    public function __construct(DOMElement $mf = NULL)
    {
        $this->tagName = "\114\x6f\x67\x6f\x75\x74\x52\x65\x71\x75\145\x73\164";
        $this->id = Utilities::generateID();
        $this->issueInstant = time();
        $this->certificates = array();
        $this->validators = array();
        if (!($mf === NULL)) {
            goto oD;
        }
        return;
        oD:
        if ($mf->hasAttribute("\x49\104")) {
            goto zf;
        }
        throw new Exception("\x4d\151\x73\x73\x69\156\x67\40\111\x44\40\x61\x74\164\x72\x69\x62\x75\164\x65\x20\157\156\x20\123\101\115\x4c\x20\155\145\163\x73\141\147\145\56");
        zf:
        $this->id = $mf->getAttribute("\x49\x44");
        if (!($mf->getAttribute("\x56\145\x72\x73\151\x6f\x6e") !== "\x32\56\x30")) {
            goto eZ;
        }
        throw new Exception("\x55\x6e\x73\x75\x70\x70\x6f\162\x74\145\144\40\x76\145\162\x73\x69\x6f\x6e\x3a\40" . $mf->getAttribute("\126\x65\162\x73\x69\x6f\156"));
        eZ:
        $this->issueInstant = Utilities::xsDateTimeToTimestamp($mf->getAttribute("\111\x73\x73\x75\145\x49\156\163\164\141\156\x74"));
        if (!$mf->hasAttribute("\104\x65\163\x74\151\x6e\x61\164\x69\157\156")) {
            goto EZ;
        }
        $this->destination = $mf->getAttribute("\x44\145\x73\x74\151\156\141\164\151\157\156");
        EZ:
        $Jm = Utilities::xpQuery($mf, "\56\x2f\x73\141\155\x6c\137\141\163\163\x65\162\164\x69\x6f\156\x3a\x49\163\163\x75\x65\162");
        if (empty($Jm)) {
            goto vf;
        }
        $this->issuer = trim($Jm[0]->textContent);
        vf:
        try {
            $p9 = Utilities::validateElement($mf);
            if (!($p9 !== FALSE)) {
                goto K2;
            }
            $this->certificates = $p9["\103\145\162\x74\151\146\x69\143\141\164\x65\163"];
            $this->validators[] = array("\x46\165\x6e\143\164\151\x6f\156" => array("\x55\164\x69\x6c\x69\164\151\145\163", "\x76\x61\x6c\x69\144\141\x74\145\123\x69\x67\x6e\x61\164\x75\x72\145"), "\104\x61\164\141" => $p9);
            K2:
        } catch (Exception $wD) {
        }
        $this->sessionIndexes = array();
        if (!$mf->hasAttribute("\x4e\157\164\x4f\156\x4f\x72\x41\x66\x74\x65\162")) {
            goto Zx;
        }
        $this->notOnOrAfter = Utilities::xsDateTimeToTimestamp($mf->getAttribute("\116\x6f\164\117\156\117\162\101\x66\164\x65\162"));
        Zx:
        $Ix = Utilities::xpQuery($mf, "\x2e\57\163\141\155\154\137\x61\163\x73\145\162\x74\151\157\156\x3a\x4e\x61\x6d\145\111\x44\40\x7c\40\x2e\57\163\141\155\154\x5f\x61\x73\x73\145\x72\164\151\157\156\72\x45\x6e\143\x72\x79\160\164\145\144\111\104\57\x78\x65\156\143\x3a\105\x6e\143\162\x79\160\x74\145\144\104\141\164\x61");
        if (empty($Ix)) {
            goto XO;
        }
        if (count($Ix) > 1) {
            goto Uo;
        }
        goto in;
        XO:
        throw new Exception("\x4d\151\163\163\151\x6e\147\x20\74\x73\x61\x6d\x6c\x3a\116\141\155\x65\111\104\76\40\157\162\40\x3c\x73\x61\x6d\154\72\x45\x6e\x63\x72\171\x70\164\x65\144\x49\x44\76\x20\x69\156\x20\x3c\x73\x61\155\154\x70\72\x4c\x6f\x67\157\x75\164\x52\145\x71\165\145\x73\164\x3e\x2e");
        goto in;
        Uo:
        throw new Exception("\115\157\162\145\x20\x74\x68\141\x6e\40\x6f\156\x65\40\74\163\x61\155\154\x3a\116\x61\155\145\x49\104\x3e\x20\x6f\162\40\x3c\163\x61\155\x6c\72\x45\156\143\x72\171\x70\164\x65\144\104\76\40\151\x6e\40\74\163\141\x6d\x6c\x70\72\x4c\x6f\x67\157\x75\x74\x52\145\x71\165\x65\163\164\76\x2e");
        in:
        $Ix = $Ix[0];
        if ($Ix->localName === "\105\x6e\143\x72\171\160\164\x65\x64\104\141\x74\x61") {
            goto Iv;
        }
        $this->nameId = Utilities::parseNameId($Ix);
        goto ZS;
        Iv:
        $this->encryptedNameId = $Ix;
        ZS:
        $eX = Utilities::xpQuery($mf, "\x2e\57\163\x61\155\x6c\137\x70\162\x6f\164\157\x63\157\x6c\x3a\123\x65\x73\163\151\x6f\x6e\111\156\x64\145\170");
        foreach ($eX as $L_) {
            $this->sessionIndexes[] = trim($L_->textContent);
            eq:
        }
        DW:
    }
    public function getNotOnOrAfter()
    {
        return $this->notOnOrAfter;
    }
    public function setNotOnOrAfter($LW)
    {
        $this->notOnOrAfter = $LW;
    }
    public function isNameIdEncrypted()
    {
        if (!($this->encryptedNameId !== NULL)) {
            goto Rv;
        }
        return TRUE;
        Rv:
        return FALSE;
    }
    public function encryptNameId(XMLSecurityKey $XC)
    {
        $ZR = new DOMDocument();
        $W2 = $ZR->createElement("\x72\157\157\164");
        $ZR->appendChild($W2);
        SAML2_Utils::addNameId($W2, $this->nameId);
        $Ix = $W2->firstChild;
        SAML2_Utils::getContainer()->debugMessage($Ix, "\x65\156\x63\162\x79\x70\x74");
        $lt = new XMLSecEnc();
        $lt->setNode($Ix);
        $lt->type = XMLSecEnc::Element;
        $A1 = new XMLSecurityKey(XMLSecurityKey::AES128_CBC);
        $A1->generateSessionKey();
        $lt->encryptKey($XC, $A1);
        $this->encryptedNameId = $lt->encryptNode($A1);
        $this->nameId = NULL;
    }
    public function decryptNameId(XMLSecurityKey $XC, array $F_ = array())
    {
        if (!($this->encryptedNameId === NULL)) {
            goto JM;
        }
        return;
        JM:
        $Ix = SAML2_Utils::decryptElement($this->encryptedNameId, $XC, $F_);
        SAML2_Utils::getContainer()->debugMessage($Ix, "\x64\145\x63\x72\x79\160\x74");
        $this->nameId = SAML2_Utils::parseNameId($Ix);
        $this->encryptedNameId = NULL;
    }
    public function getNameId()
    {
        if (!($this->encryptedNameId !== NULL)) {
            goto hr;
        }
        throw new Exception("\x41\164\164\x65\x6d\160\x74\x65\x64\x20\164\157\x20\x72\x65\x74\162\151\145\x76\145\x20\145\156\x63\x72\171\x70\164\145\144\x20\116\141\x6d\x65\111\x44\40\x77\151\x74\x68\x6f\x75\164\40\x64\x65\143\162\171\160\x74\151\x6e\147\40\x69\x74\x20\146\x69\162\x73\164\56");
        hr:
        return $this->nameId;
    }
    public function setNameId($Ix)
    {
        $this->nameId = $Ix;
    }
    public function getSessionIndexes()
    {
        return $this->sessionIndexes;
    }
    public function setSessionIndexes(array $eX)
    {
        $this->sessionIndexes = $eX;
    }
    public function getSessionIndex()
    {
        if (!empty($this->sessionIndexes)) {
            goto uN;
        }
        return NULL;
        uN:
        return $this->sessionIndexes[0];
    }
    public function setSessionIndex($L_)
    {
        if (is_null($L_)) {
            goto C1;
        }
        $this->sessionIndexes = array($L_);
        goto AY;
        C1:
        $this->sessionIndexes = array();
        AY:
    }
    public function getId()
    {
        return $this->id;
    }
    public function setId($Wz)
    {
        $this->id = $Wz;
    }
    public function getIssueInstant()
    {
        return $this->issueInstant;
    }
    public function setIssueInstant($b4)
    {
        $this->issueInstant = $b4;
    }
    public function getDestination()
    {
        return $this->destination;
    }
    public function setDestination($Nn)
    {
        $this->destination = $Nn;
    }
    public function getIssuer()
    {
        return $this->issuer;
    }
    public function setIssuer($Jm)
    {
        $this->issuer = $Jm;
    }
}
