<?php


include_once "\125\164\151\154\x69\164\x69\x65\163\x2e\x70\150\x70";
class MetadataReader
{
    private $identityProviders;
    private $serviceProviders;
    public function __construct(DOMNode $nb = NULL)
    {
        $this->identityProviders = array();
        $this->serviceProviders = array();
        $SD = Utilities::xpQuery($nb, "\56\x2f\x73\141\x6d\x6c\137\155\x65\x74\141\x64\141\164\141\72\x45\156\164\151\164\171\104\145\163\x63\162\x69\x70\164\157\162");
        foreach ($SD as $rb) {
            $Tr = Utilities::xpQuery($rb, "\56\x2f\x73\x61\155\x6c\x5f\x6d\145\164\141\x64\x61\164\x61\x3a\x49\x44\120\x53\123\117\x44\x65\163\143\x72\151\x70\164\157\162");
            if (!(isset($Tr) && !empty($Tr))) {
                goto cFm;
            }
            array_push($this->identityProviders, new IdentityProviders($rb));
            cFm:
            YCT:
        }
        SPH:
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
    public function __construct(DOMElement $nb = NULL)
    {
        $this->idpName = '';
        $this->loginDetails = array();
        $this->logoutDetails = array();
        $this->signingCertificate = array();
        $this->encryptionCertificate = array();
        if (!$nb->hasAttribute("\x65\156\x74\x69\164\171\111\x44")) {
            goto BMN;
        }
        $this->entityID = $nb->getAttribute("\x65\156\164\151\164\x79\111\x44");
        BMN:
        if (!$nb->hasAttribute("\127\141\x6e\164\101\x75\x74\150\156\122\x65\x71\x75\x65\x73\164\x73\123\151\x67\x6e\x65\x64")) {
            goto i77;
        }
        $this->signedRequest = $nb->getAttribute("\x57\x61\x6e\164\101\165\x74\150\x6e\122\x65\161\165\145\x73\164\163\x53\x69\147\x6e\145\x64");
        i77:
        $Tr = Utilities::xpQuery($nb, "\56\x2f\x73\141\155\154\x5f\155\x65\x74\141\x64\x61\x74\141\72\111\x44\x50\123\123\x4f\x44\145\x73\143\x72\x69\160\x74\157\162");
        if (count($Tr) > 1) {
            goto q4_;
        }
        if (empty($Tr)) {
            goto oLc;
        }
        goto Hec;
        q4_:
        throw new Exception("\x4d\157\162\145\40\164\150\x61\x6e\x20\x6f\156\x65\x20\74\111\104\120\123\x53\x4f\x44\x65\163\143\x72\x69\160\164\157\x72\x3e\x20\x69\156\40\74\105\x6e\x74\x69\x74\x79\x44\145\x73\143\x72\151\160\x74\157\162\x3e\x2e");
        goto Hec;
        oLc:
        throw new Exception("\115\x69\163\x73\151\156\147\x20\162\145\x71\x75\x69\x72\x65\x64\x20\74\x49\x44\120\123\x53\117\x44\145\163\143\162\151\160\164\157\x72\76\40\151\x6e\x20\74\x45\x6e\164\x69\164\171\x44\145\163\143\x72\x69\160\x74\157\x72\x3e\x2e");
        Hec:
        $oi = $Tr[0];
        $Ar = Utilities::xpQuery($nb, "\56\x2f\163\x61\155\x6c\x5f\x6d\145\x74\x61\144\x61\164\x61\72\105\170\x74\x65\156\x73\151\x6f\156\x73");
        if (!$Ar) {
            goto he2;
        }
        $this->parseInfo($oi);
        he2:
        $this->parseSSOService($oi);
        $this->parseSLOService($oi);
        $this->parsex509Certificate($oi);
    }
    private function parseInfo($nb)
    {
        $p7 = Utilities::xpQuery($nb, "\x2e\57\x6d\144\x75\151\x3a\x55\111\111\x6e\146\x6f\x2f\x6d\144\x75\151\72\x44\151\x73\x70\x6c\x61\171\116\x61\155\x65");
        foreach ($p7 as $dC) {
            if (!($dC->hasAttribute("\x78\155\154\72\x6c\141\156\147") && $dC->getAttribute("\x78\x6d\x6c\72\154\x61\x6e\x67") == "\x65\x6e")) {
                goto fYt;
            }
            $this->idpName = $dC->textContent;
            fYt:
            zhr:
        }
        ax4:
    }
    private function parseSSOService($nb)
    {
        $r_ = Utilities::xpQuery($nb, "\x2e\57\163\141\x6d\154\137\x6d\145\x74\x61\x64\x61\x74\141\72\123\151\x6e\147\x6c\145\123\x69\x67\156\117\156\x53\145\x72\x76\x69\x63\145");
        $GN = 0;
        foreach ($r_ as $KX) {
            $GL = str_replace("\165\162\156\72\x6f\x61\x73\x69\163\72\x6e\141\x6d\145\x73\72\x74\x63\72\x53\101\x4d\114\72\62\x2e\x30\x3a\x62\151\x6e\144\x69\x6e\x67\163\72", '', $KX->getAttribute("\x42\x69\x6e\x64\151\156\x67"));
            $this->loginDetails = array_merge($this->loginDetails, array($GL => $KX->getAttribute("\114\157\x63\141\x74\151\157\156")));
            if (!($GL == "\x48\x54\x54\x50\x2d\x52\145\x64\151\162\x65\143\x74")) {
                goto D5k;
            }
            $GN = 1;
            $this->loginbinding = "\x48\164\164\x70\122\145\144\151\162\x65\x63\x74";
            D5k:
            UzO:
        }
        mtc:
        if ($GN) {
            goto nJY;
        }
        $this->loginbinding = "\110\164\x74\160\120\x6f\x73\x74";
        nJY:
    }
    private function parseSLOService($nb)
    {
        $GN = 0;
        $o1 = Utilities::xpQuery($nb, "\56\57\163\x61\155\154\137\155\x65\164\141\144\x61\x74\141\x3a\x53\x69\x6e\147\x6c\145\114\157\147\157\x75\x74\x53\145\162\166\x69\143\145");
        foreach ($o1 as $Me) {
            $GL = str_replace("\x75\x72\x6e\72\x6f\x61\x73\x69\163\x3a\156\141\x6d\x65\x73\x3a\x74\x63\72\x53\x41\x4d\x4c\x3a\x32\56\x30\x3a\x62\151\156\x64\x69\156\x67\163\x3a", '', $Me->getAttribute("\102\151\156\144\151\x6e\x67"));
            $this->logoutDetails = array_merge($this->logoutDetails, array($GL => $Me->getAttribute("\114\157\143\141\164\x69\x6f\156")));
            if (!($GL == "\110\124\x54\120\55\x52\x65\144\151\x72\x65\x63\x74")) {
                goto pA3;
            }
            $GN = 1;
            $this->logoutbinding = "\x48\x74\164\x70\x52\145\x64\x69\162\x65\x63\x74";
            pA3:
            EiH:
        }
        RZf:
        if (!empty($this->logoutbinding)) {
            goto Uuu;
        }
        $this->logoutbinding = "\x48\x74\164\x70\120\157\163\164";
        Uuu:
    }
    private function parsex509Certificate($nb)
    {
        foreach (Utilities::xpQuery($nb, "\56\x2f\x73\141\155\154\137\155\145\164\141\144\x61\x74\x61\72\x4b\145\171\104\145\x73\x63\x72\x69\x70\x74\x6f\x72") as $e0) {
            if ($e0->hasAttribute("\165\163\145")) {
                goto D_g;
            }
            $this->parseSigningCertificate($e0);
            goto IGR;
            D_g:
            if ($e0->getAttribute("\165\163\145") == "\145\156\143\162\x79\160\164\x69\157\x6e") {
                goto zYm;
            }
            $this->parseSigningCertificate($e0);
            goto P1Q;
            zYm:
            $this->parseEncryptionCertificate($e0);
            P1Q:
            IGR:
            MvV:
        }
        GGD:
    }
    private function parseSigningCertificate($nb)
    {
        $cW = Utilities::xpQuery($nb, "\x2e\57\x64\163\x3a\113\x65\171\111\156\x66\157\57\144\x73\72\x58\x35\60\71\104\x61\164\141\57\144\x73\x3a\130\65\x30\x39\x43\x65\x72\x74\151\146\151\143\141\x74\x65");
        $jO = trim($cW[0]->textContent);
        $jO = str_replace(array("\15", "\xa", "\x9", "\40"), '', $jO);
        if (empty($cW)) {
            goto P0q;
        }
        array_push($this->signingCertificate, $jO);
        P0q:
    }
    private function parseEncryptionCertificate($nb)
    {
        $cW = Utilities::xpQuery($nb, "\56\57\x64\x73\72\x4b\x65\171\111\156\146\157\x2f\144\163\x3a\130\x35\60\71\x44\141\164\x61\x2f\x64\x73\x3a\130\x35\60\x39\x43\x65\162\164\x69\146\151\143\x61\x74\145");
        $jO = trim($cW[0]->textContent);
        $jO = str_replace(array("\xd", "\xa", "\x9", "\x20"), '', $jO);
        if (empty($cW)) {
            goto hDM;
        }
        array_push($this->encryptionCertificate, $jO);
        hDM:
    }
    public function getIdpName()
    {
        return $this->idpName;
    }
    public function getEntityID()
    {
        return $this->entityID;
    }
    public function getLoginURL($GL)
    {
        return $this->loginDetails[$GL];
    }
    public function getLogoutURL($GL)
    {
        return $this->logoutDetails[$GL];
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
