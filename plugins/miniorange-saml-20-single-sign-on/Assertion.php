<?php


include_once "\125\164\x69\x6c\x69\x74\151\x65\163\x2e\x70\150\x70";
include_once "\170\155\x6c\x73\145\x63\154\x69\x62\x73\x2e\x70\150\x70";
use RobRichards\XMLSecLibs\XMLSecurityKey;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecEnc;
class SAML2_Assertion
{
    private $id;
    private $issueInstant;
    private $issuer;
    private $nameId;
    private $encryptedNameId;
    private $encryptedAttribute;
    private $encryptionKey;
    private $notBefore;
    private $notOnOrAfter;
    private $validAudiences;
    private $sessionNotOnOrAfter;
    private $sessionIndex;
    private $authnInstant;
    private $authnContextClassRef;
    private $authnContextDecl;
    private $authnContextDeclRef;
    private $AuthenticatingAuthority;
    private $attributes;
    private $nameFormat;
    private $signatureKey;
    private $certificates;
    private $signatureData;
    private $requiredEncAttributes;
    private $SubjectConfirmation;
    protected $wasSignedAtConstruction = FALSE;
    public function __construct(DOMElement $nb = NULL)
    {
        $this->id = Utilities::generateId();
        $this->issueInstant = Utilities::generateTimestamp();
        $this->issuer = '';
        $this->authnInstant = Utilities::generateTimestamp();
        $this->attributes = array();
        $this->nameFormat = "\x75\x72\x6e\72\157\141\163\x69\x73\72\x6e\x61\x6d\x65\163\x3a\x74\x63\x3a\x53\101\x4d\114\x3a\x31\56\61\x3a\x6e\x61\155\x65\x69\x64\x2d\146\157\162\155\x61\x74\72\x75\x6e\x73\x70\x65\143\x69\x66\151\x65\x64";
        $this->certificates = array();
        $this->AuthenticatingAuthority = array();
        $this->SubjectConfirmation = array();
        if (!($nb === NULL)) {
            goto HV;
        }
        return;
        HV:
        if (!($nb->localName === "\105\x6e\143\x72\171\x70\x74\145\x64\x41\163\163\145\162\164\151\157\x6e")) {
            goto eG;
        }
        $K9 = Utilities::xpQuery($nb, "\56\x2f\x78\x65\156\x63\72\x45\x6e\x63\x72\171\x70\164\145\x64\x44\141\164\141");
        $ug = Utilities::xpQuery($nb, "\x2e\x2f\170\x65\156\x63\x3a\105\x6e\143\x72\x79\x70\x74\145\x64\104\x61\x74\x61\x2f\x64\x73\x3a\x4b\145\x79\x49\x6e\146\x6f\57\170\145\x6e\143\72\105\x6e\x63\x72\x79\160\x74\x65\144\x4b\x65\x79");
        $tO = '';
        if (empty($ug)) {
            goto bS;
        }
        $tO = $ug[0]->firstChild->getAttribute("\x41\x6c\147\x6f\162\x69\x74\150\x6d");
        goto Mh;
        bS:
        $ug = Utilities::xpQuery($nb, "\56\57\x78\x65\156\143\72\x45\x6e\x63\162\171\x70\164\145\144\113\145\171\x2f\170\145\x6e\x63\72\x45\x6e\143\x72\x79\x70\164\151\x6f\156\x4d\145\164\150\157\144");
        $tO = $ug[0]->getAttribute("\101\154\x67\157\162\x69\x74\150\155");
        Mh:
        $jm = Utilities::getEncryptionAlgorithm($tO);
        if (count($K9) === 0) {
            goto hW;
        }
        if (count($K9) > 1) {
            goto VH;
        }
        goto Sw;
        hW:
        throw new Exception("\115\x69\163\x73\x69\156\147\x20\145\156\x63\x72\171\x70\164\145\144\40\144\x61\164\x61\x20\151\156\40\74\163\141\155\x6c\x3a\x45\156\x63\x72\x79\x70\164\145\x64\101\x73\x73\x65\x72\164\x69\x6f\x6e\76\x2e");
        goto Sw;
        VH:
        throw new Exception("\x4d\x6f\162\145\40\164\x68\141\156\40\x6f\156\145\40\x65\156\143\162\x79\160\164\x65\144\40\x64\141\164\141\40\145\x6c\145\155\145\x6e\164\40\151\156\40\x3c\x73\x61\x6d\x6c\72\105\x6e\x63\162\x79\160\x74\145\x64\101\x73\x73\x65\162\164\x69\157\156\76\56");
        Sw:
        $Z1 = new XMLSecurityKey($jm, array("\x74\171\160\x65" => "\160\162\151\x76\141\x74\x65"));
        $Oy = get_site_option("\155\157\137\x73\x61\155\154\137\143\x75\x72\x72\145\x6e\x74\137\143\145\x72\x74\137\x70\x72\151\x76\141\x74\x65\137\153\x65\x79");
        $Z1->loadKey($Oy, FALSE);
        $H7 = new XMLSecurityKey($jm, array("\x74\171\160\x65" => "\160\162\151\x76\141\164\145"));
        $Pw = plugin_dir_path(__FILE__) . "\x72\x65\163\x6f\165\x72\143\x65\x73" . DIRECTORY_SEPARATOR . "\155\151\x6e\151\x6f\162\141\156\147\x65\x5f\163\160\x5f\x70\162\151\x76\137\153\145\x79\56\153\x65\171";
        $H7->loadKey($Pw, TRUE);
        $XT = array();
        $nb = Utilities::decryptElement($K9[0], $Z1, $XT, $H7);
        eG:
        if ($nb->hasAttribute("\111\104")) {
            goto IC;
        }
        throw new Exception("\x4d\x69\x73\x73\x69\x6e\x67\x20\x49\104\x20\x61\164\164\x72\x69\142\165\164\x65\x20\x6f\156\40\123\101\x4d\x4c\40\x61\x73\x73\x65\x72\164\151\157\x6e\56");
        IC:
        $this->id = $nb->getAttribute("\111\104");
        if (!($nb->getAttribute("\126\145\162\x73\151\x6f\156") !== "\62\56\60")) {
            goto jA;
        }
        throw new Exception("\125\156\163\165\x70\160\157\162\164\x65\144\x20\166\145\x72\163\151\x6f\x6e\x3a\40" . $nb->getAttribute("\x56\145\x72\x73\x69\157\x6e"));
        jA:
        $this->issueInstant = Utilities::xsDateTimeToTimestamp($nb->getAttribute("\111\x73\163\165\x65\x49\156\x73\164\x61\156\x74"));
        $OS = Utilities::xpQuery($nb, "\56\57\x73\x61\x6d\x6c\137\141\163\x73\x65\162\x74\151\157\156\x3a\x49\163\163\x75\145\x72");
        if (!empty($OS)) {
            goto hd;
        }
        throw new Exception("\x4d\151\x73\163\151\x6e\147\x20\74\163\141\x6d\154\x3a\x49\x73\x73\x75\x65\x72\x3e\40\151\x6e\x20\x61\x73\163\x65\x72\x74\151\x6f\156\56");
        hd:
        $this->issuer = trim($OS[0]->textContent);
        $this->parseConditions($nb);
        $this->parseAuthnStatement($nb);
        $this->parseAttributes($nb);
        $this->parseEncryptedAttributes($nb);
        $this->parseSignature($nb);
        $this->parseSubject($nb);
    }
    private function parseSubject(DOMElement $nb)
    {
        $kP = Utilities::xpQuery($nb, "\x2e\x2f\x73\141\x6d\x6c\137\x61\x73\x73\x65\x72\x74\151\157\x6e\72\x53\165\x62\x6a\x65\x63\164");
        if (empty($kP)) {
            goto le;
        }
        if (count($kP) > 1) {
            goto tm;
        }
        goto LB;
        le:
        return;
        goto LB;
        tm:
        throw new Exception("\115\157\162\x65\x20\164\150\x61\156\40\157\156\145\40\x3c\163\x61\x6d\154\x3a\123\165\x62\152\x65\143\x74\x3e\40\151\x6e\40\74\x73\141\155\x6c\x3a\101\x73\163\x65\x72\164\x69\x6f\156\x3e\x2e");
        LB:
        $kP = $kP[0];
        $Xe = Utilities::xpQuery($kP, "\56\x2f\x73\x61\x6d\154\x5f\141\163\163\x65\162\164\x69\157\156\72\x4e\x61\x6d\145\x49\104\40\174\40\56\x2f\163\141\x6d\154\137\x61\163\163\x65\x72\164\151\157\156\x3a\105\156\143\x72\x79\x70\164\145\x64\111\104\x2f\170\145\156\x63\x3a\105\156\143\162\171\160\164\x65\x64\104\x61\164\x61");
        if (empty($Xe)) {
            goto Jc;
        }
        if (count($Xe) > 1) {
            goto t4;
        }
        goto qt;
        Jc:
        if ($_POST["\122\145\154\x61\171\x53\x74\x61\x74\x65"] == "\x74\145\x73\x74\x56\x61\154\x69\x64\141\164\145") {
            goto Sh;
        }
        wp_die("\127\x65\40\x63\x6f\x75\x6c\x64\40\156\157\x74\40\x73\x69\147\156\x20\171\157\x75\40\151\x6e\x2e\x20\x50\x6c\145\x61\163\145\40\x63\x6f\x6e\x74\141\x63\x74\40\x79\x6f\165\x72\40\141\x64\x6d\x69\x6e\x69\x73\164\162\x61\164\x6f\x72");
        goto cM;
        Sh:
        echo "\74\144\x69\x76\x20\163\164\x79\154\x65\75\42\x66\x6f\x6e\164\x2d\146\141\x6d\151\x6c\x79\x3a\103\x61\x6c\x69\142\162\151\x3b\x70\x61\144\x64\x69\156\147\72\60\40\x33\x25\x3b\x22\76";
        echo "\x3c\x64\151\x76\40\163\164\171\154\145\75\42\143\157\154\157\162\72\40\43\x61\71\x34\64\x34\x32\73\142\141\x63\153\x67\162\157\165\156\x64\55\143\x6f\x6c\157\162\72\40\43\x66\x32\144\x65\144\x65\73\x70\x61\144\144\151\x6e\147\x3a\40\x31\x35\160\x78\73\x6d\x61\x72\x67\151\x6e\55\x62\157\164\164\x6f\x6d\x3a\x20\x32\x30\160\x78\x3b\x74\x65\170\164\x2d\x61\x6c\151\147\x6e\x3a\143\x65\156\x74\145\x72\x3b\x62\157\162\144\x65\x72\x3a\x31\x70\x78\x20\x73\x6f\x6c\x69\144\40\x23\105\x36\x42\63\x42\62\73\146\157\156\164\55\x73\151\172\145\x3a\61\70\160\164\73\42\x3e\40\x45\122\x52\x4f\x52\74\57\x64\151\x76\76\12\x20\x20\40\x20\40\40\40\x20\40\40\x20\74\x64\x69\166\x20\x73\164\x79\x6c\145\75\42\143\157\154\157\162\72\x20\43\x61\71\64\x34\64\x32\73\x66\157\x6e\164\x2d\x73\151\x7a\145\72\x31\64\x70\x74\73\40\155\x61\162\x67\151\x6e\x2d\142\157\164\x74\x6f\x6d\72\62\60\160\170\x3b\42\76\74\160\x3e\x3c\x73\x74\x72\x6f\x6e\147\76\x45\x72\162\157\162\72\40\74\57\x73\164\x72\157\156\147\76\x4d\151\x73\163\x69\156\x67\40\40\116\x61\155\x65\x49\104\40\x6f\x72\x20\105\x6e\143\162\171\160\164\145\x64\111\104\40\151\x6e\x20\123\x41\115\114\40\x52\145\163\160\157\x6e\163\145\74\x2f\x70\76\12\x20\x20\40\40\40\40\x20\x20\40\x20\40\x20\40\x20\40\x20\x3c\160\x3e\x50\154\145\141\163\x65\40\143\157\x6e\164\x61\x63\164\40\x79\157\x75\162\40\141\144\x6d\x69\x6e\151\163\164\x72\x61\164\157\162\40\141\x6e\144\x20\162\x65\160\x6f\x72\x74\x20\x74\150\145\x20\x66\157\x6c\x6c\157\x77\151\x6e\x67\40\145\x72\x72\x6f\162\x3a\74\57\x70\x3e\xa\x20\40\x20\40\40\x20\x20\x20\x20\x20\40\40\x20\x20\x20\40\x3c\160\x3e\74\x73\164\x72\157\156\x67\76\120\x6f\x73\163\151\142\x6c\145\40\103\141\x75\x73\x65\72\x3c\x2f\x73\x74\162\157\156\147\x3e\40\x4e\x61\155\x65\111\x44\40\156\x6f\164\40\x66\157\165\x6e\x64\x20\x69\x6e\x20\123\101\115\114\40\122\145\163\x70\157\x6e\163\x65\x20\x73\165\142\152\145\x63\x74\x3c\57\160\x3e\xa\x20\x20\40\x20\x20\40\x20\x20\x20\40\40\x20\40\40\x20\x20\74\x2f\x64\x69\x76\x3e\12\40\40\40\x20\x20\x20\40\40\40\x20\x20\x20\x20\40\40\x20\x3c\x64\151\x76\40\163\164\x79\x6c\x65\x3d\42\x6d\x61\x72\x67\151\156\x3a\63\45\x3b\144\x69\x73\160\154\141\171\x3a\x62\154\157\x63\x6b\73\164\x65\x78\164\55\x61\x6c\x69\x67\x6e\72\x63\x65\x6e\x74\145\162\x3b\42\76\12\40\x20\40\40\40\x20\x20\x20\40\x20\x20\x20\40\x20\x20\x20\74\144\x69\166\x20\x73\x74\x79\154\145\75\42\155\x61\x72\x67\x69\156\x3a\63\45\x3b\x64\x69\163\160\x6c\x61\171\x3a\x62\154\157\143\x6b\73\x74\x65\170\164\x2d\x61\x6c\x69\x67\x6e\x3a\143\x65\156\164\145\x72\73\42\x3e\x3c\x69\156\160\165\164\40\x73\164\x79\x6c\145\x3d\42\x70\141\144\x64\x69\x6e\x67\x3a\x31\x25\x3b\167\x69\144\x74\150\x3a\61\60\60\x70\x78\x3b\x62\141\x63\x6b\147\162\157\165\x6e\144\x3a\x20\43\60\x30\71\x31\x43\104\x20\156\157\x6e\145\40\162\145\x70\x65\141\164\x20\163\143\x72\157\x6c\x6c\x20\x30\x25\40\x30\x25\x3b\x63\x75\x72\x73\x6f\x72\x3a\x20\x70\157\x69\156\x74\145\162\x3b\146\x6f\156\x74\x2d\x73\151\x7a\x65\72\61\65\160\170\73\x62\157\x72\x64\x65\162\55\x77\x69\144\164\x68\x3a\x20\x31\x70\170\x3b\142\157\x72\x64\145\162\x2d\x73\164\x79\154\x65\72\x20\163\157\x6c\x69\144\73\142\157\162\x64\x65\x72\x2d\162\x61\x64\x69\x75\x73\x3a\40\63\160\170\x3b\x77\150\x69\164\x65\55\x73\x70\141\x63\145\x3a\x20\156\x6f\x77\x72\141\x70\73\x62\157\x78\x2d\163\151\x7a\151\x6e\147\x3a\x20\x62\157\162\144\x65\x72\x2d\x62\157\170\x3b\142\157\x72\144\145\162\55\x63\x6f\154\157\162\x3a\40\x23\x30\60\x37\63\101\101\73\x62\157\170\x2d\163\150\141\144\x6f\167\x3a\40\60\x70\x78\40\x31\x70\170\x20\60\160\x78\40\x72\147\x62\141\x28\61\62\x30\x2c\40\x32\x30\60\54\x20\x32\x33\60\54\x20\x30\x2e\66\51\x20\151\x6e\163\x65\x74\x3b\x63\x6f\x6c\157\x72\72\40\x23\x46\x46\x46\73\x22\164\171\160\x65\x3d\42\x62\x75\164\164\x6f\x6e\x22\x20\x76\x61\x6c\x75\145\x3d\42\x44\x6f\x6e\x65\x22\x20\x6f\156\103\154\x69\143\x6b\x3d\42\163\145\154\x66\56\x63\x6c\x6f\x73\x65\x28\x29\73\42\x3e\74\x2f\x64\x69\x76\x3e";
        die;
        cM:
        goto qt;
        t4:
        throw new Exception("\x4d\157\x72\145\x20\164\150\141\x6e\40\x6f\156\145\x20\x3c\163\141\x6d\x6c\72\116\x61\x6d\x65\x49\x44\76\40\x6f\162\x20\x3c\163\141\155\154\x3a\x45\156\143\162\171\160\164\145\x64\104\x3e\x20\151\156\40\x3c\163\x61\x6d\154\x3a\123\x75\142\x6a\145\143\164\76\56");
        qt:
        $Xe = $Xe[0];
        if ($Xe->localName === "\105\156\143\x72\x79\160\164\x65\144\104\141\164\141") {
            goto Qz;
        }
        $this->nameId = Utilities::parseNameId($Xe);
        goto Mu;
        Qz:
        $this->encryptedNameId = $Xe;
        Mu:
    }
    private function parseConditions(DOMElement $nb)
    {
        $fs = Utilities::xpQuery($nb, "\56\57\x73\141\x6d\154\137\x61\163\x73\145\162\164\x69\157\156\x3a\103\157\156\144\x69\164\151\157\156\x73");
        if (empty($fs)) {
            goto PI;
        }
        if (count($fs) > 1) {
            goto Lt;
        }
        goto uJ;
        PI:
        return;
        goto uJ;
        Lt:
        throw new Exception("\115\x6f\162\x65\40\164\x68\141\x6e\x20\157\x6e\145\40\x3c\163\x61\155\154\72\x43\x6f\x6e\144\x69\x74\x69\157\x6e\x73\x3e\40\151\x6e\40\74\x73\141\x6d\154\x3a\x41\163\163\145\x72\164\x69\x6f\156\x3e\x2e");
        uJ:
        $fs = $fs[0];
        if (!$fs->hasAttribute("\116\157\x74\102\145\146\x6f\x72\x65")) {
            goto qJ;
        }
        $mC = Utilities::xsDateTimeToTimestamp($fs->getAttribute("\x4e\x6f\164\102\x65\146\157\162\145"));
        if (!($this->notBefore === NULL || $this->notBefore < $mC)) {
            goto GC;
        }
        $this->notBefore = $mC;
        GC:
        qJ:
        if (!$fs->hasAttribute("\x4e\x6f\164\x4f\x6e\x4f\x72\101\146\164\145\162")) {
            goto pq;
        }
        $ib = Utilities::xsDateTimeToTimestamp($fs->getAttribute("\x4e\157\164\x4f\x6e\117\162\x41\x66\x74\x65\162"));
        if (!($this->notOnOrAfter === NULL || $this->notOnOrAfter > $ib)) {
            goto tW;
        }
        $this->notOnOrAfter = $ib;
        tW:
        pq:
        $nC = $fs->firstChild;
        O1:
        if (!($nC !== NULL)) {
            goto Dd;
        }
        if (!$nC instanceof DOMText) {
            goto l6;
        }
        goto W3;
        l6:
        if (!($nC->namespaceURI !== "\165\162\156\72\157\141\163\x69\163\x3a\x6e\141\155\x65\163\72\x74\143\x3a\x53\x41\x4d\114\x3a\x32\56\x30\72\141\x73\x73\145\x72\164\151\157\156")) {
            goto qP;
        }
        throw new Exception("\x55\x6e\x6b\156\x6f\x77\x6e\40\156\x61\x6d\145\x73\160\x61\143\x65\x20\x6f\146\40\x63\x6f\156\x64\x69\164\x69\x6f\x6e\x3a\40" . var_export($nC->namespaceURI, TRUE));
        qP:
        switch ($nC->localName) {
            case "\101\165\144\151\145\x6e\143\x65\122\x65\163\164\162\151\143\x74\151\x6f\156":
                $yW = Utilities::extractStrings($nC, "\x75\162\x6e\x3a\157\x61\x73\x69\x73\72\x6e\141\155\145\163\x3a\x74\143\x3a\123\101\115\114\x3a\62\x2e\x30\x3a\141\163\163\x65\x72\x74\x69\157\x6e", "\101\x75\144\151\145\156\143\x65");
                if ($this->validAudiences === NULL) {
                    goto wu;
                }
                $this->validAudiences = array_intersect($this->validAudiences, $yW);
                goto y0;
                wu:
                $this->validAudiences = $yW;
                y0:
                goto k9;
            case "\117\156\145\124\151\x6d\x65\125\x73\145":
                goto k9;
            case "\x50\x72\x6f\170\171\x52\x65\163\164\162\151\143\164\151\x6f\x6e":
                goto k9;
            default:
                throw new Exception("\125\156\x6b\x6e\157\167\x6e\40\143\157\x6e\x64\x69\164\x69\157\x6e\72\40" . var_export($nC->localName, TRUE));
        }
        x2:
        k9:
        W3:
        $nC = $nC->nextSibling;
        goto O1;
        Dd:
    }
    private function parseAuthnStatement(DOMElement $nb)
    {
        $Do = Utilities::xpQuery($nb, "\56\57\x73\141\155\154\x5f\x61\x73\x73\x65\162\x74\x69\157\156\x3a\x41\x75\x74\150\156\123\x74\141\164\145\155\145\156\164");
        if (empty($Do)) {
            goto Ko;
        }
        if (count($Do) > 1) {
            goto dF;
        }
        goto mu;
        Ko:
        $this->authnInstant = NULL;
        return;
        goto mu;
        dF:
        throw new Exception("\115\157\x72\x65\x20\164\150\141\x74\40\157\x6e\x65\x20\74\163\x61\155\154\x3a\101\165\x74\150\x6e\123\x74\x61\x74\x65\x6d\x65\156\164\76\40\151\156\40\74\163\x61\x6d\154\x3a\101\x73\x73\x65\x72\164\x69\157\156\76\x20\156\157\x74\40\163\165\160\x70\157\x72\x74\145\144\x2e");
        mu:
        $s0 = $Do[0];
        if ($s0->hasAttribute("\x41\165\x74\150\156\x49\156\x73\x74\x61\156\x74")) {
            goto j0;
        }
        throw new Exception("\115\151\x73\163\151\156\x67\x20\162\145\x71\x75\x69\x72\145\x64\x20\101\165\164\x68\156\x49\x6e\163\x74\141\x6e\164\x20\x61\164\164\162\151\x62\165\164\145\40\x6f\x6e\x20\x3c\x73\141\155\154\72\101\x75\164\x68\156\123\x74\141\164\145\155\145\x6e\164\76\56");
        j0:
        $this->authnInstant = Utilities::xsDateTimeToTimestamp($s0->getAttribute("\x41\x75\164\150\156\x49\156\163\x74\141\x6e\x74"));
        if (!$s0->hasAttribute("\x53\145\x73\163\x69\157\156\x4e\x6f\x74\117\156\117\x72\101\146\164\x65\x72")) {
            goto iB;
        }
        $this->sessionNotOnOrAfter = Utilities::xsDateTimeToTimestamp($s0->getAttribute("\x53\x65\163\163\x69\x6f\x6e\x4e\x6f\164\x4f\156\117\162\x41\x66\x74\x65\x72"));
        iB:
        if (!$s0->hasAttribute("\123\145\x73\x73\151\157\156\x49\x6e\144\145\170")) {
            goto sK;
        }
        $this->sessionIndex = $s0->getAttribute("\x53\145\x73\163\x69\x6f\x6e\111\x6e\144\145\x78");
        sK:
        $this->parseAuthnContext($s0);
    }
    private function parseAuthnContext(DOMElement $jh)
    {
        $Bb = Utilities::xpQuery($jh, "\56\57\163\141\155\x6c\137\x61\163\x73\x65\162\164\151\157\156\x3a\x41\x75\x74\x68\x6e\x43\x6f\x6e\x74\x65\170\x74");
        if (count($Bb) > 1) {
            goto Nw;
        }
        if (empty($Bb)) {
            goto Lh;
        }
        goto ot;
        Nw:
        throw new Exception("\x4d\157\x72\145\x20\164\x68\x61\156\x20\157\x6e\145\x20\74\163\x61\155\x6c\72\101\x75\164\x68\156\x43\157\x6e\164\145\170\x74\76\x20\x69\156\40\x3c\x73\x61\x6d\x6c\x3a\101\165\164\x68\x6e\123\164\x61\x74\145\155\x65\x6e\x74\x3e\x2e");
        goto ot;
        Lh:
        throw new Exception("\x4d\x69\x73\x73\151\156\x67\x20\x72\145\x71\x75\151\x72\145\144\40\74\163\x61\x6d\154\72\101\165\164\x68\x6e\103\x6f\x6e\x74\x65\170\x74\76\40\x69\156\40\x3c\x73\141\x6d\x6c\72\x41\x75\164\x68\156\123\164\141\164\145\x6d\145\x6e\164\x3e\56");
        ot:
        $Zf = $Bb[0];
        $Cd = Utilities::xpQuery($Zf, "\x2e\x2f\x73\141\x6d\x6c\x5f\141\163\x73\x65\162\x74\151\157\156\72\101\165\164\150\156\x43\x6f\156\x74\x65\x78\x74\104\x65\143\154\122\x65\146");
        if (count($Cd) > 1) {
            goto dC;
        }
        if (count($Cd) === 1) {
            goto W7;
        }
        goto xq;
        dC:
        throw new Exception("\115\x6f\162\145\40\x74\150\141\156\40\157\156\145\x20\74\x73\141\155\154\x3a\101\x75\x74\x68\156\x43\157\156\x74\145\170\164\x44\145\143\154\x52\x65\146\x3e\x20\x66\x6f\165\x6e\x64\x3f");
        goto xq;
        W7:
        $this->setAuthnContextDeclRef(trim($Cd[0]->textContent));
        xq:
        $Ix = Utilities::xpQuery($Zf, "\x2e\57\x73\141\155\154\x5f\x61\x73\163\145\x72\164\151\x6f\156\x3a\101\165\164\x68\156\103\x6f\156\164\x65\170\x74\x44\145\143\154");
        if (count($Ix) > 1) {
            goto D6;
        }
        if (count($Ix) === 1) {
            goto v5;
        }
        goto LA;
        D6:
        throw new Exception("\115\157\x72\x65\x20\164\150\141\x6e\x20\x6f\x6e\x65\x20\x3c\x73\141\x6d\154\x3a\x41\165\164\x68\156\103\157\x6e\x74\x65\170\164\x44\145\143\154\x3e\40\146\157\165\x6e\x64\x3f");
        goto LA;
        v5:
        $this->setAuthnContextDecl(new SAML2_XML_Chunk($Ix[0]));
        LA:
        $MN = Utilities::xpQuery($Zf, "\56\x2f\163\x61\x6d\x6c\x5f\x61\163\163\145\x72\x74\x69\157\x6e\72\101\165\164\150\x6e\x43\x6f\156\x74\145\170\164\x43\x6c\141\163\x73\122\x65\146");
        if (count($MN) > 1) {
            goto io;
        }
        if (count($MN) === 1) {
            goto fB;
        }
        goto Ip;
        io:
        throw new Exception("\x4d\x6f\x72\145\40\x74\150\141\156\x20\157\156\145\40\x3c\163\141\155\154\72\101\165\x74\150\156\x43\x6f\x6e\x74\145\x78\x74\x43\154\x61\163\x73\x52\x65\146\76\x20\x69\x6e\40\74\x73\141\155\x6c\x3a\x41\x75\x74\x68\156\103\x6f\x6e\164\x65\170\x74\76\56");
        goto Ip;
        fB:
        $this->setAuthnContextClassRef(trim($MN[0]->textContent));
        Ip:
        if (!(empty($this->authnContextClassRef) && empty($this->authnContextDecl) && empty($this->authnContextDeclRef))) {
            goto dk;
        }
        throw new Exception("\115\151\x73\x73\x69\x6e\147\x20\x65\151\164\x68\145\x72\x20\74\x73\x61\155\154\x3a\x41\x75\164\x68\156\x43\x6f\156\x74\145\170\164\x43\154\x61\x73\163\x52\x65\x66\x3e\40\x6f\162\x20\74\x73\x61\x6d\154\x3a\101\165\x74\x68\156\x43\x6f\x6e\x74\x65\x78\x74\104\x65\143\154\122\x65\x66\x3e\x20\157\x72\40\74\163\141\x6d\154\x3a\x41\x75\164\x68\156\x43\157\x6e\164\145\x78\164\104\145\143\154\76");
        dk:
        $this->AuthenticatingAuthority = Utilities::extractStrings($Zf, "\x75\x72\x6e\72\x6f\x61\x73\151\x73\x3a\x6e\141\x6d\145\x73\x3a\x74\x63\x3a\x53\x41\115\x4c\72\x32\56\60\x3a\141\163\163\x65\162\164\x69\157\x6e", "\101\x75\164\x68\145\x6e\164\x69\143\141\164\151\x6e\x67\x41\165\x74\x68\x6f\x72\x69\x74\x79");
    }
    private function parseAttributes(DOMElement $nb)
    {
        $YD = TRUE;
        $vR = Utilities::xpQuery($nb, "\56\x2f\x73\141\155\154\x5f\x61\163\163\145\x72\x74\151\157\x6e\x3a\101\x74\164\x72\x69\x62\165\164\145\x53\x74\x61\164\x65\155\145\x6e\x74\57\x73\141\155\154\x5f\x61\163\x73\x65\x72\x74\x69\x6f\156\x3a\x41\x74\164\x72\151\x62\x75\164\x65");
        foreach ($vR as $jK) {
            if ($jK->hasAttribute("\x4e\141\x6d\145")) {
                goto JC;
            }
            throw new Exception("\115\151\x73\x73\151\156\x67\40\156\141\x6d\145\x20\x6f\156\x20\74\x73\x61\x6d\154\72\101\164\x74\x72\151\142\165\x74\x65\x3e\40\x65\154\x65\x6d\145\x6e\164\56");
            JC:
            $dC = $jK->getAttribute("\116\x61\155\x65");
            if ($jK->hasAttribute("\116\x61\155\x65\106\x6f\x72\x6d\141\164")) {
                goto XN;
            }
            $FG = "\x75\162\x6e\72\x6f\x61\163\x69\x73\72\x6e\141\x6d\x65\163\72\164\x63\72\123\x41\115\x4c\72\61\56\61\x3a\156\x61\x6d\145\x69\x64\x2d\146\157\162\155\141\x74\72\165\156\163\160\145\x63\151\146\x69\145\144";
            goto MR;
            XN:
            $FG = $jK->getAttribute("\x4e\x61\x6d\145\x46\157\x72\155\141\x74");
            MR:
            if ($YD) {
                goto EW;
            }
            if (!($this->nameFormat !== $FG)) {
                goto bX;
            }
            $this->nameFormat = "\165\162\x6e\72\x6f\x61\x73\151\163\x3a\x6e\x61\x6d\x65\163\72\164\143\x3a\123\101\115\x4c\72\x31\x2e\61\x3a\156\141\x6d\x65\151\144\x2d\146\x6f\x72\155\141\x74\72\x75\x6e\163\160\x65\x63\151\x66\151\145\144";
            bX:
            goto Nz;
            EW:
            $this->nameFormat = $FG;
            $YD = FALSE;
            Nz:
            if (array_key_exists($dC, $this->attributes)) {
                goto bg;
            }
            $this->attributes[$dC] = array();
            bg:
            $pC = Utilities::xpQuery($jK, "\x2e\57\x73\141\x6d\x6c\137\141\x73\163\x65\162\x74\151\157\156\x3a\101\164\x74\162\x69\x62\x75\164\x65\x56\x61\x6c\x75\x65");
            foreach ($pC as $zF) {
                $this->attributes[$dC][] = trim($zF->textContent);
                Ly:
            }
            J9:
            ZH:
        }
        Pn:
    }
    private function parseEncryptedAttributes(DOMElement $nb)
    {
        $this->encryptedAttribute = Utilities::xpQuery($nb, "\56\x2f\163\x61\155\x6c\137\x61\x73\163\x65\x72\164\151\x6f\156\72\x41\164\x74\162\151\x62\165\x74\145\123\164\x61\x74\145\155\x65\156\164\57\x73\141\x6d\154\137\141\163\x73\x65\162\x74\x69\x6f\x6e\x3a\x45\x6e\x63\162\x79\160\x74\x65\144\x41\164\x74\x72\x69\x62\x75\x74\145");
    }
    private function parseSignature(DOMElement $nb)
    {
        $f3 = Utilities::validateElement($nb);
        if (!($f3 !== FALSE)) {
            goto Fu;
        }
        $this->wasSignedAtConstruction = TRUE;
        $this->certificates = $f3["\x43\145\162\164\x69\x66\x69\x63\x61\x74\x65\x73"];
        $this->signatureData = $f3;
        Fu:
    }
    public function validate(XMLSecurityKey $Z1)
    {
        if (!($this->signatureData === NULL)) {
            goto rx;
        }
        return FALSE;
        rx:
        Utilities::validateSignature($this->signatureData, $Z1);
        return TRUE;
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
    public function getIssuer()
    {
        return $this->issuer;
    }
    public function setIssuer($OS)
    {
        $this->issuer = $OS;
    }
    public function getNameId()
    {
        if (!($this->encryptedNameId !== NULL)) {
            goto zd;
        }
        throw new Exception("\101\164\164\145\x6d\x70\x74\145\144\x20\164\x6f\40\x72\145\164\x72\x69\145\166\x65\40\x65\x6e\143\162\x79\160\164\145\144\40\116\141\155\x65\x49\104\x20\167\151\164\x68\157\x75\x74\40\144\145\143\162\x79\160\164\151\156\x67\x20\x69\x74\40\x66\151\162\x73\164\56");
        zd:
        return $this->nameId;
    }
    public function setNameId($Xe)
    {
        $this->nameId = $Xe;
    }
    public function isNameIdEncrypted()
    {
        if (!($this->encryptedNameId !== NULL)) {
            goto RI;
        }
        return TRUE;
        RI:
        return FALSE;
    }
    public function encryptNameId(XMLSecurityKey $Z1)
    {
        $li = new DOMDocument();
        $oO = $li->createElement("\x72\157\x6f\164");
        $li->appendChild($oO);
        Utilities::addNameId($oO, $this->nameId);
        $Xe = $oO->firstChild;
        Utilities::getContainer()->debugMessage($Xe, "\x65\156\x63\x72\171\160\x74");
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
            goto c0;
        }
        return;
        c0:
        $Xe = Utilities::decryptElement($this->encryptedNameId, $Z1, $XT);
        Utilities::getContainer()->debugMessage($Xe, "\x64\x65\x63\x72\171\x70\164");
        $this->nameId = Utilities::parseNameId($Xe);
        $this->encryptedNameId = NULL;
    }
    public function decryptAttributes(XMLSecurityKey $Z1, array $XT = array())
    {
        if (!($this->encryptedAttribute === NULL)) {
            goto YJ;
        }
        return;
        YJ:
        $YD = TRUE;
        $vR = $this->encryptedAttribute;
        foreach ($vR as $jz) {
            $jK = Utilities::decryptElement($jz->getElementsByTagName("\105\156\x63\x72\x79\160\x74\145\x64\104\141\x74\x61")->item(0), $Z1, $XT);
            if ($jK->hasAttribute("\116\x61\155\x65")) {
                goto px;
            }
            throw new Exception("\115\x69\x73\x73\x69\x6e\147\40\x6e\141\x6d\145\x20\x6f\156\x20\74\x73\141\x6d\154\72\x41\164\x74\x72\151\x62\165\164\145\76\x20\145\154\145\x6d\x65\156\x74\x2e");
            px:
            $dC = $jK->getAttribute("\x4e\141\155\145");
            if ($jK->hasAttribute("\116\x61\x6d\x65\106\x6f\x72\155\141\x74")) {
                goto JG;
            }
            $FG = "\165\x72\156\72\157\141\x73\151\163\72\x6e\141\155\145\x73\x3a\164\x63\x3a\123\101\115\x4c\x3a\x32\x2e\60\72\x61\164\164\x72\156\141\155\145\x2d\x66\157\x72\155\x61\164\72\x75\x6e\163\x70\x65\143\151\x66\151\x65\144";
            goto lS;
            JG:
            $FG = $jK->getAttribute("\x4e\x61\155\145\106\157\162\155\x61\x74");
            lS:
            if ($YD) {
                goto kW;
            }
            if (!($this->nameFormat !== $FG)) {
                goto g0;
            }
            $this->nameFormat = "\x75\x72\x6e\72\x6f\141\x73\x69\x73\x3a\156\141\x6d\x65\x73\72\164\x63\72\x53\101\115\x4c\x3a\62\56\60\x3a\141\x74\x74\162\x6e\141\x6d\145\55\x66\x6f\x72\x6d\x61\x74\72\x75\x6e\163\160\x65\x63\x69\146\x69\x65\x64";
            g0:
            goto tg;
            kW:
            $this->nameFormat = $FG;
            $YD = FALSE;
            tg:
            if (array_key_exists($dC, $this->attributes)) {
                goto DP;
            }
            $this->attributes[$dC] = array();
            DP:
            $pC = Utilities::xpQuery($jK, "\x2e\57\x73\141\155\x6c\137\x61\x73\x73\145\x72\164\x69\157\156\72\x41\164\x74\162\x69\x62\165\164\145\x56\141\154\165\x65");
            foreach ($pC as $zF) {
                $this->attributes[$dC][] = trim($zF->textContent);
                fb:
            }
            Hw:
            Ob:
        }
        MD:
    }
    public function getNotBefore()
    {
        return $this->notBefore;
    }
    public function setNotBefore($mC)
    {
        $this->notBefore = $mC;
    }
    public function getNotOnOrAfter()
    {
        return $this->notOnOrAfter;
    }
    public function setNotOnOrAfter($ib)
    {
        $this->notOnOrAfter = $ib;
    }
    public function setEncryptedAttributes($qW)
    {
        $this->requiredEncAttributes = $qW;
    }
    public function getValidAudiences()
    {
        return $this->validAudiences;
    }
    public function setValidAudiences(array $az = NULL)
    {
        $this->validAudiences = $az;
    }
    public function getAuthnInstant()
    {
        return $this->authnInstant;
    }
    public function setAuthnInstant($hv)
    {
        $this->authnInstant = $hv;
    }
    public function getSessionNotOnOrAfter()
    {
        return $this->sessionNotOnOrAfter;
    }
    public function setSessionNotOnOrAfter($Iw)
    {
        $this->sessionNotOnOrAfter = $Iw;
    }
    public function getSessionIndex()
    {
        return $this->sessionIndex;
    }
    public function setSessionIndex($Kw)
    {
        $this->sessionIndex = $Kw;
    }
    public function getAuthnContext()
    {
        if (empty($this->authnContextClassRef)) {
            goto YN;
        }
        return $this->authnContextClassRef;
        YN:
        if (empty($this->authnContextDeclRef)) {
            goto P9;
        }
        return $this->authnContextDeclRef;
        P9:
        return NULL;
    }
    public function setAuthnContext($Mr)
    {
        $this->setAuthnContextClassRef($Mr);
    }
    public function getAuthnContextClassRef()
    {
        return $this->authnContextClassRef;
    }
    public function setAuthnContextClassRef($qi)
    {
        $this->authnContextClassRef = $qi;
    }
    public function setAuthnContextDecl(SAML2_XML_Chunk $tQ)
    {
        if (empty($this->authnContextDeclRef)) {
            goto x3;
        }
        throw new Exception("\101\x75\164\150\x6e\103\157\156\164\145\170\164\x44\145\143\x6c\x52\x65\x66\x20\151\x73\40\x61\x6c\162\x65\x61\144\x79\40\x72\145\x67\x69\163\x74\145\162\145\144\x21\x20\115\141\x79\x20\157\x6e\x6c\171\40\x68\141\x76\145\x20\145\151\x74\150\x65\162\x20\x61\x20\104\x65\x63\154\40\x6f\162\x20\x61\x20\104\x65\x63\154\x52\145\x66\x2c\x20\156\157\x74\x20\142\157\x74\x68\x21");
        x3:
        $this->authnContextDecl = $tQ;
    }
    public function getAuthnContextDecl()
    {
        return $this->authnContextDecl;
    }
    public function setAuthnContextDeclRef($HW)
    {
        if (empty($this->authnContextDecl)) {
            goto b2;
        }
        throw new Exception("\101\165\164\150\156\x43\157\x6e\x74\x65\x78\x74\x44\145\x63\x6c\40\151\x73\40\x61\154\162\145\141\144\171\40\x72\145\x67\x69\163\164\x65\x72\x65\x64\x21\40\115\141\171\40\x6f\156\x6c\x79\x20\x68\141\x76\145\x20\145\151\x74\x68\x65\x72\40\x61\40\104\x65\x63\154\40\x6f\x72\40\141\x20\104\145\143\154\122\x65\146\x2c\x20\156\157\164\x20\142\157\164\x68\x21");
        b2:
        $this->authnContextDeclRef = $HW;
    }
    public function getAuthnContextDeclRef()
    {
        return $this->authnContextDeclRef;
    }
    public function getAuthenticatingAuthority()
    {
        return $this->AuthenticatingAuthority;
    }
    public function setAuthenticatingAuthority($MD)
    {
        $this->AuthenticatingAuthority = $MD;
    }
    public function getAttributes()
    {
        return $this->attributes;
    }
    public function setAttributes(array $vR)
    {
        $this->attributes = $vR;
    }
    public function getAttributeNameFormat()
    {
        return $this->nameFormat;
    }
    public function setAttributeNameFormat($FG)
    {
        $this->nameFormat = $FG;
    }
    public function getSubjectConfirmation()
    {
        return $this->SubjectConfirmation;
    }
    public function setSubjectConfirmation(array $gk)
    {
        $this->SubjectConfirmation = $gk;
    }
    public function getSignatureKey()
    {
        return $this->signatureKey;
    }
    public function setSignatureKey(XMLsecurityKey $qv = NULL)
    {
        $this->signatureKey = $qv;
    }
    public function getEncryptionKey()
    {
        return $this->encryptionKey;
    }
    public function setEncryptionKey(XMLSecurityKey $Qh = NULL)
    {
        $this->encryptionKey = $Qh;
    }
    public function setCertificates(array $zK)
    {
        $this->certificates = $zK;
    }
    public function getCertificates()
    {
        return $this->certificates;
    }
    public function getSignatureData()
    {
        return $this->signatureData;
    }
    public function getWasSignedAtConstruction()
    {
        return $this->wasSignedAtConstruction;
    }
    public function toXML(DOMNode $pv = NULL)
    {
        if ($pv === NULL) {
            goto Ja;
        }
        $uz = $pv->ownerDocument;
        goto Cu;
        Ja:
        $uz = new DOMDocument();
        $pv = $uz;
        Cu:
        $oO = $uz->createElementNS("\165\x72\x6e\72\x6f\x61\163\151\x73\72\156\141\x6d\145\x73\72\x74\x63\72\123\101\x4d\114\x3a\x32\56\x30\72\x61\x73\x73\145\x72\164\x69\157\156", "\x73\141\155\x6c\72" . "\x41\x73\163\145\x72\164\151\157\156");
        $pv->appendChild($oO);
        $oO->setAttributeNS("\x75\162\x6e\x3a\157\x61\163\151\x73\x3a\156\141\155\145\x73\x3a\x74\x63\x3a\x53\x41\x4d\114\x3a\x32\x2e\60\x3a\160\x72\157\x74\x6f\x63\157\x6c", "\x73\x61\x6d\x6c\x70\x3a\164\x6d\160", "\164\x6d\x70");
        $oO->removeAttributeNS("\x75\x72\156\x3a\157\141\x73\x69\163\72\156\141\155\145\163\x3a\164\x63\72\123\101\115\114\x3a\x32\x2e\x30\72\x70\162\x6f\x74\x6f\143\157\154", "\164\x6d\160");
        $oO->setAttributeNS("\x68\164\164\x70\x3a\57\57\167\167\x77\x2e\x77\x33\56\x6f\162\x67\57\62\60\x30\61\57\x58\x4d\114\123\x63\x68\x65\x6d\141\55\151\156\x73\164\x61\x6e\143\x65", "\x78\x73\x69\72\164\x6d\x70", "\x74\x6d\x70");
        $oO->removeAttributeNS("\150\x74\x74\160\72\x2f\x2f\167\x77\x77\56\x77\63\x2e\x6f\x72\147\57\x32\x30\60\61\x2f\130\115\x4c\x53\143\150\145\x6d\141\x2d\151\x6e\x73\x74\141\156\143\145", "\164\155\160");
        $oO->setAttributeNS("\150\164\x74\x70\x3a\x2f\x2f\167\167\x77\56\167\x33\x2e\x6f\x72\147\x2f\x32\x30\60\x31\57\x58\x4d\x4c\x53\143\150\145\155\x61", "\x78\x73\x3a\164\x6d\160", "\x74\x6d\x70");
        $oO->removeAttributeNS("\x68\164\x74\x70\72\x2f\57\x77\167\x77\x2e\x77\63\56\157\x72\x67\x2f\x32\x30\x30\x31\57\130\115\114\123\143\150\x65\x6d\x61", "\164\155\x70");
        $oO->setAttribute("\x49\x44", $this->id);
        $oO->setAttribute("\x56\x65\x72\163\x69\x6f\x6e", "\x32\56\x30");
        $oO->setAttribute("\x49\163\163\x75\145\111\156\163\164\141\x6e\x74", gmdate("\x59\55\155\x2d\144\x5c\x54\x48\72\x69\x3a\x73\134\x5a", $this->issueInstant));
        $OS = Utilities::addString($oO, "\165\162\156\x3a\157\x61\163\x69\163\x3a\156\141\x6d\x65\x73\x3a\x74\x63\72\123\101\115\x4c\72\62\56\x30\x3a\141\163\x73\x65\x72\164\151\157\156", "\163\141\x6d\154\x3a\x49\163\x73\x75\145\162", $this->issuer);
        $this->addSubject($oO);
        $this->addConditions($oO);
        $this->addAuthnStatement($oO);
        if ($this->requiredEncAttributes == FALSE) {
            goto AG;
        }
        $this->addEncryptedAttributeStatement($oO);
        goto R8;
        AG:
        $this->addAttributeStatement($oO);
        R8:
        if (!($this->signatureKey !== NULL)) {
            goto vB;
        }
        Utilities::insertSignature($this->signatureKey, $this->certificates, $oO, $OS->nextSibling);
        vB:
        return $oO;
    }
    private function addSubject(DOMElement $oO)
    {
        if (!($this->nameId === NULL && $this->encryptedNameId === NULL)) {
            goto CG;
        }
        return;
        CG:
        $kP = $oO->ownerDocument->createElementNS("\x75\x72\156\x3a\x6f\141\163\151\x73\72\x6e\141\x6d\x65\163\72\164\143\x3a\x53\x41\115\114\x3a\62\x2e\60\72\x61\163\x73\x65\162\164\151\x6f\x6e", "\163\141\x6d\154\72\123\x75\142\x6a\145\143\x74");
        $oO->appendChild($kP);
        if ($this->encryptedNameId === NULL) {
            goto HO;
        }
        $zS = $kP->ownerDocument->createElementNS("\165\162\156\72\x6f\x61\x73\x69\x73\72\x6e\x61\x6d\145\163\x3a\x74\143\72\x53\x41\115\114\72\62\56\x30\x3a\x61\163\163\x65\x72\x74\151\x6f\156", "\x73\141\x6d\154\72" . "\105\156\143\162\x79\x70\x74\x65\144\111\x44");
        $kP->appendChild($zS);
        $zS->appendChild($kP->ownerDocument->importNode($this->encryptedNameId, TRUE));
        goto nz;
        HO:
        Utilities::addNameId($kP, $this->nameId);
        nz:
        foreach ($this->SubjectConfirmation as $oV) {
            $oV->toXML($kP);
            Yi:
        }
        Kj:
    }
    private function addConditions(DOMElement $oO)
    {
        $uz = $oO->ownerDocument;
        $fs = $uz->createElementNS("\165\162\156\x3a\157\141\x73\x69\x73\x3a\156\141\x6d\x65\163\x3a\164\143\x3a\123\101\x4d\x4c\x3a\x32\x2e\60\x3a\141\x73\163\x65\162\164\x69\157\156", "\163\x61\x6d\x6c\x3a\103\x6f\x6e\x64\x69\164\x69\x6f\x6e\x73");
        $oO->appendChild($fs);
        if (!($this->notBefore !== NULL)) {
            goto jZ;
        }
        $fs->setAttribute("\116\157\x74\102\145\x66\x6f\162\145", gmdate("\x59\55\155\x2d\x64\134\124\110\72\x69\x3a\163\134\x5a", $this->notBefore));
        jZ:
        if (!($this->notOnOrAfter !== NULL)) {
            goto YY;
        }
        $fs->setAttribute("\x4e\157\164\x4f\x6e\117\162\x41\x66\x74\x65\x72", gmdate("\x59\55\155\55\144\x5c\x54\110\72\151\x3a\x73\x5c\132", $this->notOnOrAfter));
        YY:
        if (!($this->validAudiences !== NULL)) {
            goto CL;
        }
        $dd = $uz->createElementNS("\x75\x72\x6e\72\x6f\141\163\151\163\72\156\x61\x6d\x65\x73\72\164\143\72\123\x41\115\114\72\62\x2e\x30\x3a\141\x73\x73\x65\x72\x74\x69\157\156", "\163\x61\155\x6c\x3a\x41\165\144\x69\x65\x6e\143\x65\122\145\x73\x74\x72\151\143\164\151\x6f\156");
        $fs->appendChild($dd);
        Utilities::addStrings($dd, "\165\x72\x6e\72\157\x61\163\x69\x73\x3a\x6e\x61\155\x65\163\x3a\x74\x63\x3a\123\101\115\114\x3a\62\x2e\60\x3a\x61\163\163\145\x72\x74\151\x6f\156", "\x73\141\x6d\x6c\x3a\101\165\144\151\x65\x6e\143\x65", FALSE, $this->validAudiences);
        CL:
    }
    private function addAuthnStatement(DOMElement $oO)
    {
        if (!($this->authnInstant === NULL || $this->authnContextClassRef === NULL && $this->authnContextDecl === NULL && $this->authnContextDeclRef === NULL)) {
            goto Pm;
        }
        return;
        Pm:
        $uz = $oO->ownerDocument;
        $jh = $uz->createElementNS("\165\x72\x6e\72\157\x61\163\x69\163\x3a\x6e\x61\x6d\x65\163\72\164\143\x3a\x53\x41\x4d\x4c\72\62\x2e\x30\x3a\141\163\x73\x65\162\164\x69\x6f\156", "\x73\141\155\x6c\72\101\x75\164\150\x6e\123\x74\141\x74\x65\155\145\x6e\x74");
        $oO->appendChild($jh);
        $jh->setAttribute("\x41\x75\164\150\156\x49\x6e\x73\164\141\156\164", gmdate("\x59\55\x6d\55\x64\134\124\x48\72\151\72\x73\134\132", $this->authnInstant));
        if (!($this->sessionNotOnOrAfter !== NULL)) {
            goto bv;
        }
        $jh->setAttribute("\123\145\163\163\151\x6f\x6e\116\157\x74\x4f\156\117\x72\x41\x66\164\145\162", gmdate("\131\55\155\x2d\x64\x5c\x54\x48\x3a\151\x3a\163\134\x5a", $this->sessionNotOnOrAfter));
        bv:
        if (!($this->sessionIndex !== NULL)) {
            goto pE;
        }
        $jh->setAttribute("\123\x65\x73\163\x69\x6f\156\x49\x6e\x64\145\x78", $this->sessionIndex);
        pE:
        $Zf = $uz->createElementNS("\x75\x72\x6e\72\x6f\141\163\x69\x73\x3a\156\141\155\145\x73\72\x74\x63\72\123\x41\x4d\114\72\x32\x2e\60\x3a\x61\x73\163\x65\162\164\151\x6f\x6e", "\163\141\155\x6c\x3a\x41\165\x74\150\x6e\103\x6f\x6e\164\x65\170\x74");
        $jh->appendChild($Zf);
        if (empty($this->authnContextClassRef)) {
            goto us;
        }
        Utilities::addString($Zf, "\x75\x72\x6e\x3a\157\x61\x73\151\163\72\x6e\x61\155\x65\163\72\x74\143\x3a\123\101\115\x4c\72\x32\x2e\x30\72\x61\x73\163\145\x72\164\151\157\156", "\x73\141\x6d\x6c\72\x41\165\x74\150\156\103\157\156\x74\x65\x78\164\x43\154\141\x73\163\x52\x65\x66", $this->authnContextClassRef);
        us:
        if (empty($this->authnContextDecl)) {
            goto X6;
        }
        $this->authnContextDecl->toXML($Zf);
        X6:
        if (empty($this->authnContextDeclRef)) {
            goto hu;
        }
        Utilities::addString($Zf, "\165\x72\156\72\x6f\141\163\151\x73\x3a\156\x61\155\145\x73\72\x74\x63\72\x53\x41\x4d\x4c\x3a\x32\x2e\60\72\141\x73\x73\x65\x72\164\151\157\156", "\163\141\155\154\72\x41\x75\164\150\156\103\x6f\x6e\164\x65\x78\164\104\145\143\154\122\x65\146", $this->authnContextDeclRef);
        hu:
        Utilities::addStrings($Zf, "\x75\x72\x6e\72\157\141\x73\x69\163\x3a\x6e\141\155\145\163\72\164\143\x3a\x53\x41\115\114\x3a\x32\56\x30\x3a\x61\x73\x73\145\x72\164\151\157\x6e", "\163\141\x6d\154\x3a\x41\165\164\x68\x65\156\164\151\143\141\164\x69\156\x67\101\165\164\x68\x6f\162\151\x74\x79", FALSE, $this->AuthenticatingAuthority);
    }
    private function addAttributeStatement(DOMElement $oO)
    {
        if (!empty($this->attributes)) {
            goto Pe;
        }
        return;
        Pe:
        $uz = $oO->ownerDocument;
        $dc = $uz->createElementNS("\165\162\156\x3a\157\141\163\x69\163\72\x6e\x61\x6d\x65\x73\x3a\x74\143\72\x53\x41\x4d\114\x3a\x32\56\x30\72\141\x73\x73\145\162\164\x69\157\x6e", "\163\141\x6d\154\72\x41\x74\164\162\x69\142\165\164\x65\x53\164\141\x74\145\155\145\x6e\x74");
        $oO->appendChild($dc);
        foreach ($this->attributes as $dC => $pC) {
            $jK = $uz->createElementNS("\x75\162\x6e\72\157\x61\x73\x69\x73\72\156\x61\155\145\163\x3a\164\143\72\123\x41\x4d\114\72\x32\56\60\72\141\x73\163\x65\162\x74\151\157\x6e", "\x73\x61\155\154\x3a\101\x74\164\162\x69\x62\165\x74\145");
            $dc->appendChild($jK);
            $jK->setAttribute("\x4e\x61\x6d\x65", $dC);
            if (!($this->nameFormat !== "\165\162\156\x3a\157\141\163\x69\x73\72\x6e\141\155\x65\163\x3a\164\143\72\123\x41\x4d\114\x3a\x32\x2e\x30\x3a\x61\164\164\x72\156\x61\155\145\55\146\157\162\155\x61\164\x3a\x75\156\163\160\x65\x63\151\x66\x69\145\x64")) {
                goto vq;
            }
            $jK->setAttribute("\116\x61\x6d\x65\x46\157\x72\155\141\x74", $this->nameFormat);
            vq:
            foreach ($pC as $zF) {
                if (is_string($zF)) {
                    goto eZ;
                }
                if (is_int($zF)) {
                    goto zi;
                }
                $Uj = NULL;
                goto kj;
                eZ:
                $Uj = "\x78\163\72\163\x74\162\151\156\x67";
                goto kj;
                zi:
                $Uj = "\170\x73\x3a\151\x6e\164\x65\147\x65\x72";
                kj:
                $Qe = $uz->createElementNS("\165\162\156\x3a\x6f\141\x73\151\163\72\156\141\155\x65\x73\72\x74\143\72\x53\x41\x4d\x4c\72\x32\56\60\72\141\x73\x73\x65\x72\x74\x69\157\x6e", "\163\141\155\154\72\101\164\164\x72\151\142\x75\x74\x65\x56\141\154\x75\145");
                $jK->appendChild($Qe);
                if (!($Uj !== NULL)) {
                    goto dI;
                }
                $Qe->setAttributeNS("\x68\164\x74\x70\x3a\x2f\57\167\x77\x77\56\x77\x33\56\157\162\x67\57\62\x30\60\x31\57\x58\x4d\114\123\x63\x68\x65\155\x61\55\x69\156\163\164\141\156\143\x65", "\x78\x73\151\x3a\x74\171\x70\145", $Uj);
                dI:
                if (!is_null($zF)) {
                    goto cA;
                }
                $Qe->setAttributeNS("\x68\164\x74\x70\x3a\57\57\x77\167\167\x2e\x77\63\x2e\x6f\162\147\57\x32\x30\x30\x31\x2f\x58\115\x4c\123\x63\150\x65\155\141\x2d\x69\156\x73\x74\x61\x6e\x63\x65", "\170\163\151\72\156\151\x6c", "\x74\162\165\145");
                cA:
                if ($zF instanceof DOMNodeList) {
                    goto qn;
                }
                $Qe->appendChild($uz->createTextNode($zF));
                goto ud;
                qn:
                $cu = 0;
                Uf:
                if (!($cu < $zF->length)) {
                    goto A4;
                }
                $nC = $uz->importNode($zF->item($cu), TRUE);
                $Qe->appendChild($nC);
                tS:
                $cu++;
                goto Uf;
                A4:
                ud:
                Ee:
            }
            J7:
            z0:
        }
        iv:
    }
    private function addEncryptedAttributeStatement(DOMElement $oO)
    {
        if (!($this->requiredEncAttributes == FALSE)) {
            goto fp;
        }
        return;
        fp:
        $uz = $oO->ownerDocument;
        $dc = $uz->createElementNS("\x75\162\156\x3a\157\x61\x73\x69\x73\72\156\141\x6d\145\x73\72\x74\x63\72\123\x41\x4d\114\72\62\x2e\60\72\141\163\x73\x65\162\164\151\x6f\x6e", "\x73\x61\x6d\154\x3a\101\x74\x74\162\151\x62\165\164\x65\123\x74\x61\x74\x65\155\145\x6e\x74");
        $oO->appendChild($dc);
        foreach ($this->attributes as $dC => $pC) {
            $a9 = new DOMDocument();
            $jK = $a9->createElementNS("\165\162\156\x3a\157\141\x73\x69\163\x3a\156\x61\155\145\163\x3a\164\143\x3a\123\101\x4d\x4c\x3a\62\x2e\x30\72\x61\x73\163\145\162\164\x69\157\156", "\163\x61\155\x6c\72\x41\x74\x74\x72\x69\x62\165\164\x65");
            $jK->setAttribute("\x4e\x61\x6d\145", $dC);
            $a9->appendChild($jK);
            if (!($this->nameFormat !== "\165\x72\156\72\x6f\x61\x73\x69\163\72\x6e\141\155\145\x73\x3a\164\143\x3a\x53\x41\115\114\x3a\x32\x2e\60\x3a\x61\x74\x74\162\x6e\x61\155\x65\x2d\x66\157\162\155\141\x74\x3a\165\x6e\x73\x70\145\143\151\x66\x69\145\x64")) {
                goto ds;
            }
            $jK->setAttribute("\x4e\141\155\x65\106\157\162\x6d\141\164", $this->nameFormat);
            ds:
            foreach ($pC as $zF) {
                if (is_string($zF)) {
                    goto Jb;
                }
                if (is_int($zF)) {
                    goto RJ;
                }
                $Uj = NULL;
                goto aK;
                Jb:
                $Uj = "\170\x73\72\x73\x74\162\151\156\x67";
                goto aK;
                RJ:
                $Uj = "\x78\x73\72\x69\x6e\164\x65\x67\x65\162";
                aK:
                $Qe = $a9->createElementNS("\x75\162\x6e\x3a\157\x61\163\x69\163\72\156\x61\x6d\x65\x73\x3a\164\x63\x3a\x53\101\115\x4c\72\62\x2e\x30\x3a\141\163\x73\145\x72\x74\151\x6f\156", "\x73\x61\155\154\72\x41\x74\164\x72\151\142\x75\x74\x65\126\x61\154\165\x65");
                $jK->appendChild($Qe);
                if (!($Uj !== NULL)) {
                    goto XW;
                }
                $Qe->setAttributeNS("\150\x74\164\160\72\57\x2f\x77\x77\167\x2e\x77\63\56\x6f\x72\x67\57\x32\x30\60\x31\x2f\130\115\114\x53\x63\150\x65\155\x61\x2d\x69\x6e\x73\164\141\156\143\145", "\x78\163\151\72\x74\x79\x70\x65", $Uj);
                XW:
                if ($zF instanceof DOMNodeList) {
                    goto VU;
                }
                $Qe->appendChild($a9->createTextNode($zF));
                goto Ci;
                VU:
                $cu = 0;
                kb:
                if (!($cu < $zF->length)) {
                    goto MA;
                }
                $nC = $a9->importNode($zF->item($cu), TRUE);
                $Qe->appendChild($nC);
                fj:
                $cu++;
                goto kb;
                MA:
                Ci:
                R7:
            }
            aZ:
            $Ni = new XMLSecEnc();
            $Ni->setNode($a9->documentElement);
            $Ni->type = "\x68\x74\164\160\x3a\57\57\x77\x77\167\x2e\x77\x33\x2e\x6f\x72\x67\57\x32\60\60\61\x2f\x30\x34\57\x78\155\x6c\x65\156\x63\43\x45\x6c\x65\x6d\145\156\164";
            $oL = new XMLSecurityKey(XMLSecurityKey::AES256_CBC);
            $oL->generateSessionKey();
            $Ni->encryptKey($this->encryptionKey, $oL);
            $Ss = $Ni->encryptNode($oL);
            $WE = $uz->createElementNS("\165\162\x6e\x3a\157\x61\x73\x69\x73\x3a\x6e\x61\x6d\x65\163\x3a\x74\143\x3a\x53\x41\x4d\114\72\62\56\60\x3a\x61\163\x73\145\162\164\151\x6f\156", "\x73\141\155\154\x3a\x45\x6e\x63\162\x79\160\164\x65\x64\101\164\164\x72\x69\142\165\164\x65");
            $dc->appendChild($WE);
            $h_ = $uz->importNode($Ss, TRUE);
            $WE->appendChild($h_);
            Yg:
        }
        WQ:
    }
}
