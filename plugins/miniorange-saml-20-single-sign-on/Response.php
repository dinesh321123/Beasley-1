<?php


include "\101\x73\x73\145\x72\x74\x69\x6f\156\56\x70\x68\160";
class SAML2_Response
{
    private $assertions;
    private $destination;
    private $certificates;
    private $signatureData;
    public function __construct(DOMElement $mf = NULL, $n_)
    {
        $this->assertions = array();
        $this->certificates = array();
        if (!($mf === NULL)) {
            goto ts8;
        }
        return;
        ts8:
        $p9 = Utilities::validateElement($mf);
        if (!($p9 !== FALSE)) {
            goto iCS;
        }
        $this->certificates = $p9["\103\145\162\164\x69\146\x69\x63\141\164\145\163"];
        $this->signatureData = $p9;
        iCS:
        if (!$mf->hasAttribute("\104\x65\163\x74\151\156\141\x74\151\x6f\156")) {
            goto zkV;
        }
        $this->destination = $mf->getAttribute("\104\145\x73\164\x69\x6e\x61\164\151\157\x6e");
        zkV:
        $p8 = $mf->firstChild;
        fBO:
        if (!($p8 !== NULL)) {
            goto oub;
        }
        if (!($p8->namespaceURI !== "\x75\x72\x6e\72\x6f\x61\x73\151\x73\72\x6e\141\155\145\x73\x3a\x74\x63\72\123\101\115\x4c\x3a\x32\56\60\x3a\141\163\163\145\x72\x74\151\157\x6e")) {
            goto dZd;
        }
        goto irj;
        dZd:
        if (!($p8->localName === "\x41\163\163\x65\162\x74\151\x6f\x6e" || $p8->localName === "\105\x6e\143\x72\171\160\x74\x65\x64\101\163\163\145\x72\x74\151\x6f\156")) {
            goto vmz;
        }
        $this->assertions[] = new SAML2_Assertion($p8, $n_);
        vmz:
        irj:
        $p8 = $p8->nextSibling;
        goto fBO;
        oub:
    }
    public function getAssertions()
    {
        return $this->assertions;
    }
    public function setAssertions(array $ZY)
    {
        $this->assertions = $ZY;
    }
    public function getDestination()
    {
        return $this->destination;
    }
    public function getCertificates()
    {
        return $this->certificates;
    }
    public function getSignatureData()
    {
        return $this->signatureData;
    }
}
