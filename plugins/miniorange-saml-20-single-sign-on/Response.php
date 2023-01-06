<?php


include "\101\163\163\x65\x72\x74\x69\157\156\56\160\x68\x70";
class SAML2_Response
{
    private $assertions;
    private $destination;
    private $certificates;
    private $signatureData;
    public function __construct(DOMElement $nb = NULL)
    {
        $this->assertions = array();
        $this->certificates = array();
        if (!($nb === NULL)) {
            goto kE;
        }
        return;
        kE:
        $f3 = Utilities::validateElement($nb);
        if (!($f3 !== FALSE)) {
            goto uE;
        }
        $this->certificates = $f3["\103\145\162\164\x69\x66\151\x63\141\164\x65\x73"];
        $this->signatureData = $f3;
        uE:
        if (!$nb->hasAttribute("\x44\x65\x73\164\151\x6e\141\164\151\157\156")) {
            goto dm;
        }
        $this->destination = $nb->getAttribute("\x44\x65\x73\x74\151\x6e\x61\x74\151\x6f\x6e");
        dm:
        $nC = $nb->firstChild;
        b_:
        if (!($nC !== NULL)) {
            goto OW;
        }
        if (!($nC->namespaceURI !== "\165\x72\156\x3a\157\x61\163\151\x73\x3a\156\141\x6d\x65\163\x3a\x74\143\x3a\x53\x41\x4d\x4c\x3a\x32\x2e\x30\x3a\x61\x73\163\x65\x72\x74\x69\x6f\156")) {
            goto gz;
        }
        goto em;
        gz:
        if (!($nC->localName === "\x41\x73\x73\x65\x72\164\x69\157\x6e" || $nC->localName === "\x45\156\x63\x72\x79\x70\x74\145\x64\x41\163\163\145\162\164\151\157\x6e")) {
            goto wa;
        }
        $this->assertions[] = new SAML2_Assertion($nC);
        wa:
        em:
        $nC = $nC->nextSibling;
        goto b_;
        OW:
    }
    public function getAssertions()
    {
        return $this->assertions;
    }
    public function setAssertions(array $nG)
    {
        $this->assertions = $nG;
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
