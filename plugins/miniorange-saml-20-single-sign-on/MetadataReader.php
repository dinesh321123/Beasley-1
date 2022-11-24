<?php


include_once "\125\x74\x69\x6c\151\164\x69\145\163\x2e\x70\150\x70";
class MetadataReader
{
    private $identityProviders;
    private $serviceProviders;
    public function __construct(DOMNode $mf = NULL)
    {
        $this->identityProviders = array();
        $this->serviceProviders = array();
        $tr = Utilities::xpQuery($mf, "\56\x2f\163\141\x6d\154\137\x6d\x65\x74\x61\x64\x61\164\x61\x3a\x45\156\164\151\x74\151\x65\x73\104\145\x73\x63\x72\151\x70\164\157\x72");
        if (!empty($tr)) {
            goto Od;
        }
        $Vc = Utilities::xpQuery($mf, "\x2e\57\163\141\x6d\x6c\137\x6d\145\164\x61\x64\141\x74\141\72\x45\156\x74\x69\164\x79\104\145\163\143\x72\151\160\x74\157\x72");
        goto t2;
        Od:
        $Vc = Utilities::xpQuery($tr[0], "\56\57\x73\141\155\x6c\x5f\155\145\x74\x61\144\x61\x74\141\72\x45\156\164\151\164\171\104\x65\163\x63\162\x69\160\x74\x6f\x72");
        t2:
        foreach ($Vc as $ac) {
            $VG = Utilities::xpQuery($ac, "\x2e\x2f\163\x61\155\x6c\137\155\x65\x74\x61\x64\141\x74\x61\x3a\x49\104\x50\x53\123\117\104\x65\163\143\x72\151\160\164\x6f\x72");
            if (!(isset($VG) && !empty($VG))) {
                goto gu;
            }
            array_push($this->identityProviders, new IdentityProviders($ac));
            gu:
            bd:
        }
        pM:
    }
    public function getIdentityProviders()
    {
        return $this->identityProviders;
    }
    public function getServiceProviders()
    {
        return $this->serviceProviders;
    }
}
class IdentityProviders
{
    private $idpName;
    private $entityID;
    private $loginDetails;
    private $logoutDetails;
    private $signingCertificate;
    private $encryptionCertificate;
    private $signedRequest;
    private $loginbinding;
    private $logoutbinding;
    public function __construct(DOMElement $mf = NULL)
    {
        $this->idpName = '';
        $this->loginDetails = array();
        $this->logoutDetails = array();
        $this->signingCertificate = array();
        $this->encryptionCertificate = array();
        if (!$mf->hasAttribute("\x65\156\164\x69\164\x79\x49\104")) {
            goto O8;
        }
        $this->entityID = $mf->getAttribute("\x65\156\164\x69\164\171\x49\104");
        O8:
        if (!$mf->hasAttribute("\x57\141\156\164\101\x75\x74\150\156\x52\145\x71\165\145\163\164\x73\123\x69\x67\x6e\145\x64")) {
            goto zk;
        }
        $this->signedRequest = $mf->getAttribute("\127\x61\156\x74\101\165\x74\150\156\122\145\x71\165\x65\x73\164\x73\x53\151\x67\156\x65\144");
        zk:
        $VG = Utilities::xpQuery($mf, "\x2e\57\x73\141\x6d\154\137\155\x65\x74\141\144\141\164\141\x3a\111\x44\120\123\123\x4f\x44\x65\163\143\x72\151\x70\164\x6f\162");
        if (count($VG) > 1) {
            goto tE;
        }
        if (empty($VG)) {
            goto l8;
        }
        goto EI;
        tE:
        throw new Exception("\x4d\x6f\162\145\40\164\150\141\156\x20\157\x6e\145\x20\x3c\111\104\120\123\x53\x4f\x44\x65\163\143\162\151\160\x74\x6f\x72\x3e\x20\x69\x6e\x20\x3c\105\x6e\164\x69\x74\171\104\x65\x73\143\x72\x69\160\164\x6f\x72\x3e\56");
        goto EI;
        l8:
        throw new Exception("\115\151\x73\x73\x69\x6e\x67\x20\x72\x65\161\165\x69\162\145\x64\40\x3c\111\104\120\123\x53\117\104\145\163\x63\162\151\x70\x74\157\162\76\x20\151\156\x20\x3c\x45\x6e\x74\x69\164\171\x44\x65\x73\143\162\151\x70\164\157\162\x3e\56");
        EI:
        $gb = $VG[0];
        $aL = Utilities::xpQuery($mf, "\x2e\57\x73\141\155\154\137\x6d\145\164\141\144\141\164\141\x3a\x45\170\x74\x65\156\163\x69\157\x6e\x73");
        if (!$aL) {
            goto Qv;
        }
        $this->parseInfo($gb);
        Qv:
        $this->parseSSOService($gb);
        $this->parseSLOService($gb);
        $this->parsex509Certificate($gb);
    }
    private function parseInfo($mf)
    {
        $pu = Utilities::xpQuery($mf, "\56\x2f\x6d\x64\x75\151\72\x55\x49\111\x6e\x66\157\57\x6d\x64\x75\151\72\x44\151\x73\x70\x6c\141\171\116\141\x6d\x65");
        foreach ($pu as $UO) {
            if (!($UO->hasAttribute("\170\155\x6c\x3a\x6c\141\156\x67") && $UO->getAttribute("\x78\155\x6c\72\x6c\x61\x6e\147") == "\x65\x6e")) {
                goto Pw;
            }
            $this->idpName = $UO->textContent;
            Pw:
            fS:
        }
        LP:
    }
    private function parseSSOService($mf)
    {
        $Dw = Utilities::xpQuery($mf, "\56\x2f\x73\x61\155\x6c\137\155\x65\x74\x61\x64\141\x74\x61\x3a\123\151\x6e\147\x6c\x65\123\x69\147\x6e\117\x6e\123\145\162\166\151\143\x65");
        $Ic = 0;
        foreach ($Dw as $I0) {
            $dG = str_replace("\x75\x72\x6e\72\x6f\x61\x73\151\x73\72\156\141\x6d\145\163\72\x74\x63\x3a\123\x41\115\x4c\72\62\x2e\x30\x3a\142\151\x6e\144\x69\x6e\147\x73\x3a", '', $I0->getAttribute("\x42\151\156\144\x69\x6e\x67"));
            $this->loginDetails = array_merge($this->loginDetails, array($dG => $I0->getAttribute("\114\157\143\141\x74\151\x6f\x6e")));
            if (!($dG == "\110\x54\124\x50\x2d\122\145\x64\x69\162\x65\143\x74")) {
                goto W0;
            }
            $Ic = 1;
            $this->loginbinding = "\x48\164\x74\x70\x52\145\144\x69\162\145\x63\x74";
            W0:
            y6:
        }
        rL:
        if ($Ic) {
            goto Q3;
        }
        $this->loginbinding = "\x48\164\x74\x70\120\157\163\x74";
        Q3:
    }
    private function parseSLOService($mf)
    {
        $Ic = 0;
        $zg = Utilities::xpQuery($mf, "\x2e\x2f\x73\141\155\x6c\x5f\x6d\145\164\141\144\x61\x74\141\72\123\151\x6e\x67\154\x65\114\x6f\147\157\165\164\x53\x65\x72\166\151\143\145");
        foreach ($zg as $k4) {
            $dG = str_replace("\x75\x72\x6e\x3a\157\141\x73\x69\x73\72\156\141\155\145\163\x3a\164\x63\72\123\101\x4d\114\x3a\62\56\60\72\x62\x69\x6e\x64\151\156\147\163\x3a", '', $k4->getAttribute("\x42\151\156\x64\x69\156\x67"));
            $this->logoutDetails = array_merge($this->logoutDetails, array($dG => $k4->getAttribute("\114\x6f\143\141\x74\x69\x6f\x6e")));
            if (!($dG == "\x48\x54\124\x50\x2d\122\x65\x64\x69\x72\x65\x63\x74")) {
                goto z2;
            }
            $Ic = 1;
            $this->logoutbinding = "\x48\164\x74\x70\122\x65\x64\151\x72\145\x63\x74";
            z2:
            Wv:
        }
        Ff:
        if (!empty($this->logoutbinding)) {
            goto bh;
        }
        $this->logoutbinding = "\110\x74\164\x70\120\157\x73\x74";
        bh:
    }
    private function parsex509Certificate($mf)
    {
        foreach (Utilities::xpQuery($mf, "\x2e\x2f\x73\141\x6d\x6c\x5f\155\145\164\141\x64\x61\164\x61\x3a\x4b\x65\171\104\x65\163\143\x72\151\160\x74\x6f\162") as $xR) {
            if ($xR->hasAttribute("\165\163\145")) {
                goto ZF;
            }
            $this->parseSigningCertificate($xR);
            goto Hj;
            ZF:
            if ($xR->getAttribute("\x75\163\x65") == "\145\x6e\143\162\x79\x70\164\x69\157\156") {
                goto wT;
            }
            $this->parseSigningCertificate($xR);
            goto PH;
            wT:
            $this->parseEncryptionCertificate($xR);
            PH:
            Hj:
            cj:
        }
        IJ:
    }
    private function parseSigningCertificate($mf)
    {
        $lm = Utilities::xpQuery($mf, "\x2e\x2f\144\x73\x3a\x4b\145\171\x49\156\x66\x6f\x2f\144\163\x3a\x58\x35\x30\71\x44\x61\x74\141\x2f\x64\x73\72\x58\x35\60\x39\x43\145\162\x74\x69\x66\151\x63\x61\164\x65");
        $mw = trim($lm[0]->textContent);
        $mw = str_replace(array("\15", "\12", "\x9", "\40"), '', $mw);
        if (empty($lm)) {
            goto OJ;
        }
        array_push($this->signingCertificate, $mw);
        OJ:
    }
    private function parseEncryptionCertificate($mf)
    {
        $lm = Utilities::xpQuery($mf, "\x2e\57\x64\163\72\x4b\x65\x79\x49\156\x66\x6f\x2f\x64\163\72\130\x35\60\x39\104\x61\164\141\x2f\144\x73\72\x58\65\60\x39\103\145\x72\x74\151\146\151\143\x61\x74\145");
        $mw = trim($lm[0]->textContent);
        $mw = str_replace(array("\xd", "\xa", "\x9", "\x20"), '', $mw);
        if (empty($lm)) {
            goto gP;
        }
        array_push($this->encryptionCertificate, $mw);
        gP:
    }
    public function getIdpName()
    {
        return $this->idpName;
    }
    public function getEntityID()
    {
        return $this->entityID;
    }
    public function getLoginURL($dG)
    {
        return $this->loginDetails[$dG];
    }
    public function getLogoutURL($dG)
    {
        return $this->logoutDetails[$dG];
    }
    public function getLoginDetails()
    {
        return $this->loginDetails;
    }
    public function getLogoutDetails()
    {
        return $this->logoutDetails;
    }
    public function getSigningCertificate()
    {
        return $this->signingCertificate;
    }
    public function getEncryptionCertificate()
    {
        return $this->encryptionCertificate[0];
    }
    public function isRequestSigned()
    {
        return $this->signedRequest;
    }
    public function getBindingLogin()
    {
        return $this->loginbinding;
    }
    public function getBindingLogout()
    {
        return $this->logoutbinding;
    }
}
class ServiceProviders
{
}
