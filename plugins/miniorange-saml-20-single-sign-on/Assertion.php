<?php


include_once "\x55\164\x69\154\x69\x74\x69\145\163\56\x70\150\160";
include_once "\x78\155\154\163\145\x63\x6c\x69\142\163\56\x70\x68\160";
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
    private $privateKeyUrl;
    protected $wasSignedAtConstruction = FALSE;
    public function __construct(DOMElement $mf = NULL, $n_)
    {
        $this->id = Utilities::generateId();
        $this->issueInstant = Utilities::generateTimestamp();
        $this->issuer = '';
        $this->authnInstant = Utilities::generateTimestamp();
        $this->attributes = array();
        $this->nameFormat = "\x75\x72\156\x3a\157\141\163\x69\x73\x3a\156\141\155\x65\x73\x3a\x74\143\72\x53\x41\115\x4c\72\61\56\x31\72\156\141\155\x65\x69\144\x2d\146\157\x72\155\x61\x74\x3a\x75\x6e\163\160\x65\143\151\146\151\145\144";
        $this->certificates = array();
        $this->AuthenticatingAuthority = array();
        $this->SubjectConfirmation = array();
        if (!($mf === NULL)) {
            goto ig;
        }
        return;
        ig:
        if (!($mf->localName === "\x45\x6e\143\x72\x79\x70\164\145\x64\x41\x73\163\x65\162\x74\x69\157\156")) {
            goto oc;
        }
        $Tb = Utilities::xpQuery($mf, "\56\57\170\145\x6e\143\72\105\156\143\162\171\160\164\145\x64\x44\x61\x74\x61");
        $zq = Utilities::xpQuery($mf, "\x2e\x2f\170\145\156\x63\x3a\105\x6e\143\x72\171\160\164\x65\144\104\141\x74\141\x2f\x64\163\72\113\x65\x79\x49\x6e\146\157\57\170\x65\x6e\x63\72\x45\156\143\x72\171\160\x74\145\144\x4b\145\x79");
        $zd = '';
        if (empty($zq)) {
            goto cr;
        }
        $zd = $zq[0]->firstChild->getAttribute("\x41\x6c\147\x6f\162\151\x74\150\x6d");
        goto qI;
        cr:
        $zq = Utilities::xpQuery($mf, "\x2e\57\x78\145\156\143\72\105\156\143\x72\171\x70\164\x65\144\x4b\145\171\57\170\x65\x6e\x63\72\x45\156\143\x72\x79\x70\164\x69\157\156\x4d\x65\x74\x68\157\x64");
        $zd = $zq[0]->getAttribute("\101\x6c\147\x6f\x72\x69\x74\x68\155");
        qI:
        $nb = Utilities::getEncryptionAlgorithm($zd);
        if (count($Tb) === 0) {
            goto PL;
        }
        if (count($Tb) > 1) {
            goto bH;
        }
        goto JJ;
        PL:
        throw new Exception("\115\x69\x73\x73\151\x6e\x67\x20\x65\156\x63\162\x79\160\164\x65\x64\x20\x64\141\x74\141\x20\x69\x6e\40\74\x73\141\x6d\x6c\72\x45\x6e\x63\x72\171\x70\x74\145\x64\101\163\163\x65\x72\164\x69\x6f\156\76\x2e");
        goto JJ;
        bH:
        throw new Exception("\x4d\157\x72\145\40\164\150\141\x6e\40\x6f\156\145\x20\145\156\143\162\x79\160\x74\145\x64\x20\x64\141\164\x61\x20\145\154\145\155\145\x6e\x74\40\151\156\40\x3c\x73\x61\x6d\x6c\x3a\x45\156\x63\x72\171\160\x74\145\144\x41\163\163\145\162\164\x69\157\x6e\x3e\56");
        JJ:
        $XC = new XMLSecurityKey($nb, array("\164\171\160\x65" => "\160\x72\151\166\141\x74\x65"));
        $Tz = get_site_option("\x6d\157\x5f\x73\x61\155\x6c\137\143\165\x72\162\145\156\x74\x5f\143\x65\162\x74\x5f\x70\x72\x69\x76\141\164\x65\x5f\153\145\171");
        $XC->loadKey($n_, FALSE);
        $F_ = array();
        $mf = Utilities::decryptElement($Tb[0], $XC, $F_);
        oc:
        if ($mf->hasAttribute("\x49\104")) {
            goto W_;
        }
        throw new Exception("\x4d\151\163\x73\x69\x6e\x67\x20\x49\x44\x20\141\164\164\x72\x69\142\x75\164\145\40\157\156\x20\x53\101\x4d\x4c\x20\x61\x73\163\x65\x72\x74\x69\157\x6e\x2e");
        W_:
        $this->id = $mf->getAttribute("\x49\x44");
        if (!($mf->getAttribute("\126\145\162\x73\151\157\156") !== "\62\56\60")) {
            goto Cf;
        }
        throw new Exception("\x55\x6e\x73\165\160\160\157\162\164\145\x64\x20\x76\145\162\x73\x69\157\156\x3a\x20" . $mf->getAttribute("\126\x65\x72\x73\x69\157\x6e"));
        Cf:
        $this->issueInstant = Utilities::xsDateTimeToTimestamp($mf->getAttribute("\111\163\163\x75\145\x49\156\x73\164\x61\156\x74"));
        $Jm = Utilities::xpQuery($mf, "\x2e\57\163\x61\x6d\154\137\141\x73\163\x65\x72\164\151\157\156\72\111\163\x73\165\145\162");
        if (!empty($Jm)) {
            goto uj;
        }
        throw new Exception("\115\151\163\x73\151\156\x67\40\74\163\x61\155\x6c\x3a\111\163\x73\165\145\162\x3e\40\151\x6e\x20\x61\163\x73\145\x72\x74\x69\x6f\x6e\x2e");
        uj:
        $this->issuer = trim($Jm[0]->textContent);
        $this->parseConditions($mf);
        $this->parseAuthnStatement($mf);
        $this->parseAttributes($mf);
        $this->parseEncryptedAttributes($mf);
        $this->parseSignature($mf);
        $this->parseSubject($mf);
    }
    private function parseSubject(DOMElement $mf)
    {
        $Cd = Utilities::xpQuery($mf, "\56\57\163\x61\x6d\154\137\141\x73\163\145\x72\164\x69\x6f\x6e\x3a\x53\165\x62\152\145\143\x74");
        if (empty($Cd)) {
            goto Br;
        }
        if (count($Cd) > 1) {
            goto Nx;
        }
        goto XS;
        Br:
        return;
        goto XS;
        Nx:
        throw new Exception("\115\157\162\x65\40\164\150\x61\156\x20\157\156\145\40\x3c\x73\141\155\154\x3a\x53\165\142\x6a\x65\x63\x74\x3e\x20\151\x6e\x20\74\x73\x61\x6d\x6c\x3a\101\163\x73\x65\x72\x74\x69\157\x6e\x3e\x2e");
        XS:
        $Cd = $Cd[0];
        $Ix = Utilities::xpQuery($Cd, "\56\57\163\x61\155\154\137\x61\163\x73\145\162\x74\x69\x6f\156\x3a\116\141\x6d\145\x49\104\40\174\40\56\x2f\163\141\x6d\x6c\x5f\x61\x73\163\145\162\x74\x69\157\x6e\x3a\105\156\x63\x72\171\x70\x74\x65\x64\111\x44\x2f\170\x65\156\143\x3a\x45\x6e\x63\x72\171\160\164\x65\x64\x44\x61\x74\x61");
        if (empty($Ix)) {
            goto Mx;
        }
        if (count($Ix) > 1) {
            goto fT;
        }
        goto jk;
        Mx:
        if ($_POST["\x52\x65\154\141\171\123\164\x61\164\x65"] == "\x74\145\x73\x74\x56\141\x6c\151\x64\x61\x74\145" or $_POST["\x52\145\x6c\141\x79\x53\164\141\164\145"] == "\164\145\163\164\116\x65\x77\103\x65\x72\x74\151\x66\151\143\141\164\145") {
            goto AG;
        }
        wp_die("\x57\x65\x20\x63\157\x75\154\x64\x20\x6e\157\x74\x20\163\x69\147\156\40\x79\157\165\x20\x69\156\x2e\40\120\x6c\x65\141\163\x65\40\x63\x6f\156\164\141\x63\x74\x20\x79\157\165\162\x20\141\x64\x6d\x69\156\x69\163\x74\162\x61\164\157\x72");
        goto IS;
        AG:
        echo "\x3c\x64\x69\x76\x20\163\164\x79\154\145\75\x22\146\157\x6e\x74\x2d\146\x61\x6d\151\x6c\x79\x3a\x43\x61\154\151\x62\x72\x69\73\160\x61\144\144\x69\x6e\x67\72\x30\x20\63\x25\x3b\42\76";
        echo "\x3c\x64\x69\x76\x20\163\x74\x79\x6c\x65\x3d\42\x63\157\x6c\157\162\x3a\x20\43\x61\x39\x34\x34\64\x32\x3b\142\141\x63\153\x67\x72\157\165\156\144\x2d\143\157\154\157\162\x3a\40\43\146\x32\144\145\x64\x65\x3b\160\141\144\x64\151\156\x67\72\x20\61\x35\x70\170\x3b\x6d\141\x72\x67\151\156\55\142\x6f\164\164\157\x6d\72\x20\x32\60\160\170\73\164\x65\x78\x74\x2d\141\x6c\x69\x67\156\x3a\143\x65\156\x74\x65\x72\x3b\x62\x6f\x72\x64\145\162\72\x31\x70\x78\x20\163\x6f\154\x69\x64\x20\43\x45\66\x42\x33\x42\62\x3b\146\x6f\156\x74\x2d\163\x69\x7a\145\x3a\61\x38\160\x74\73\42\x3e\40\105\122\122\x4f\122\74\x2f\x64\151\x76\x3e\xd\xa\40\x20\40\40\x20\40\40\40\x20\40\x20\74\144\151\166\x20\x73\164\x79\154\x65\75\42\143\157\154\157\162\x3a\40\x23\141\71\64\64\x34\62\x3b\146\157\x6e\164\x2d\x73\151\x7a\x65\x3a\x31\x34\x70\164\73\x20\155\x61\x72\x67\x69\x6e\x2d\142\157\164\164\157\155\72\x32\x30\160\x78\73\x22\x3e\74\x70\x3e\74\x73\x74\162\157\x6e\x67\x3e\x45\162\162\x6f\162\x3a\x20\x3c\x2f\163\x74\x72\157\156\147\76\x4d\x69\163\163\x69\156\147\40\40\116\141\155\145\x49\104\40\157\x72\x20\x45\x6e\x63\x72\171\160\164\x65\x64\x49\104\40\x69\x6e\40\123\101\115\x4c\x20\x52\145\163\160\x6f\x6e\163\x65\x3c\57\160\76\xd\xa\x20\40\x20\40\x20\x20\40\x20\x20\40\x20\x20\x20\x20\x20\x20\x3c\160\76\120\154\x65\x61\163\x65\40\x63\x6f\156\164\x61\x63\x74\x20\x79\157\165\x72\x20\x61\x64\155\151\156\x69\x73\164\x72\x61\164\x6f\162\x20\141\x6e\144\40\x72\145\160\157\162\x74\x20\x74\x68\x65\40\x66\157\x6c\154\157\167\x69\x6e\x67\40\145\x72\162\x6f\x72\x3a\74\x2f\160\x3e\xd\xa\40\x20\x20\x20\x20\x20\40\40\x20\40\40\x20\40\40\x20\40\x3c\x70\76\x3c\x73\164\162\x6f\156\147\76\120\157\163\x73\151\142\154\145\x20\103\x61\x75\163\x65\72\x3c\57\163\x74\162\x6f\156\147\76\x20\116\x61\x6d\x65\111\104\40\x6e\x6f\x74\40\x66\157\165\156\x64\40\151\156\x20\x53\101\115\114\x20\x52\x65\163\160\157\x6e\163\145\x20\x73\165\x62\x6a\145\143\164\x3c\57\160\x3e\xd\12\x20\x20\40\40\x20\x20\x20\x20\x20\40\x20\40\x20\40\x20\40\x3c\57\144\151\166\x3e\xd\12\x20\40\x20\x20\x20\40\40\40\40\x20\40\40\x20\x20\40\40\74\144\x69\166\x20\x73\x74\x79\x6c\145\x3d\42\x6d\141\162\x67\151\156\72\63\x25\x3b\x64\x69\x73\160\154\141\171\x3a\x62\x6c\157\143\153\73\x74\x65\x78\164\x2d\x61\x6c\x69\147\x6e\x3a\143\145\156\x74\145\162\73\42\76\xd\12\x20\x20\x20\x20\40\40\40\x20\x20\x20\40\40\40\x20\40\x20\74\x64\151\x76\40\x73\164\171\154\x65\75\x22\x6d\x61\x72\147\x69\156\72\x33\x25\73\x64\x69\x73\160\154\x61\171\72\142\x6c\157\143\153\73\x74\x65\x78\164\x2d\141\x6c\x69\147\156\72\x63\145\x6e\164\x65\162\73\x22\x3e\74\x69\x6e\x70\165\x74\x20\163\x74\171\x6c\x65\x3d\x22\160\x61\x64\144\x69\x6e\x67\x3a\x31\x25\x3b\x77\151\x64\164\x68\x3a\61\x30\x30\x70\x78\73\142\x61\x63\x6b\147\x72\x6f\165\x6e\x64\72\x20\43\x30\60\x39\x31\103\104\x20\156\x6f\156\145\40\x72\145\x70\145\141\x74\40\163\143\162\x6f\154\x6c\40\x30\x25\40\60\45\x3b\143\165\x72\163\157\162\72\40\x70\157\x69\156\164\x65\x72\x3b\146\157\156\x74\x2d\163\151\172\x65\x3a\61\65\x70\x78\x3b\142\x6f\162\144\145\162\55\167\151\x64\164\x68\x3a\40\61\160\x78\x3b\142\157\162\x64\145\x72\55\163\164\171\154\x65\72\x20\163\157\154\x69\x64\x3b\142\x6f\162\x64\145\162\x2d\x72\x61\144\151\x75\163\x3a\40\x33\160\170\x3b\167\x68\151\164\x65\55\163\x70\141\x63\145\72\x20\156\157\x77\x72\141\x70\73\142\157\170\x2d\x73\151\172\151\x6e\147\72\40\142\157\162\144\145\x72\x2d\142\x6f\x78\73\142\157\x72\x64\145\162\x2d\143\157\x6c\x6f\162\72\x20\x23\60\x30\x37\x33\101\x41\73\x62\x6f\170\55\163\150\141\x64\x6f\x77\x3a\x20\x30\160\x78\x20\x31\160\170\x20\60\160\170\x20\x72\x67\x62\141\50\61\62\60\x2c\x20\x32\x30\x30\54\x20\62\63\x30\x2c\x20\x30\56\66\51\x20\x69\156\163\x65\x74\73\143\x6f\154\157\x72\x3a\40\43\106\106\106\73\x22\164\171\160\x65\75\x22\x62\165\164\164\x6f\156\42\40\x76\x61\x6c\165\x65\x3d\42\104\x6f\156\x65\42\x20\x6f\156\x43\x6c\151\143\153\75\42\163\145\x6c\146\56\143\154\x6f\x73\x65\x28\x29\73\42\76\x3c\x2f\144\x69\166\x3e";
        exit;
        IS:
        goto jk;
        fT:
        throw new Exception("\115\x6f\x72\145\40\x74\x68\x61\156\40\x6f\156\x65\x20\x3c\x73\x61\x6d\x6c\x3a\x4e\x61\155\145\111\x44\x3e\x20\157\x72\x20\x3c\163\141\x6d\154\x3a\105\x6e\x63\162\x79\x70\164\x65\x64\104\76\40\151\156\x20\74\x73\x61\155\x6c\x3a\123\x75\142\x6a\145\x63\164\x3e\56");
        jk:
        $Ix = $Ix[0];
        if ($Ix->localName === "\x45\x6e\x63\162\171\160\164\x65\x64\x44\141\x74\141") {
            goto tq;
        }
        $this->nameId = Utilities::parseNameId($Ix);
        goto Pn;
        tq:
        $this->encryptedNameId = $Ix;
        Pn:
    }
    private function parseConditions(DOMElement $mf)
    {
        $vt = Utilities::xpQuery($mf, "\56\57\x73\141\155\154\137\x61\x73\163\x65\162\x74\151\x6f\156\72\x43\x6f\156\x64\x69\x74\x69\157\156\x73");
        if (empty($vt)) {
            goto wx;
        }
        if (count($vt) > 1) {
            goto eY;
        }
        goto Ay;
        wx:
        return;
        goto Ay;
        eY:
        throw new Exception("\x4d\157\x72\145\x20\x74\150\141\x6e\x20\157\x6e\145\x20\74\x73\x61\x6d\x6c\x3a\103\x6f\156\144\x69\164\151\157\156\163\x3e\x20\x69\x6e\40\x3c\163\141\155\x6c\x3a\101\163\163\x65\162\x74\151\x6f\156\76\x2e");
        Ay:
        $vt = $vt[0];
        if (!$vt->hasAttribute("\x4e\x6f\x74\x42\145\x66\x6f\162\x65")) {
            goto HS;
        }
        $kP = Utilities::xsDateTimeToTimestamp($vt->getAttribute("\x4e\157\x74\102\145\x66\157\162\145"));
        if (!($this->notBefore === NULL || $this->notBefore < $kP)) {
            goto ZN;
        }
        $this->notBefore = $kP;
        ZN:
        HS:
        if (!$vt->hasAttribute("\116\157\x74\117\x6e\117\x72\101\x66\x74\x65\162")) {
            goto bf;
        }
        $LW = Utilities::xsDateTimeToTimestamp($vt->getAttribute("\x4e\x6f\x74\117\156\117\x72\101\146\x74\145\162"));
        if (!($this->notOnOrAfter === NULL || $this->notOnOrAfter > $LW)) {
            goto sU;
        }
        $this->notOnOrAfter = $LW;
        sU:
        bf:
        $p8 = $vt->firstChild;
        UR:
        if (!($p8 !== NULL)) {
            goto ut;
        }
        if (!$p8 instanceof DOMText) {
            goto ET;
        }
        goto No;
        ET:
        if (!($p8->namespaceURI !== "\165\162\156\72\157\x61\x73\151\163\x3a\x6e\141\155\x65\163\x3a\x74\143\72\x53\101\115\114\72\x32\56\x30\72\141\163\x73\145\162\x74\151\x6f\x6e")) {
            goto iZ;
        }
        throw new Exception("\125\x6e\x6b\x6e\x6f\x77\156\40\x6e\141\155\x65\x73\160\141\143\x65\40\x6f\146\40\143\x6f\156\x64\151\x74\151\157\x6e\x3a\40" . var_export($p8->namespaceURI, TRUE));
        iZ:
        switch ($p8->localName) {
            case "\x41\x75\144\x69\x65\x6e\x63\x65\122\x65\163\164\x72\x69\x63\164\x69\x6f\x6e":
                $bd = Utilities::extractStrings($p8, "\x75\x72\x6e\72\157\141\163\x69\163\x3a\156\141\x6d\x65\163\72\x74\143\x3a\x53\x41\115\x4c\72\x32\56\60\x3a\x61\163\x73\145\x72\164\x69\157\156", "\101\165\144\x69\145\x6e\x63\145");
                if ($this->validAudiences === NULL) {
                    goto e0;
                }
                $this->validAudiences = array_intersect($this->validAudiences, $bd);
                goto mL;
                e0:
                $this->validAudiences = $bd;
                mL:
                goto FB;
            case "\x4f\156\x65\x54\151\x6d\x65\x55\163\x65":
                goto FB;
            case "\120\162\x6f\x78\171\x52\145\163\164\162\x69\x63\x74\151\157\156":
                goto FB;
            default:
                throw new Exception("\125\156\153\x6e\157\x77\156\x20\x63\x6f\156\x64\151\x74\151\x6f\156\72\x20" . var_export($p8->localName, TRUE));
        }
        vx:
        FB:
        No:
        $p8 = $p8->nextSibling;
        goto UR;
        ut:
    }
    private function parseAuthnStatement(DOMElement $mf)
    {
        $qo = Utilities::xpQuery($mf, "\x2e\57\x73\x61\155\x6c\x5f\x61\163\163\145\162\164\x69\x6f\156\x3a\x41\x75\164\x68\x6e\123\164\141\164\145\x6d\x65\x6e\x74");
        if (empty($qo)) {
            goto Zj;
        }
        if (count($qo) > 1) {
            goto A7;
        }
        goto I_;
        Zj:
        $this->authnInstant = NULL;
        return;
        goto I_;
        A7:
        throw new Exception("\115\x6f\x72\145\x20\x74\150\141\x74\40\157\x6e\145\x20\74\163\x61\155\x6c\x3a\101\165\164\150\156\123\x74\x61\x74\145\155\145\x6e\x74\76\40\x69\156\x20\x3c\x73\x61\155\x6c\x3a\101\x73\163\x65\x72\164\x69\157\x6e\76\x20\x6e\157\x74\40\163\165\160\x70\x6f\162\164\145\144\56");
        I_:
        $dm = $qo[0];
        if ($dm->hasAttribute("\x41\x75\x74\150\x6e\x49\x6e\163\x74\141\x6e\164")) {
            goto sX;
        }
        throw new Exception("\115\x69\163\x73\x69\156\147\40\162\145\x71\165\151\162\145\x64\x20\101\x75\164\150\156\111\x6e\163\x74\141\156\164\40\141\x74\x74\162\151\x62\x75\164\145\x20\157\x6e\40\x3c\163\141\155\154\72\101\x75\164\150\156\x53\x74\x61\164\145\x6d\145\156\x74\76\x2e");
        sX:
        $this->authnInstant = Utilities::xsDateTimeToTimestamp($dm->getAttribute("\x41\165\164\x68\x6e\x49\x6e\x73\x74\141\x6e\x74"));
        if (!$dm->hasAttribute("\123\145\x73\163\151\x6f\x6e\x4e\x6f\164\117\156\x4f\x72\101\146\x74\x65\162")) {
            goto Uh;
        }
        $this->sessionNotOnOrAfter = Utilities::xsDateTimeToTimestamp($dm->getAttribute("\x53\145\x73\163\x69\157\156\x4e\x6f\164\x4f\156\x4f\x72\101\x66\x74\145\x72"));
        Uh:
        if (!$dm->hasAttribute("\123\145\x73\x73\151\x6f\156\111\156\x64\145\170")) {
            goto b_;
        }
        $this->sessionIndex = $dm->getAttribute("\123\x65\163\163\151\x6f\156\111\x6e\144\x65\170");
        b_:
        $this->parseAuthnContext($dm);
    }
    private function parseAuthnContext(DOMElement $eI)
    {
        $WN = Utilities::xpQuery($eI, "\56\57\163\141\155\154\137\141\163\x73\x65\x72\x74\x69\157\x6e\x3a\x41\165\x74\150\156\x43\157\156\x74\x65\170\x74");
        if (count($WN) > 1) {
            goto th;
        }
        if (empty($WN)) {
            goto UC;
        }
        goto m7;
        th:
        throw new Exception("\115\157\x72\x65\40\x74\x68\141\156\x20\x6f\x6e\x65\40\74\x73\141\x6d\154\72\101\165\x74\150\156\103\x6f\x6e\x74\145\170\164\76\40\151\x6e\x20\x3c\x73\x61\x6d\154\x3a\x41\165\164\150\156\x53\164\141\x74\145\x6d\x65\x6e\164\76\56");
        goto m7;
        UC:
        throw new Exception("\115\151\163\163\151\x6e\x67\x20\162\x65\x71\x75\x69\162\x65\x64\40\x3c\163\141\155\x6c\72\x41\x75\164\150\x6e\x43\157\156\x74\145\170\x74\76\40\x69\156\x20\74\x73\141\x6d\x6c\72\101\165\x74\x68\x6e\x53\164\x61\x74\145\155\x65\x6e\164\x3e\x2e");
        m7:
        $FP = $WN[0];
        $YX = Utilities::xpQuery($FP, "\56\x2f\x73\x61\155\x6c\x5f\x61\x73\163\145\x72\x74\x69\x6f\156\x3a\101\x75\x74\150\x6e\x43\157\x6e\x74\145\x78\x74\104\x65\143\154\x52\145\x66");
        if (count($YX) > 1) {
            goto Qt;
        }
        if (count($YX) === 1) {
            goto qc;
        }
        goto Eh;
        Qt:
        throw new Exception("\x4d\157\x72\145\40\164\x68\141\x6e\x20\157\156\x65\x20\x3c\163\141\155\154\x3a\101\x75\x74\150\156\x43\x6f\x6e\164\145\x78\164\x44\x65\x63\154\122\145\x66\x3e\40\x66\157\165\156\144\x3f");
        goto Eh;
        qc:
        $this->setAuthnContextDeclRef(trim($YX[0]->textContent));
        Eh:
        $Xj = Utilities::xpQuery($FP, "\56\57\163\x61\155\154\x5f\141\163\x73\145\x72\x74\151\157\x6e\72\101\165\164\150\156\x43\x6f\x6e\164\x65\170\164\x44\x65\x63\154");
        if (count($Xj) > 1) {
            goto r5;
        }
        if (count($Xj) === 1) {
            goto tN;
        }
        goto ja;
        r5:
        throw new Exception("\115\x6f\162\x65\x20\x74\150\x61\156\x20\x6f\x6e\145\x20\x3c\163\141\x6d\x6c\72\x41\165\164\x68\156\103\157\156\164\x65\170\164\x44\x65\143\x6c\76\x20\x66\157\x75\156\x64\x3f");
        goto ja;
        tN:
        $this->setAuthnContextDecl(new SAML2_XML_Chunk($Xj[0]));
        ja:
        $Ey = Utilities::xpQuery($FP, "\x2e\57\x73\x61\155\x6c\x5f\x61\163\163\145\x72\x74\x69\157\156\x3a\x41\165\x74\x68\156\x43\157\156\x74\145\170\164\103\x6c\141\163\163\x52\x65\x66");
        if (count($Ey) > 1) {
            goto Jy;
        }
        if (count($Ey) === 1) {
            goto J0;
        }
        goto gz;
        Jy:
        throw new Exception("\x4d\x6f\162\x65\40\164\150\x61\x6e\40\x6f\156\145\40\x3c\x73\x61\155\x6c\72\101\165\164\x68\x6e\103\x6f\x6e\x74\145\x78\164\103\x6c\141\x73\163\x52\145\146\76\x20\x69\156\40\x3c\x73\141\x6d\x6c\x3a\x41\165\164\x68\x6e\x43\x6f\156\x74\145\170\x74\76\x2e");
        goto gz;
        J0:
        $this->setAuthnContextClassRef(trim($Ey[0]->textContent));
        gz:
        if (!(empty($this->authnContextClassRef) && empty($this->authnContextDecl) && empty($this->authnContextDeclRef))) {
            goto lf;
        }
        throw new Exception("\115\151\163\x73\x69\156\x67\x20\145\x69\164\x68\145\x72\40\74\x73\x61\x6d\154\72\101\165\164\x68\156\x43\x6f\156\164\x65\x78\164\103\154\141\x73\x73\x52\145\x66\x3e\x20\157\x72\40\74\163\141\x6d\154\x3a\x41\165\164\x68\156\x43\x6f\x6e\164\145\170\164\104\x65\143\x6c\x52\x65\146\x3e\40\x6f\x72\x20\x3c\163\x61\x6d\154\x3a\x41\165\164\150\156\103\x6f\156\x74\x65\170\x74\x44\145\143\x6c\x3e");
        lf:
        $this->AuthenticatingAuthority = Utilities::extractStrings($FP, "\x75\x72\x6e\72\x6f\141\x73\151\x73\72\156\x61\x6d\145\x73\72\164\x63\72\x53\x41\115\114\72\x32\56\60\x3a\141\x73\163\145\x72\x74\151\x6f\x6e", "\101\165\164\x68\145\x6e\164\151\143\141\x74\151\x6e\147\x41\165\164\x68\x6f\x72\x69\164\171");
    }
    private function parseAttributes(DOMElement $mf)
    {
        $YO = TRUE;
        $Z7 = Utilities::xpQuery($mf, "\x2e\57\163\x61\x6d\x6c\x5f\141\x73\x73\145\x72\164\x69\x6f\156\x3a\x41\x74\x74\x72\151\x62\x75\x74\145\x53\164\141\164\x65\155\145\156\x74\57\163\141\x6d\x6c\137\x61\163\163\145\x72\x74\151\x6f\x6e\x3a\101\x74\164\162\x69\142\165\x74\x65");
        foreach ($Z7 as $Yz) {
            if ($Yz->hasAttribute("\116\141\155\145")) {
                goto J9;
            }
            throw new Exception("\115\151\x73\163\x69\156\x67\x20\x6e\x61\x6d\145\40\157\x6e\40\x3c\163\x61\x6d\x6c\72\101\164\x74\x72\151\x62\165\164\145\x3e\40\x65\x6c\x65\155\145\x6e\164\56");
            J9:
            $UO = $Yz->getAttribute("\116\141\x6d\x65");
            if ($Yz->hasAttribute("\x4e\141\x6d\x65\106\x6f\x72\155\x61\164")) {
                goto tG;
            }
            $jk = "\x75\x72\x6e\x3a\157\x61\x73\151\163\72\156\141\155\x65\163\x3a\164\143\x3a\x53\101\x4d\x4c\x3a\x31\56\x31\x3a\156\141\x6d\145\x69\x64\x2d\146\157\x72\x6d\141\x74\x3a\x75\156\163\160\145\x63\151\146\x69\145\144";
            goto WN;
            tG:
            $jk = $Yz->getAttribute("\x4e\141\x6d\145\x46\157\x72\155\141\164");
            WN:
            if ($YO) {
                goto nP;
            }
            if (!($this->nameFormat !== $jk)) {
                goto c_;
            }
            $this->nameFormat = "\165\x72\156\x3a\157\141\163\x69\163\x3a\156\141\x6d\145\163\x3a\164\x63\x3a\x53\101\115\x4c\72\x31\x2e\x31\x3a\156\x61\155\145\151\144\x2d\x66\x6f\162\155\141\x74\x3a\x75\x6e\x73\160\x65\x63\151\x66\151\145\144";
            c_:
            goto qg;
            nP:
            $this->nameFormat = $jk;
            $YO = FALSE;
            qg:
            if (array_key_exists($UO, $this->attributes)) {
                goto TX;
            }
            $this->attributes[$UO] = array();
            TX:
            $jQ = Utilities::xpQuery($Yz, "\56\x2f\163\141\x6d\154\x5f\141\163\163\145\162\x74\x69\157\156\72\101\x74\164\x72\151\142\x75\164\145\x56\x61\154\165\x65");
            foreach ($jQ as $wE) {
                $this->attributes[$UO][] = trim($wE->textContent);
                jl:
            }
            NU:
            nt:
        }
        WI:
    }
    private function parseEncryptedAttributes(DOMElement $mf)
    {
        $this->encryptedAttribute = Utilities::xpQuery($mf, "\x2e\57\x73\141\x6d\154\137\141\163\163\145\162\164\151\157\156\x3a\x41\164\164\x72\151\142\x75\x74\x65\123\x74\141\x74\x65\155\145\156\x74\57\163\141\x6d\154\137\x61\x73\x73\x65\x72\x74\x69\x6f\x6e\x3a\x45\x6e\143\162\x79\x70\164\x65\144\x41\x74\164\162\x69\x62\165\x74\x65");
    }
    private function parseSignature(DOMElement $mf)
    {
        $p9 = Utilities::validateElement($mf);
        if (!($p9 !== FALSE)) {
            goto it;
        }
        $this->wasSignedAtConstruction = TRUE;
        $this->certificates = $p9["\x43\x65\162\164\151\146\151\143\x61\164\x65\x73"];
        $this->signatureData = $p9;
        it:
    }
    public function validate(XMLSecurityKey $XC)
    {
        if (!($this->signatureData === NULL)) {
            goto Im;
        }
        return FALSE;
        Im:
        Utilities::validateSignature($this->signatureData, $XC);
        return TRUE;
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
    public function getIssuer()
    {
        return $this->issuer;
    }
    public function setIssuer($Jm)
    {
        $this->issuer = $Jm;
    }
    public function getNameId()
    {
        if (!($this->encryptedNameId !== NULL)) {
            goto YH;
        }
        throw new Exception("\x41\164\x74\x65\155\160\x74\x65\144\x20\164\157\40\x72\x65\x74\162\x69\145\x76\145\x20\x65\156\x63\162\x79\160\164\x65\x64\x20\116\x61\155\x65\x49\104\40\x77\151\164\x68\x6f\x75\x74\x20\144\145\x63\x72\x79\160\164\151\x6e\x67\x20\151\x74\x20\x66\x69\x72\x73\x74\x2e");
        YH:
        return $this->nameId;
    }
    public function setNameId($Ix)
    {
        $this->nameId = $Ix;
    }
    public function isNameIdEncrypted()
    {
        if (!($this->encryptedNameId !== NULL)) {
            goto TO;
        }
        return TRUE;
        TO:
        return FALSE;
    }
    public function encryptNameId(XMLSecurityKey $XC)
    {
        $ZR = new DOMDocument();
        $W2 = $ZR->createElement("\162\x6f\157\x74");
        $ZR->appendChild($W2);
        Utilities::addNameId($W2, $this->nameId);
        $Ix = $W2->firstChild;
        Utilities::getContainer()->debugMessage($Ix, "\145\156\143\x72\171\160\x74");
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
            goto GV;
        }
        return;
        GV:
        $Ix = Utilities::decryptElement($this->encryptedNameId, $XC, $F_);
        Utilities::getContainer()->debugMessage($Ix, "\144\145\143\x72\171\x70\x74");
        $this->nameId = Utilities::parseNameId($Ix);
        $this->encryptedNameId = NULL;
    }
    public function decryptAttributes(XMLSecurityKey $XC, array $F_ = array())
    {
        if (!($this->encryptedAttribute === NULL)) {
            goto gg;
        }
        return;
        gg:
        $YO = TRUE;
        $Z7 = $this->encryptedAttribute;
        foreach ($Z7 as $Te) {
            $Yz = Utilities::decryptElement($Te->getElementsByTagName("\105\156\x63\x72\x79\160\164\x65\x64\x44\x61\x74\141")->item(0), $XC, $F_);
            if ($Yz->hasAttribute("\116\x61\155\x65")) {
                goto DB;
            }
            throw new Exception("\x4d\x69\163\x73\x69\156\x67\x20\x6e\141\x6d\145\x20\x6f\x6e\40\74\x73\x61\155\154\72\x41\x74\x74\x72\151\142\x75\164\x65\x3e\x20\x65\154\x65\x6d\x65\x6e\x74\x2e");
            DB:
            $UO = $Yz->getAttribute("\116\141\x6d\145");
            if ($Yz->hasAttribute("\116\141\x6d\145\106\157\x72\155\x61\164")) {
                goto te;
            }
            $jk = "\165\x72\x6e\x3a\157\x61\163\x69\x73\72\x6e\x61\x6d\x65\x73\x3a\164\143\x3a\123\x41\115\114\72\x32\56\60\x3a\x61\x74\x74\162\x6e\141\155\145\55\x66\x6f\x72\x6d\x61\164\72\165\156\x73\160\x65\143\x69\x66\x69\145\144";
            goto L3;
            te:
            $jk = $Yz->getAttribute("\116\141\155\x65\106\157\x72\x6d\141\x74");
            L3:
            if ($YO) {
                goto ai;
            }
            if (!($this->nameFormat !== $jk)) {
                goto wG;
            }
            $this->nameFormat = "\x75\162\156\72\x6f\141\163\151\163\72\156\x61\x6d\145\163\72\x74\x63\x3a\x53\101\115\114\x3a\62\56\x30\72\x61\x74\164\162\x6e\x61\x6d\x65\x2d\146\157\162\x6d\x61\164\72\165\156\x73\160\145\143\151\146\151\x65\144";
            wG:
            goto fJ;
            ai:
            $this->nameFormat = $jk;
            $YO = FALSE;
            fJ:
            if (array_key_exists($UO, $this->attributes)) {
                goto e3;
            }
            $this->attributes[$UO] = array();
            e3:
            $jQ = Utilities::xpQuery($Yz, "\x2e\x2f\163\141\155\154\x5f\x61\x73\x73\145\162\x74\x69\x6f\156\x3a\101\x74\x74\162\x69\x62\x75\x74\x65\x56\141\154\165\145");
            foreach ($jQ as $wE) {
                $this->attributes[$UO][] = trim($wE->textContent);
                UT:
            }
            hd:
            Ln:
        }
        Hm:
    }
    public function getNotBefore()
    {
        return $this->notBefore;
    }
    public function setNotBefore($kP)
    {
        $this->notBefore = $kP;
    }
    public function getNotOnOrAfter()
    {
        return $this->notOnOrAfter;
    }
    public function setNotOnOrAfter($LW)
    {
        $this->notOnOrAfter = $LW;
    }
    public function setEncryptedAttributes($uX)
    {
        $this->requiredEncAttributes = $uX;
    }
    public function getValidAudiences()
    {
        return $this->validAudiences;
    }
    public function setValidAudiences(array $q3 = NULL)
    {
        $this->validAudiences = $q3;
    }
    public function getAuthnInstant()
    {
        return $this->authnInstant;
    }
    public function setAuthnInstant($Vw)
    {
        $this->authnInstant = $Vw;
    }
    public function getSessionNotOnOrAfter()
    {
        return $this->sessionNotOnOrAfter;
    }
    public function setSessionNotOnOrAfter($zi)
    {
        $this->sessionNotOnOrAfter = $zi;
    }
    public function getSessionIndex()
    {
        return $this->sessionIndex;
    }
    public function setSessionIndex($L_)
    {
        $this->sessionIndex = $L_;
    }
    public function getAuthnContext()
    {
        if (empty($this->authnContextClassRef)) {
            goto Ef;
        }
        return $this->authnContextClassRef;
        Ef:
        if (empty($this->authnContextDeclRef)) {
            goto eQ;
        }
        return $this->authnContextDeclRef;
        eQ:
        return NULL;
    }
    public function setAuthnContext($oS)
    {
        $this->setAuthnContextClassRef($oS);
    }
    public function getAuthnContextClassRef()
    {
        return $this->authnContextClassRef;
    }
    public function setAuthnContextClassRef($Vt)
    {
        $this->authnContextClassRef = $Vt;
    }
    public function setAuthnContextDecl(SAML2_XML_Chunk $mM)
    {
        if (empty($this->authnContextDeclRef)) {
            goto py;
        }
        throw new Exception("\101\x75\x74\x68\x6e\103\157\156\x74\x65\x78\164\x44\145\143\x6c\122\145\146\40\x69\x73\40\x61\x6c\x72\145\141\144\171\x20\162\x65\x67\x69\163\x74\x65\162\145\144\x21\40\115\141\x79\40\157\x6e\154\x79\40\x68\141\166\145\40\145\x69\164\150\145\162\40\141\x20\104\145\x63\154\40\x6f\x72\x20\141\40\104\x65\143\154\122\145\146\x2c\40\x6e\157\164\x20\x62\x6f\164\150\x21");
        py:
        $this->authnContextDecl = $mM;
    }
    public function getAuthnContextDecl()
    {
        return $this->authnContextDecl;
    }
    public function setAuthnContextDeclRef($ux)
    {
        if (empty($this->authnContextDecl)) {
            goto nH;
        }
        throw new Exception("\101\165\164\150\156\x43\x6f\156\164\145\x78\x74\104\x65\143\x6c\40\x69\163\40\141\154\162\145\141\144\x79\40\162\x65\x67\x69\163\164\x65\x72\x65\144\x21\x20\115\141\171\40\x6f\x6e\x6c\x79\x20\x68\141\166\x65\40\x65\151\164\150\145\x72\x20\x61\x20\x44\x65\x63\x6c\40\x6f\162\40\x61\x20\x44\145\143\x6c\x52\145\x66\x2c\40\x6e\157\x74\x20\x62\157\x74\150\x21");
        nH:
        $this->authnContextDeclRef = $ux;
    }
    public function getAuthnContextDeclRef()
    {
        return $this->authnContextDeclRef;
    }
    public function getAuthenticatingAuthority()
    {
        return $this->AuthenticatingAuthority;
    }
    public function setAuthenticatingAuthority($et)
    {
        $this->AuthenticatingAuthority = $et;
    }
    public function getAttributes()
    {
        return $this->attributes;
    }
    public function setAttributes(array $Z7)
    {
        $this->attributes = $Z7;
    }
    public function getAttributeNameFormat()
    {
        return $this->nameFormat;
    }
    public function setAttributeNameFormat($jk)
    {
        $this->nameFormat = $jk;
    }
    public function getSubjectConfirmation()
    {
        return $this->SubjectConfirmation;
    }
    public function setSubjectConfirmation(array $Kq)
    {
        $this->SubjectConfirmation = $Kq;
    }
    public function getSignatureKey()
    {
        return $this->signatureKey;
    }
    public function setSignatureKey(XMLsecurityKey $Pu = NULL)
    {
        $this->signatureKey = $Pu;
    }
    public function getEncryptionKey()
    {
        return $this->encryptionKey;
    }
    public function setEncryptionKey(XMLSecurityKey $jg = NULL)
    {
        $this->encryptionKey = $jg;
    }
    public function setCertificates(array $re)
    {
        $this->certificates = $re;
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
    public function toXML(DOMNode $k7 = NULL)
    {
        if ($k7 === NULL) {
            goto Qp;
        }
        $Lx = $k7->ownerDocument;
        goto RU;
        Qp:
        $Lx = new DOMDocument();
        $k7 = $Lx;
        RU:
        $W2 = $Lx->createElementNS("\165\x72\x6e\72\157\141\x73\x69\x73\x3a\x6e\141\155\x65\163\x3a\x74\143\x3a\123\101\115\114\72\62\56\x30\72\141\163\163\x65\x72\x74\151\157\x6e", "\x73\x61\x6d\x6c\x3a" . "\x41\163\x73\x65\162\164\x69\x6f\156");
        $k7->appendChild($W2);
        $W2->setAttributeNS("\165\162\156\x3a\x6f\x61\163\151\163\x3a\x6e\x61\x6d\145\x73\72\164\x63\x3a\x53\101\x4d\114\72\62\x2e\x30\x3a\x70\162\x6f\x74\x6f\143\x6f\x6c", "\163\141\x6d\x6c\160\72\164\155\160", "\164\x6d\x70");
        $W2->removeAttributeNS("\165\x72\156\x3a\x6f\141\x73\x69\x73\x3a\156\141\155\145\163\72\x74\x63\x3a\x53\101\x4d\x4c\72\62\x2e\60\x3a\x70\x72\x6f\164\x6f\x63\157\x6c", "\x74\155\x70");
        $W2->setAttributeNS("\x68\164\164\x70\x3a\x2f\57\x77\167\167\56\167\63\56\x6f\162\147\x2f\62\x30\60\x31\57\130\x4d\x4c\123\x63\x68\145\x6d\141\x2d\x69\x6e\163\x74\x61\x6e\x63\x65", "\x78\x73\151\x3a\x74\155\x70", "\164\x6d\160");
        $W2->removeAttributeNS("\x68\164\164\160\x3a\57\57\167\167\167\x2e\x77\63\56\x6f\162\147\x2f\62\x30\60\x31\x2f\130\x4d\114\123\143\x68\145\x6d\x61\x2d\151\x6e\x73\x74\141\156\x63\x65", "\x74\x6d\160");
        $W2->setAttributeNS("\x68\x74\164\160\72\x2f\x2f\167\x77\x77\56\x77\63\x2e\157\x72\147\57\x32\60\x30\61\57\130\115\x4c\123\x63\x68\x65\155\x61", "\170\163\72\164\155\x70", "\164\x6d\x70");
        $W2->removeAttributeNS("\x68\x74\164\160\x3a\57\x2f\x77\167\167\x2e\x77\63\56\x6f\x72\x67\57\x32\x30\x30\61\57\130\x4d\114\x53\143\x68\145\155\141", "\164\x6d\x70");
        $W2->setAttribute("\111\x44", $this->id);
        $W2->setAttribute("\x56\x65\x72\x73\x69\157\x6e", "\62\x2e\x30");
        $W2->setAttribute("\111\x73\163\165\145\111\x6e\163\164\141\156\164", gmdate("\x59\x2d\x6d\x2d\x64\x5c\124\110\72\x69\72\x73\134\x5a", $this->issueInstant));
        $Jm = Utilities::addString($W2, "\165\x72\x6e\72\x6f\141\163\151\163\72\156\141\155\x65\x73\72\x74\143\72\123\x41\115\114\72\x32\56\60\x3a\141\x73\x73\x65\x72\x74\151\157\156", "\x73\141\x6d\154\x3a\111\163\x73\x75\x65\x72", $this->issuer);
        $this->addSubject($W2);
        $this->addConditions($W2);
        $this->addAuthnStatement($W2);
        if ($this->requiredEncAttributes == FALSE) {
            goto UP;
        }
        $this->addEncryptedAttributeStatement($W2);
        goto aU;
        UP:
        $this->addAttributeStatement($W2);
        aU:
        if (!($this->signatureKey !== NULL)) {
            goto bC;
        }
        Utilities::insertSignature($this->signatureKey, $this->certificates, $W2, $Jm->nextSibling);
        bC:
        return $W2;
    }
    private function addSubject(DOMElement $W2)
    {
        if (!($this->nameId === NULL && $this->encryptedNameId === NULL)) {
            goto jO;
        }
        return;
        jO:
        $Cd = $W2->ownerDocument->createElementNS("\x75\162\156\72\x6f\141\163\x69\163\72\156\141\x6d\145\163\x3a\164\143\x3a\x53\101\115\x4c\72\x32\x2e\x30\x3a\x61\163\x73\145\x72\x74\x69\157\156", "\x73\141\155\154\x3a\x53\165\142\152\145\x63\x74");
        $W2->appendChild($Cd);
        if ($this->encryptedNameId === NULL) {
            goto f9;
        }
        $B8 = $Cd->ownerDocument->createElementNS("\165\162\156\72\x6f\141\163\151\x73\72\x6e\x61\155\x65\x73\72\x74\x63\72\x53\101\x4d\114\72\x32\56\x30\72\141\x73\x73\145\x72\x74\x69\157\156", "\x73\141\155\x6c\x3a" . "\x45\x6e\143\x72\171\x70\x74\x65\144\x49\x44");
        $Cd->appendChild($B8);
        $B8->appendChild($Cd->ownerDocument->importNode($this->encryptedNameId, TRUE));
        goto pO;
        f9:
        Utilities::addNameId($Cd, $this->nameId);
        pO:
        foreach ($this->SubjectConfirmation as $vl) {
            $vl->toXML($Cd);
            HO:
        }
        p3:
    }
    private function addConditions(DOMElement $W2)
    {
        $Lx = $W2->ownerDocument;
        $vt = $Lx->createElementNS("\165\x72\x6e\72\157\141\x73\x69\x73\72\x6e\x61\x6d\x65\163\x3a\x74\143\72\x53\x41\115\114\72\62\56\x30\x3a\141\x73\x73\145\x72\x74\151\x6f\x6e", "\163\x61\155\x6c\x3a\x43\x6f\156\144\151\164\151\x6f\156\x73");
        $W2->appendChild($vt);
        if (!($this->notBefore !== NULL)) {
            goto J4;
        }
        $vt->setAttribute("\116\157\164\x42\145\x66\x6f\162\x65", gmdate("\131\x2d\x6d\x2d\144\134\124\x48\x3a\151\x3a\163\x5c\x5a", $this->notBefore));
        J4:
        if (!($this->notOnOrAfter !== NULL)) {
            goto PU;
        }
        $vt->setAttribute("\x4e\x6f\164\117\x6e\x4f\x72\101\146\x74\145\162", gmdate("\131\x2d\155\55\x64\134\x54\110\72\151\x3a\163\x5c\x5a", $this->notOnOrAfter));
        PU:
        if (!($this->validAudiences !== NULL)) {
            goto XR;
        }
        $KP = $Lx->createElementNS("\x75\x72\156\72\157\x61\x73\151\x73\72\156\x61\x6d\x65\x73\72\164\x63\x3a\123\101\115\114\72\x32\x2e\x30\x3a\x61\163\x73\145\162\x74\x69\x6f\x6e", "\x73\141\155\154\72\101\x75\x64\x69\x65\156\143\x65\x52\x65\x73\164\162\x69\143\164\x69\x6f\156");
        $vt->appendChild($KP);
        Utilities::addStrings($KP, "\x75\162\x6e\72\157\141\163\151\x73\72\156\x61\x6d\145\163\x3a\164\143\x3a\123\101\115\x4c\72\x32\x2e\60\72\x61\163\163\145\162\164\x69\157\x6e", "\x73\141\x6d\x6c\72\x41\165\x64\151\145\156\x63\x65", FALSE, $this->validAudiences);
        XR:
    }
    private function addAuthnStatement(DOMElement $W2)
    {
        if (!($this->authnInstant === NULL || $this->authnContextClassRef === NULL && $this->authnContextDecl === NULL && $this->authnContextDeclRef === NULL)) {
            goto rc;
        }
        return;
        rc:
        $Lx = $W2->ownerDocument;
        $eI = $Lx->createElementNS("\x75\162\156\x3a\157\x61\x73\151\163\72\156\x61\155\145\x73\72\x74\143\x3a\x53\101\x4d\114\72\62\x2e\x30\x3a\141\163\163\x65\162\x74\151\x6f\156", "\x73\141\x6d\154\x3a\x41\165\x74\150\x6e\123\x74\x61\x74\145\155\x65\156\x74");
        $W2->appendChild($eI);
        $eI->setAttribute("\x41\165\x74\x68\x6e\x49\x6e\x73\x74\141\x6e\x74", gmdate("\x59\55\155\55\144\x5c\124\110\x3a\151\72\163\x5c\132", $this->authnInstant));
        if (!($this->sessionNotOnOrAfter !== NULL)) {
            goto w1;
        }
        $eI->setAttribute("\123\x65\x73\x73\151\x6f\x6e\116\157\164\117\156\x4f\162\101\x66\x74\x65\162", gmdate("\131\55\x6d\x2d\x64\x5c\x54\x48\72\x69\72\163\x5c\132", $this->sessionNotOnOrAfter));
        w1:
        if (!($this->sessionIndex !== NULL)) {
            goto UK;
        }
        $eI->setAttribute("\x53\145\x73\x73\x69\157\156\x49\x6e\144\x65\170", $this->sessionIndex);
        UK:
        $FP = $Lx->createElementNS("\165\162\x6e\72\157\141\x73\x69\x73\x3a\156\141\x6d\145\x73\72\164\143\x3a\x53\101\x4d\x4c\x3a\62\x2e\60\72\141\x73\x73\x65\162\164\x69\157\x6e", "\x73\x61\155\154\x3a\101\x75\x74\150\156\x43\157\156\x74\145\x78\164");
        $eI->appendChild($FP);
        if (empty($this->authnContextClassRef)) {
            goto o9;
        }
        Utilities::addString($FP, "\165\x72\156\x3a\157\x61\163\151\x73\72\156\141\x6d\145\163\72\x74\143\72\x53\x41\x4d\114\72\x32\56\60\x3a\141\163\163\x65\x72\164\151\x6f\156", "\x73\x61\x6d\154\x3a\x41\x75\x74\x68\x6e\x43\x6f\156\164\x65\x78\164\x43\154\141\163\x73\122\145\146", $this->authnContextClassRef);
        o9:
        if (empty($this->authnContextDecl)) {
            goto pm;
        }
        $this->authnContextDecl->toXML($FP);
        pm:
        if (empty($this->authnContextDeclRef)) {
            goto Xm;
        }
        Utilities::addString($FP, "\x75\x72\156\x3a\157\x61\x73\151\x73\x3a\x6e\141\x6d\x65\x73\x3a\164\x63\x3a\123\x41\x4d\x4c\x3a\x32\x2e\60\x3a\141\x73\x73\x65\x72\164\x69\x6f\x6e", "\163\x61\155\x6c\72\x41\x75\164\x68\156\x43\157\156\164\145\x78\x74\x44\x65\x63\x6c\x52\145\x66", $this->authnContextDeclRef);
        Xm:
        Utilities::addStrings($FP, "\x75\x72\156\72\157\141\x73\151\163\72\x6e\141\155\145\163\x3a\x74\143\72\123\101\115\114\72\62\x2e\x30\x3a\x61\163\x73\x65\x72\x74\151\157\x6e", "\163\141\x6d\x6c\x3a\x41\165\164\150\x65\156\164\x69\143\x61\x74\151\x6e\x67\x41\165\x74\x68\157\x72\151\164\171", FALSE, $this->AuthenticatingAuthority);
    }
    private function addAttributeStatement(DOMElement $W2)
    {
        if (!empty($this->attributes)) {
            goto gR;
        }
        return;
        gR:
        $Lx = $W2->ownerDocument;
        $E5 = $Lx->createElementNS("\165\x72\x6e\72\157\141\163\151\163\72\156\141\155\x65\x73\72\164\143\x3a\x53\x41\x4d\114\72\62\x2e\x30\72\141\x73\x73\x65\x72\164\151\x6f\x6e", "\x73\x61\155\154\x3a\101\x74\x74\x72\151\x62\165\164\145\x53\164\x61\164\x65\155\x65\x6e\x74");
        $W2->appendChild($E5);
        foreach ($this->attributes as $UO => $jQ) {
            $Yz = $Lx->createElementNS("\x75\162\x6e\x3a\x6f\141\163\x69\x73\72\156\141\x6d\x65\x73\72\164\143\72\x53\x41\x4d\114\x3a\62\56\60\72\x61\163\163\x65\x72\164\151\157\x6e", "\163\x61\x6d\154\x3a\x41\164\164\x72\x69\142\165\x74\x65");
            $E5->appendChild($Yz);
            $Yz->setAttribute("\x4e\x61\155\x65", $UO);
            if (!($this->nameFormat !== "\165\x72\156\72\157\141\163\151\163\x3a\156\x61\155\x65\x73\x3a\x74\143\x3a\x53\x41\x4d\114\72\62\56\x30\72\141\x74\x74\x72\x6e\141\155\x65\55\x66\x6f\x72\155\x61\x74\x3a\165\x6e\163\160\x65\x63\151\x66\x69\x65\144")) {
                goto rX;
            }
            $Yz->setAttribute("\116\141\x6d\x65\x46\x6f\162\155\141\x74", $this->nameFormat);
            rX:
            foreach ($jQ as $wE) {
                if (is_string($wE)) {
                    goto uJ;
                }
                if (is_int($wE)) {
                    goto N7;
                }
                $Ts = NULL;
                goto De;
                uJ:
                $Ts = "\170\163\x3a\163\x74\x72\151\x6e\x67";
                goto De;
                N7:
                $Ts = "\x78\x73\x3a\x69\x6e\x74\145\147\x65\162";
                De:
                $Ia = $Lx->createElementNS("\165\x72\x6e\72\x6f\x61\x73\x69\x73\72\x6e\141\x6d\x65\x73\72\x74\143\72\123\101\x4d\114\72\x32\x2e\x30\x3a\x61\x73\x73\145\162\x74\151\x6f\156", "\x73\141\x6d\x6c\x3a\101\x74\164\162\x69\x62\x75\164\x65\x56\141\154\x75\x65");
                $Yz->appendChild($Ia);
                if (!($Ts !== NULL)) {
                    goto Mj;
                }
                $Ia->setAttributeNS("\150\164\164\160\72\57\x2f\167\x77\167\x2e\x77\x33\56\x6f\162\x67\57\62\60\60\61\x2f\130\x4d\114\123\x63\x68\x65\155\x61\x2d\x69\156\x73\x74\141\x6e\143\145", "\170\x73\151\72\164\x79\x70\145", $Ts);
                Mj:
                if (!is_null($wE)) {
                    goto yB;
                }
                $Ia->setAttributeNS("\150\x74\x74\x70\72\57\57\x77\x77\167\x2e\167\63\x2e\x6f\162\147\57\x32\60\x30\x31\57\x58\115\x4c\x53\x63\x68\145\x6d\x61\55\151\x6e\163\x74\x61\x6e\x63\x65", "\x78\163\x69\72\156\x69\154", "\164\162\x75\x65");
                yB:
                if ($wE instanceof DOMNodeList) {
                    goto yU;
                }
                $Ia->appendChild($Lx->createTextNode($wE));
                goto x8;
                yU:
                $vF = 0;
                BA:
                if (!($vF < $wE->length)) {
                    goto SG;
                }
                $p8 = $Lx->importNode($wE->item($vF), TRUE);
                $Ia->appendChild($p8);
                xZ:
                $vF++;
                goto BA;
                SG:
                x8:
                jS:
            }
            ax:
            KI:
        }
        ez:
    }
    private function addEncryptedAttributeStatement(DOMElement $W2)
    {
        if (!($this->requiredEncAttributes == FALSE)) {
            goto wn;
        }
        return;
        wn:
        $Lx = $W2->ownerDocument;
        $E5 = $Lx->createElementNS("\165\162\156\x3a\157\141\x73\x69\x73\x3a\x6e\141\x6d\145\163\72\164\x63\x3a\123\101\x4d\x4c\72\x32\x2e\60\72\141\163\163\145\x72\x74\151\x6f\156", "\163\141\x6d\154\x3a\x41\x74\164\x72\x69\x62\x75\x74\x65\123\164\141\x74\145\155\145\x6e\x74");
        $W2->appendChild($E5);
        foreach ($this->attributes as $UO => $jQ) {
            $rc = new DOMDocument();
            $Yz = $rc->createElementNS("\x75\162\156\x3a\157\x61\x73\x69\163\72\x6e\141\x6d\145\163\72\164\x63\72\x53\101\x4d\114\x3a\x32\x2e\x30\x3a\x61\x73\x73\145\162\164\x69\157\156", "\163\141\x6d\x6c\x3a\101\x74\164\162\151\142\165\164\x65");
            $Yz->setAttribute("\116\141\x6d\x65", $UO);
            $rc->appendChild($Yz);
            if (!($this->nameFormat !== "\x75\x72\x6e\x3a\x6f\141\163\151\x73\x3a\x6e\141\x6d\145\163\72\x74\143\72\x53\101\x4d\114\72\62\56\x30\x3a\x61\164\164\162\x6e\x61\155\x65\55\146\157\162\x6d\x61\x74\x3a\x75\156\163\160\145\143\151\x66\x69\x65\144")) {
                goto ls;
            }
            $Yz->setAttribute("\x4e\x61\x6d\x65\x46\x6f\162\155\141\x74", $this->nameFormat);
            ls:
            foreach ($jQ as $wE) {
                if (is_string($wE)) {
                    goto kq;
                }
                if (is_int($wE)) {
                    goto EE;
                }
                $Ts = NULL;
                goto yp;
                kq:
                $Ts = "\170\163\x3a\163\x74\x72\151\x6e\147";
                goto yp;
                EE:
                $Ts = "\x78\x73\x3a\151\x6e\164\x65\x67\145\162";
                yp:
                $Ia = $rc->createElementNS("\x75\162\156\72\157\141\163\x69\163\x3a\156\x61\155\x65\163\x3a\x74\x63\72\x53\x41\115\114\72\x32\56\60\72\x61\163\163\145\162\164\151\157\x6e", "\163\141\x6d\154\72\x41\164\164\x72\151\142\x75\164\x65\126\141\x6c\165\x65");
                $Yz->appendChild($Ia);
                if (!($Ts !== NULL)) {
                    goto i0;
                }
                $Ia->setAttributeNS("\150\x74\164\x70\x3a\57\57\x77\x77\167\x2e\x77\63\56\x6f\162\147\x2f\62\60\x30\61\57\130\115\x4c\123\x63\150\x65\155\x61\x2d\151\156\163\x74\x61\x6e\143\145", "\170\163\x69\x3a\x74\171\160\x65", $Ts);
                i0:
                if ($wE instanceof DOMNodeList) {
                    goto vP;
                }
                $Ia->appendChild($rc->createTextNode($wE));
                goto gN;
                vP:
                $vF = 0;
                ua:
                if (!($vF < $wE->length)) {
                    goto AP;
                }
                $p8 = $rc->importNode($wE->item($vF), TRUE);
                $Ia->appendChild($p8);
                aj:
                $vF++;
                goto ua;
                AP:
                gN:
                P5:
            }
            Oc:
            $wH = new XMLSecEnc();
            $wH->setNode($rc->documentElement);
            $wH->type = "\x68\x74\164\x70\72\x2f\57\167\x77\167\56\x77\x33\56\x6f\x72\147\57\x32\x30\60\x31\57\60\x34\57\x78\x6d\x6c\x65\156\143\x23\105\154\x65\x6d\145\x6e\x74";
            $A1 = new XMLSecurityKey(XMLSecurityKey::AES256_CBC);
            $A1->generateSessionKey();
            $wH->encryptKey($this->encryptionKey, $A1);
            $mF = $wH->encryptNode($A1);
            $gz = $Lx->createElementNS("\165\x72\x6e\72\x6f\141\163\151\x73\72\156\x61\155\145\x73\72\x74\x63\x3a\x53\x41\x4d\x4c\72\x32\56\60\x3a\141\163\163\145\x72\x74\x69\157\x6e", "\163\x61\155\x6c\72\105\x6e\x63\162\x79\x70\x74\145\x64\101\164\x74\x72\151\x62\165\x74\145");
            $E5->appendChild($gz);
            $Sq = $Lx->importNode($mF, TRUE);
            $gz->appendChild($Sq);
            io:
        }
        rC:
    }
    public function getPrivateKeyUrl()
    {
        return $this->privateKeyUrl;
    }
    public function setPrivateKeyUrl($n_)
    {
        $this->privateKeyUrl = $n_;
    }
}
