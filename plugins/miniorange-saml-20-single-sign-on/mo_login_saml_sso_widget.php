<?php


include_once dirname(__FILE__) . "\57\125\164\151\x6c\x69\x74\x69\x65\x73\x2e\160\x68\x70";
include_once dirname(__FILE__) . "\x2f\x52\145\x73\x70\157\x6e\163\145\x2e\x70\150\x70";
include_once dirname(__FILE__) . "\x2f\114\x6f\x67\x6f\x75\x74\122\x65\x71\165\x65\x73\x74\x2e\x70\150\160";
require_once dirname(__FILE__) . "\57\151\156\x63\154\x75\x64\145\x73\57\x6c\x69\x62\57\x65\156\143\x72\171\x70\164\151\157\x6e\x2e\160\150\x70";
include_once "\170\x6d\x6c\x73\x65\143\154\x69\x62\x73\56\x70\150\x70";
use RobRichards\XMLSecLibs\XMLSecurityKey;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecEnc;
class mo_login_wid extends WP_Widget
{
    public function __construct()
    {
        $g3 = get_site_option("\x73\x61\155\154\137\151\144\145\156\164\151\164\171\137\156\x61\x6d\x65");
        parent::__construct("\x53\x61\x6d\x6c\137\114\x6f\147\151\156\x5f\x57\x69\x64\x67\145\164", "\x4c\x6f\x67\151\156\x20\x77\151\x74\x68\40" . $g3, array("\144\x65\163\x63\162\x69\x70\x74\151\157\156" => __("\x54\150\x69\x73\x20\x69\163\40\x61\40\155\151\156\151\x4f\x72\141\156\x67\145\40\123\x41\115\114\40\154\x6f\147\x69\x6e\x20\x77\151\x64\147\145\x74\56", "\155\x6f\x73\x61\155\x6c")));
    }
    public function widget($zr, $i9)
    {
        extract($zr);
        $HG = apply_filters("\x77\x69\x64\x67\145\164\137\164\151\x74\x6c\x65", $i9["\x77\151\144\137\164\x69\164\x6c\145"]);
        echo $zr["\142\x65\x66\157\x72\145\137\167\x69\144\147\x65\164"];
        if (empty($HG)) {
            goto fg;
        }
        echo $zr["\x62\145\146\157\x72\x65\137\164\x69\164\x6c\x65"] . $HG . $zr["\x61\146\164\x65\162\137\x74\151\x74\154\x65"];
        fg:
        $this->loginForm();
        echo $zr["\x61\x66\x74\145\x72\137\167\151\144\x67\145\x74"];
    }
    public function update($XG, $Ii)
    {
        $i9 = array();
        $i9["\x77\x69\144\137\164\151\164\154\x65"] = strip_tags($XG["\x77\x69\x64\137\164\151\x74\x6c\x65"]);
        return $i9;
    }
    public function form($i9)
    {
        $HG = '';
        if (!array_key_exists("\167\x69\144\x5f\164\x69\164\154\x65", $i9)) {
            goto b3;
        }
        $HG = $i9["\x77\x69\144\137\x74\151\164\x6c\145"];
        b3:
        echo "\12\x9\x9\x3c\x70\76\74\154\x61\x62\x65\154\40\146\157\162\75\42" . $this->get_field_id("\x77\x69\144\x5f\164\151\164\154\145") . "\40\42\76" . _e("\124\x69\x74\x6c\x65\72") . "\40\x3c\57\154\x61\142\145\x6c\76\xa\11\11\x9\x3c\x69\156\160\x75\x74\x20\x63\x6c\141\x73\163\75\42\167\151\x64\x65\x66\x61\164\x22\x20\151\x64\75\x22" . $this->get_field_id("\167\151\144\x5f\x74\151\164\154\x65") . "\42\40\x6e\141\x6d\145\75\x22" . $this->get_field_name("\x77\x69\144\x5f\164\x69\x74\x6c\145") . "\42\x20\164\171\x70\x65\x3d\x22\x74\145\x78\164\42\40\x76\x61\154\165\x65\x3d\42" . $HG . "\x22\40\x2f\x3e\xa\x9\11\74\x2f\x70\x3e";
    }
    public function loginForm()
    {
        global $post;
        $pK = get_site_option("\x73\141\155\154\137\x73\163\157\137\x73\x65\164\164\x69\x6e\x67\163");
        $NU = get_current_blog_id();
        $Lm = Utilities::get_active_sites();
        if (in_array($NU, $Lm)) {
            goto jH;
        }
        return;
        jH:
        if (!(empty($pK[$NU]) && !empty($pK["\104\x45\106\101\x55\x4c\124"]))) {
            goto kh;
        }
        $pK[$NU] = $pK["\x44\105\x46\x41\x55\x4c\124"];
        kh:
        if (!is_user_logged_in()) {
            goto ng;
        }
        $current_user = wp_get_current_user();
        $e2 = "\110\x65\x6c\x6c\157\54";
        if (empty($pK[$NU]["\155\x6f\137\163\141\155\154\137\x63\x75\163\164\157\x6d\137\x67\x72\145\x65\x74\x69\x6e\147\x5f\x74\145\x78\164"])) {
            goto kt;
        }
        $e2 = $pK[$NU]["\x6d\157\x5f\163\141\155\154\137\143\165\x73\x74\157\155\x5f\147\x72\145\x65\x74\151\156\147\x5f\x74\145\x78\164"];
        kt:
        $pw = '';
        if (empty($pK[$NU]["\x6d\x6f\x5f\x73\141\x6d\x6c\137\147\162\x65\x65\164\151\156\147\x5f\156\141\155\145"])) {
            goto NC;
        }
        switch ($pK[$NU]["\155\x6f\137\x73\141\x6d\154\x5f\147\162\145\145\x74\151\156\x67\137\x6e\141\155\x65"]) {
            case "\125\123\105\122\x4e\101\115\105":
                $pw = $current_user->user_login;
                goto F1;
            case "\105\115\101\x49\114":
                $pw = $current_user->user_email;
                goto F1;
            case "\106\116\101\x4d\105":
                $pw = $current_user->user_firstname;
                goto F1;
            case "\114\x4e\x41\115\x45":
                $pw = $current_user->user_lastname;
                goto F1;
            case "\106\116\x41\x4d\x45\x5f\114\116\101\115\105":
                $pw = $current_user->user_firstname . "\x20" . $current_user->user_lastname;
                goto F1;
            case "\x4c\x4e\x41\x4d\105\137\106\116\101\115\x45":
                $pw = $current_user->user_lastname . "\40" . $current_user->user_firstname;
                goto F1;
            default:
                $pw = $current_user->user_login;
        }
        gs:
        F1:
        NC:
        if (!empty(trim($pw))) {
            goto QD;
        }
        $pw = $current_user->user_login;
        QD:
        $Vo = $e2 . "\40" . $pw;
        $tD = "\x4c\157\x67\157\x75\x74";
        if (empty($pK[$NU]["\155\157\137\163\x61\155\154\x5f\x63\165\163\x74\157\155\x5f\154\157\147\x6f\165\164\x5f\x74\145\170\x74"])) {
            goto YG;
        }
        $tD = $pK[$NU]["\x6d\157\137\163\141\x6d\154\137\x63\x75\163\164\157\155\137\154\x6f\x67\157\165\x74\137\164\145\x78\x74"];
        YG:
        echo $Vo . "\x20\x7c\x20\x3c\141\40\x68\162\x65\x66\75\42" . wp_logout_url(home_url()) . "\42\x20\164\x69\x74\154\x65\75\x22\x6c\157\x67\x6f\x75\x74\42\x20\76" . $tD . "\x3c\57\x61\76\74\x2f\154\x69\x3e";
        goto pJ;
        ng:
        echo "\xa\11\x9\11\74\x73\x63\x72\151\x70\164\x3e\12\11\x9\x9\x9\x66\x75\156\143\164\151\x6f\x6e\40\x73\x75\142\x6d\x69\164\x53\x61\x6d\x6c\x46\157\x72\x6d\50\x29\x7b\x20\x64\157\x63\x75\x6d\145\156\164\x2e\147\145\164\x45\x6c\x65\x6d\x65\156\164\x42\x79\x49\x64\x28\42\x6c\157\x67\x69\156\x22\51\56\163\x75\x62\155\151\x74\50\51\73\x20\175\12\x9\x9\x9\74\57\163\143\162\x69\160\164\x3e\xa\x9\11\x9\x3c\x66\x6f\162\155\x20\156\141\x6d\145\x3d\42\x6c\157\x67\x69\156\42\40\151\144\x3d\42\154\157\x67\x69\x6e\x22\x20\x6d\145\x74\150\157\x64\x3d\x22\160\x6f\x73\164\x22\x20\141\x63\x74\x69\157\156\x3d\x22\x22\76\xa\11\x9\x9\11\x3c\151\x6e\x70\x75\164\40\x74\171\160\x65\x3d\42\x68\151\x64\x64\x65\x6e\42\x20\x6e\x61\x6d\x65\x3d\x22\157\160\164\x69\x6f\x6e\42\x20\x76\141\154\165\x65\75\x22\x73\x61\155\154\x5f\x75\x73\145\162\137\154\x6f\147\151\x6e\42\x20\x2f\76\12\12\11\x9\11\x9\x3c\x66\157\x6e\x74\40\x73\x69\x7a\145\75\x22\53\x31\x22\x20\x73\164\x79\x6c\x65\75\x22\166\x65\x72\164\x69\143\141\154\x2d\x61\x6c\151\147\156\x3a\164\157\x70\73\42\76\40\74\57\x66\157\x6e\164\76";
        $bj = get_site_option("\163\141\155\x6c\x5f\151\x64\145\156\164\x69\x74\171\137\x6e\x61\155\145");
        $y3 = get_site_option("\163\141\155\x6c\137\x78\x35\x30\71\x5f\143\x65\x72\x74\x69\146\151\143\141\164\145");
        if (!empty($bj) && !empty($y3)) {
            goto k1;
        }
        echo "\120\154\145\x61\x73\x65\40\x63\157\x6e\146\x69\147\x75\162\145\40\x74\x68\x65\40\x6d\151\156\151\x4f\162\x61\x6e\x67\145\x20\x53\x41\115\114\x20\x50\x6c\165\x67\151\156\40\146\x69\x72\x73\x74\x2e";
        goto dG;
        k1:
        $uJ = "\x4c\x6f\147\151\x6e\40\x77\151\x74\150\40\x23\x23\x49\104\x50\43\x23";
        if (empty($pK[$NU]["\x6d\157\137\x73\x61\155\x6c\137\x63\x75\163\164\x6f\155\137\154\157\x67\151\x6e\137\x74\145\x78\164"])) {
            goto im;
        }
        $uJ = $pK[$NU]["\155\x6f\x5f\x73\x61\155\154\137\x63\x75\x73\164\x6f\x6d\x5f\x6c\x6f\147\x69\x6e\x5f\164\145\170\x74"];
        im:
        $uJ = str_replace("\x23\43\x49\104\x50\43\x23", $bj, $uJ);
        $HC = false;
        if (!(isset($pK[$NU]["\155\x6f\x5f\x73\141\155\x6c\x5f\x75\x73\x65\137\142\x75\x74\164\x6f\x6e\137\141\x73\x5f\x77\x69\144\147\145\x74"]) && $pK[$NU]["\155\157\137\x73\x61\x6d\154\137\165\x73\145\x5f\142\x75\x74\x74\x6f\x6e\137\141\163\x5f\167\x69\144\x67\145\x74"] == "\164\x72\x75\x65")) {
            goto lJ;
        }
        $HC = true;
        lJ:
        if (!$HC) {
            goto BZ;
        }
        $WT = isset($pK[$NU]["\155\157\x5f\x73\x61\x6d\x6c\137\142\165\x74\164\157\x6e\137\167\x69\x64\164\x68"]) ? $pK[$NU]["\x6d\x6f\137\163\x61\x6d\154\137\142\165\x74\x74\157\156\137\x77\x69\x64\x74\150"] : "\61\60\x30";
        $Nt = isset($pK[$NU]["\x6d\x6f\137\163\x61\155\x6c\x5f\142\165\164\x74\x6f\156\x5f\x68\x65\x69\x67\150\x74"]) ? $pK[$NU]["\155\x6f\137\x73\141\x6d\154\x5f\142\x75\x74\x74\x6f\156\137\x68\x65\151\x67\150\x74"] : "\x35\x30";
        $Kq = isset($pK[$NU]["\x6d\157\137\163\x61\155\x6c\137\x62\x75\x74\x74\157\x6e\137\163\151\x7a\145"]) ? $pK[$NU]["\155\157\137\x73\x61\x6d\x6c\137\142\x75\x74\164\x6f\x6e\137\163\151\172\x65"] : "\x35\x30";
        $UZ = isset($pK[$NU]["\155\x6f\137\x73\x61\x6d\154\137\x62\x75\x74\164\157\x6e\137\x63\x75\x72\x76\x65"]) ? $pK[$NU]["\x6d\x6f\x5f\163\141\x6d\154\137\142\165\164\164\x6f\156\137\x63\x75\162\x76\x65"] : "\65";
        $qD = isset($pK[$NU]["\155\157\137\x73\x61\x6d\154\x5f\142\x75\x74\x74\x6f\x6e\x5f\x63\157\x6c\x6f\162"]) ? $pK[$NU]["\x6d\157\137\x73\141\155\x6c\137\142\x75\x74\164\157\156\137\143\157\x6c\x6f\x72"] : "\60\60\70\x35\x62\x61";
        $fM = isset($pK[$NU]["\x6d\157\x5f\x73\141\x6d\x6c\137\x62\x75\164\164\157\x6e\x5f\164\x68\x65\x6d\145"]) ? $pK[$NU]["\155\x6f\x5f\x73\141\155\x6c\x5f\142\165\x74\164\x6f\x6e\x5f\164\x68\145\155\145"] : "\154\157\x6e\x67\142\165\x74\x74\x6f\x6e";
        $D1 = isset($pK[$NU]["\x6d\157\137\x73\141\155\154\x5f\142\165\164\x74\157\x6e\x5f\x74\x65\x78\x74"]) ? $pK[$NU]["\x6d\157\137\163\x61\x6d\154\x5f\x62\165\164\x74\x6f\156\137\164\145\170\164"] : (get_site_option("\x73\x61\x6d\x6c\137\x69\x64\145\156\x74\151\x74\171\x5f\x6e\x61\155\145") ? get_site_option("\163\141\155\x6c\137\x69\144\145\x6e\x74\151\x74\x79\137\156\141\x6d\x65") : "\x4c\x6f\x67\151\x6e");
        $Qn = isset($pK[$NU]["\x6d\x6f\137\x73\141\x6d\x6c\137\x66\157\156\164\137\x63\x6f\154\x6f\162"]) ? $pK[$NU]["\x6d\x6f\x5f\x73\141\x6d\x6c\137\146\157\156\x74\137\x63\157\x6c\x6f\162"] : "\x66\146\146\x66\146\x66";
        $FY = isset($pK[$NU]["\x6d\157\x5f\163\x61\x6d\154\x5f\x66\x6f\156\x74\x5f\x73\151\x7a\145"]) ? $pK[$NU]["\155\157\137\x73\x61\x6d\154\x5f\146\x6f\156\164\137\163\x69\x7a\x65"] : "\x32\60";
        $Qk = isset($pK[$NU]["\x73\163\x6f\x5f\x62\165\x74\164\157\x6e\137\154\157\x67\151\156\137\146\157\x72\155\x5f\x70\157\163\x69\164\x69\x6f\156"]) ? $pK[$NU]["\x73\163\x6f\137\x62\165\164\x74\x6f\x6e\x5f\154\157\147\151\156\x5f\146\157\162\x6d\x5f\x70\157\163\151\x74\x69\x6f\156"] : "\141\142\157\166\x65";
        $uJ = "\74\x69\156\160\x75\x74\x20\x74\171\x70\145\75\x22\142\x75\x74\164\x6f\x6e\42\40\x6e\141\x6d\x65\x3d\42\155\157\137\163\x61\x6d\x6c\137\167\x70\x5f\163\x73\157\x5f\142\x75\x74\x74\x6f\156\x22\x20\166\141\154\165\x65\x3d\x22" . $D1 . "\x22\40\163\164\171\154\145\75\x22";
        $OF = '';
        if ($fM == "\154\x6f\x6e\147\142\165\164\x74\x6f\x6e") {
            goto Ij;
        }
        if ($fM == "\143\x69\x72\x63\x6c\145") {
            goto YD;
        }
        if ($fM == "\157\x76\x61\x6c") {
            goto lj;
        }
        if ($fM == "\163\161\165\141\162\145") {
            goto Hj;
        }
        goto Wi;
        YD:
        $OF = $OF . "\x77\x69\144\164\x68\72" . $Kq . "\160\170\x3b";
        $OF = $OF . "\150\145\x69\x67\x68\164\x3a" . $Kq . "\160\170\x3b";
        $OF = $OF . "\142\x6f\x72\x64\145\162\55\x72\141\144\x69\x75\163\72\71\x39\x39\x70\x78\73";
        goto Wi;
        lj:
        $OF = $OF . "\x77\x69\x64\164\x68\72" . $Kq . "\x70\x78\73";
        $OF = $OF . "\150\145\151\x67\x68\164\x3a" . $Kq . "\x70\170\73";
        $OF = $OF . "\x62\157\162\144\145\x72\55\162\141\x64\x69\165\x73\72\65\160\x78\73";
        goto Wi;
        Hj:
        $OF = $OF . "\167\151\x64\164\x68\x3a" . $Kq . "\x70\170\x3b";
        $OF = $OF . "\150\145\x69\147\x68\164\x3a" . $Kq . "\x70\x78\73";
        $OF = $OF . "\142\157\x72\x64\x65\162\55\x72\x61\144\x69\165\163\72\60\160\x78\73";
        Wi:
        goto mh;
        Ij:
        $OF = $OF . "\167\x69\144\164\150\x3a" . $WT . "\160\x78\73";
        $OF = $OF . "\150\145\151\x67\150\164\x3a" . $Nt . "\x70\170\x3b";
        $OF = $OF . "\x62\x6f\x72\144\x65\x72\x2d\x72\x61\144\151\x75\163\72" . $UZ . "\x70\170\73";
        mh:
        $OF = $OF . "\x62\141\x63\153\x67\162\157\165\156\144\x2d\x63\157\x6c\157\162\x3a\43" . $qD . "\x3b";
        $OF = $OF . "\142\x6f\162\144\x65\162\55\143\157\x6c\x6f\x72\x3a\x74\162\x61\156\163\160\141\x72\x65\156\x74\73";
        $OF = $OF . "\143\157\x6c\157\162\72\x23" . $Qn . "\73";
        $OF = $OF . "\x66\157\156\164\55\x73\x69\172\x65\72" . $FY . "\x70\x78\x3b";
        $OF = $OF . "\x70\141\144\x64\x69\x6e\x67\72\x30\x70\170\x3b";
        $uJ = $uJ . $OF . "\x22\57\76";
        BZ:
        ?>
 <a href="#" onClick="submitSamlForm()"><?php 
        echo $uJ;
        ?>
</a></form> <?php 
        dG:
        if ($this->mo_saml_check_empty_or_null_val(get_site_option("\155\157\x5f\x73\x61\155\x6c\x5f\162\145\x64\x69\x72\145\x63\x74\x5f\x65\162\x72\157\x72\x5f\143\157\x64\145"))) {
            goto Xj;
        }
        echo "\x3c\x64\151\x76\x3e\74\57\144\x69\x76\76\x3c\x64\x69\166\x20\164\151\164\154\145\75\x22\114\157\147\x69\x6e\x20\105\162\162\x6f\162\x22\76\x3c\146\x6f\x6e\164\x20\143\x6f\154\157\162\75\42\162\145\x64\x22\76\127\x65\x20\x63\x6f\x75\x6c\144\x20\156\157\x74\40\x73\x69\x67\156\x20\x79\157\165\x20\151\156\56\40\x50\154\x65\141\163\x65\40\143\x6f\156\x74\141\x63\164\x20\171\x6f\165\x72\40\101\144\x6d\x69\156\151\x73\x74\162\141\x74\x6f\x72\56\x3c\x2f\146\x6f\156\164\76\74\57\144\x69\x76\x3e";
        delete_site_option("\x6d\x6f\x5f\163\x61\155\154\x5f\x72\x65\144\x69\162\x65\x63\x74\x5f\145\162\x72\x6f\x72\137\143\157\144\145");
        delete_site_option("\x6d\157\137\163\x61\x6d\154\137\162\145\x64\x69\x72\x65\x63\x74\137\145\x72\162\x6f\162\137\162\x65\141\163\157\156");
        Xj:
        echo "\74\141\40\x68\x72\x65\146\x3d\42\x68\x74\x74\160\x3a\x2f\57\x6d\151\x6e\151\x6f\162\141\156\x67\145\x2e\x63\x6f\155\57\x77\157\x72\x64\x70\x72\x65\163\x73\55\x6c\x64\x61\160\55\154\x6f\147\151\156\42\x20\x73\x74\171\x6c\x65\75\x22\144\151\x73\x70\154\141\x79\x3a\x6e\x6f\x6e\x65\x22\x3e\114\157\147\151\x6e\x20\x74\x6f\x20\127\x6f\162\x64\120\162\x65\163\x73\40\165\x73\151\x6e\x67\x20\114\104\101\x50\x3c\57\x61\76\xa\11\x9\x9\11\74\x61\x20\150\x72\145\146\x3d\42\x68\164\164\160\72\x2f\x2f\x6d\x69\156\151\157\x72\x61\x6e\x67\x65\56\143\157\155\57\x63\154\x6f\165\144\x2d\x69\x64\145\156\164\x69\x74\171\55\x62\162\x6f\153\145\162\55\x73\145\x72\166\151\143\145\42\40\x73\164\x79\154\145\75\42\x64\x69\163\x70\154\141\x79\72\156\157\x6e\145\x22\76\x43\154\157\x75\x64\x20\111\x64\x65\156\x74\151\164\x79\40\142\x72\x6f\x6b\145\162\40\163\145\x72\166\151\143\145\x3c\x2f\x61\x3e\xa\x9\x9\x9\11\74\141\x20\x68\162\x65\146\x3d\42\x68\x74\x74\x70\72\57\57\155\151\156\151\x6f\x72\141\156\147\x65\x2e\143\157\x6d\57\163\164\162\157\156\147\137\141\165\164\x68\42\40\x73\x74\171\154\145\75\x22\144\x69\163\160\154\141\x79\x3a\x6e\157\156\x65\x3b\42\76\x3c\x2f\141\x3e\12\11\x9\11\x9\x3c\x61\40\x68\x72\x65\x66\75\42\150\164\164\160\x3a\x2f\x2f\x6d\151\156\x69\157\x72\x61\x6e\147\x65\56\x63\157\x6d\57\x73\151\x6e\x67\154\x65\55\163\x69\x67\156\x2d\x6f\x6e\55\x73\163\x6f\x22\x20\x73\x74\x79\154\145\x3d\x22\144\x69\x73\x70\x6c\141\171\72\156\157\x6e\x65\x3b\x22\76\74\x2f\141\76\12\x9\11\x9\11\x3c\141\40\150\x72\145\x66\75\42\x68\x74\164\160\x3a\x2f\x2f\x6d\x69\x6e\x69\157\162\x61\x6e\147\145\x2e\143\x6f\155\57\146\162\141\x75\x64\42\40\163\x74\171\154\x65\75\x22\x64\151\x73\160\154\x61\x79\x3a\x6e\x6f\156\145\73\42\76\x3c\x2f\x61\x3e\xa\12\x9\x9\11\x3c\x2f\165\154\x3e\12\11\x9\x3c\x2f\146\157\162\x6d\76";
        pJ:
    }
    public function mo_saml_check_empty_or_null_val($zF)
    {
        if (!(!isset($zF) || empty($zF))) {
            goto gW;
        }
        return true;
        gW:
        return false;
    }
    function mo_saml_logout($dI, $ZQ, $user)
    {
        $wM = get_site_option("\163\x61\155\154\137\154\x6f\x67\x6f\x75\x74\x5f\165\162\154");
        $vJ = get_site_option("\x73\x61\155\x6c\137\154\157\147\x6f\165\164\137\142\151\156\x64\151\x6e\x67\x5f\x74\171\x70\x65");
        $current_user = $user;
        $nO = get_user_meta($current_user->ID, "\x6d\157\x5f\x73\x61\x6d\x6c\x5f\151\144\160\x5f\x6c\x6f\x67\151\156");
        $nO = isset($nO[0]) ? $nO[0] : '';
        $V2 = wp_get_referer();
        if (!empty($V2)) {
            goto tr;
        }
        $V2 = !empty(get_site_option("\x6d\157\x5f\163\x61\x6d\154\x5f\x73\x70\x5f\142\141\x73\x65\137\165\x72\154")) ? get_site_option("\x6d\x6f\x5f\163\x61\155\154\x5f\x73\160\x5f\142\x61\163\145\137\165\162\x6c") : get_network_site_url();
        tr:
        if (!empty($wM)) {
            goto ui;
        }
        wp_redirect($V2);
        die;
        goto fC;
        ui:
        if (!(!session_id() || session_id() == '' || !isset($_SESSION))) {
            goto M9;
        }
        session_start();
        M9:
        if (isset($_SESSION["\155\157\x5f\163\141\155\x6c\x5f\x6c\157\147\157\165\164\137\x72\x65\161\165\x65\163\164"])) {
            goto xw;
        }
        if ($nO == "\164\162\x75\145") {
            goto HD;
        }
        wp_redirect($V2);
        die;
        goto u2;
        xw:
        self::createLogoutResponseAndRedirect($wM, $vJ);
        die;
        goto u2;
        HD:
        delete_user_meta($current_user->ID, "\155\x6f\137\163\141\155\x6c\x5f\x69\144\x70\137\154\157\147\151\156");
        $Xe = get_user_meta($current_user->ID, "\x6d\x6f\137\x73\141\x6d\x6c\137\156\x61\155\x65\x5f\x69\x64");
        $Kw = get_user_meta($current_user->ID, "\155\x6f\137\163\x61\x6d\x6c\137\x73\145\x73\163\151\x6f\156\137\151\x6e\x64\145\x78");
        $Wz = get_site_option("\x6d\157\x5f\163\141\155\154\x5f\x73\160\137\142\141\x73\145\137\x75\162\154");
        if (!empty($Wz)) {
            goto tL;
        }
        $Wz = get_network_site_url();
        tL:
        $iB = get_site_option("\155\x6f\137\163\x61\x6d\154\x5f\163\x70\137\x65\156\x74\151\x74\171\137\x69\144");
        if (!empty($iB)) {
            goto eP;
        }
        $iB = $Wz . "\57\x77\160\55\x63\x6f\x6e\164\145\156\x74\x2f\160\154\165\x67\x69\x6e\163\57\x6d\151\156\151\157\x72\141\156\147\x65\55\x73\x61\x6d\154\55\x32\x30\55\x73\x69\x6e\147\x6c\145\55\x73\151\147\156\55\157\156\x2f";
        eP:
        $LW = $wM;
        $rX = $V2;
        if (!empty($rX)) {
            goto m2;
        }
        $rX = saml_get_current_page_url();
        if (!strpos($rX, "\77")) {
            goto fz;
        }
        $rX = get_network_site_url();
        fz:
        m2:
        $rX = mo_saml_relaystate_url($rX);
        $GW = Utilities::createLogoutRequest($Xe, $Kw, $iB, $LW, $vJ);
        if (empty($vJ) || $vJ == "\110\x74\x74\160\122\x65\144\151\x72\145\x63\164") {
            goto Dc;
        }
        if (!(get_site_option("\x73\x61\155\154\137\x72\x65\161\x75\145\x73\164\137\163\x69\x67\156\x65\x64") == "\x75\156\143\x68\x65\x63\153\x65\144")) {
            goto FH;
        }
        $Qv = base64_encode($GW);
        Utilities::postSAMLRequest($wM, $Qv, $rX);
        die;
        FH:
        $wZ = '';
        $z1 = '';
        $Qv = Utilities::signXML($GW, "\116\x61\155\145\111\x44\x50\x6f\x6c\x69\x63\171");
        Utilities::postSAMLRequest($wM, $Qv, $rX);
        goto oc;
        Dc:
        $cJ = $wM;
        if (strpos($wM, "\77") !== false) {
            goto Dk;
        }
        $cJ .= "\x3f";
        goto oo;
        Dk:
        $cJ .= "\x26";
        oo:
        if (!(get_site_option("\163\x61\155\x6c\137\x72\145\x71\x75\145\x73\164\x5f\163\x69\147\x6e\145\x64") == "\x75\x6e\143\x68\x65\x63\x6b\x65\x64")) {
            goto QF;
        }
        $cJ .= "\123\101\x4d\x4c\x52\145\161\165\x65\x73\164\75" . $GW . "\46\122\145\x6c\141\171\x53\x74\x61\164\145\x3d" . urlencode($rX);
        header("\x4c\157\143\141\164\151\x6f\x6e\72\x20" . $cJ);
        die;
        QF:
        $GW = "\123\x41\x4d\114\x52\145\x71\165\145\163\x74\75" . $GW . "\x26\x52\x65\154\x61\x79\x53\164\141\164\145\x3d" . urlencode($rX) . "\x26\123\151\x67\x41\x6c\x67\75" . urlencode(XMLSecurityKey::RSA_SHA256);
        $nQ = array("\164\x79\x70\x65" => "\x70\x72\x69\166\x61\164\145");
        $Z1 = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, $nQ);
        $od = get_site_option("\x6d\x6f\137\x73\x61\155\154\x5f\143\x75\162\162\x65\156\x74\137\x63\145\x72\x74\137\160\162\151\166\x61\164\145\137\153\145\171");
        $Z1->loadKey($od, FALSE);
        $b4 = new XMLSecurityDSig();
        $sD = $Z1->signData($GW);
        $sD = base64_encode($sD);
        $cJ .= $GW . "\x26\x53\151\x67\156\x61\x74\x75\162\145\75" . urlencode($sD);
        header("\114\157\143\x61\x74\151\157\156\72" . $cJ);
        die;
        oc:
        u2:
        fC:
    }
    function createLogoutResponseAndRedirect($wM, $vJ)
    {
        $Wz = get_site_option("\x6d\x6f\137\x73\x61\x6d\154\x5f\163\x70\137\x62\141\x73\x65\137\165\x72\154");
        if (!empty($Wz)) {
            goto Nv;
        }
        $Wz = get_network_site_url();
        Nv:
        $gp = $_SESSION["\155\x6f\137\163\x61\x6d\x6c\137\x6c\x6f\147\x6f\x75\x74\137\x72\145\161\x75\x65\163\x74"];
        $Hr = $_SESSION["\155\x6f\x5f\x73\x61\x6d\154\x5f\154\x6f\147\157\x75\164\137\x72\x65\154\141\171\x5f\x73\x74\141\164\145"];
        unset($_SESSION["\155\157\137\x73\141\155\x6c\137\x6c\157\147\157\165\164\x5f\162\x65\161\165\x65\163\x74"]);
        unset($_SESSION["\x6d\157\x5f\x73\x61\x6d\x6c\x5f\x6c\x6f\x67\x6f\165\x74\137\x72\145\x6c\x61\x79\x5f\x73\164\141\x74\x65"]);
        $uz = new DOMDocument();
        $uz->loadXML($gp);
        $gp = $uz->firstChild;
        if (!($gp->localName == "\x4c\x6f\x67\x6f\165\164\x52\x65\x71\165\145\163\x74")) {
            goto WP;
        }
        $o9 = new SAML2_LogoutRequest($gp);
        $iB = get_site_option("\x6d\157\x5f\x73\141\x6d\x6c\137\163\x70\x5f\x65\x6e\x74\x69\x74\171\137\151\144");
        if (!empty($iB)) {
            goto CO;
        }
        $iB = $Wz . "\x2f\167\x70\x2d\x63\x6f\x6e\164\145\156\x74\x2f\160\154\165\147\151\x6e\163\57\155\151\156\151\x6f\162\x61\156\x67\145\55\163\x61\x6d\154\x2d\62\60\x2d\163\x69\156\x67\x6c\x65\x2d\163\151\147\156\x2d\x6f\156\57";
        CO:
        $LW = $wM;
        $kJ = Utilities::createLogoutResponse($o9->getId(), $iB, $LW, $vJ);
        if (empty($vJ) || $vJ == "\x48\x74\164\x70\122\x65\x64\151\x72\145\143\164") {
            goto oB;
        }
        if (!(get_site_option("\163\x61\155\x6c\x5f\162\x65\161\165\145\163\x74\137\163\x69\x67\x6e\145\144") == "\x75\156\143\150\x65\x63\x6b\x65\144")) {
            goto TG;
        }
        $Qv = base64_encode($kJ);
        SAMLSPUtilities::postSAMLResponse($wM, $Qv, $Hr);
        die;
        TG:
        $wZ = '';
        $z1 = '';
        $Qv = Utilities::signXML($kJ, "\123\164\x61\164\165\163");
        Utilities::postSAMLResponse($wM, $Qv, $Hr);
        goto uo;
        oB:
        $cJ = $wM;
        if (strpos($wM, "\77") !== false) {
            goto Il;
        }
        $cJ .= "\x3f";
        goto lP;
        Il:
        $cJ .= "\x26";
        lP:
        if (!(get_site_option("\163\141\x6d\154\x5f\x72\145\x71\165\145\163\x74\137\163\151\147\x6e\x65\x64") == "\x75\x6e\x63\x68\x65\143\x6b\x65\144")) {
            goto xV;
        }
        $cJ .= "\x53\101\x4d\x4c\122\145\x73\160\x6f\156\163\145\75" . $kJ . "\x26\122\145\x6c\141\171\123\164\141\x74\145\75" . urlencode($Hr);
        header("\x4c\x6f\x63\141\164\x69\x6f\x6e\x3a\40" . $cJ);
        die;
        xV:
        $cJ .= "\x53\x41\x4d\x4c\122\x65\x73\160\x6f\156\163\x65\x3d" . $kJ . "\x26\x52\x65\154\x61\171\123\164\x61\164\x65\x3d" . urlencode($Hr);
        header("\114\x6f\x63\141\164\151\157\156\72\x20" . $cJ);
        die;
        uo:
        WP:
    }
}
function mo_login_validate()
{
    if (!(isset($_REQUEST["\157\x70\164\x69\157\x6e"]) && $_REQUEST["\157\x70\x74\151\157\x6e"] == "\155\x6f\x73\x61\x6d\x6c\137\x6d\x65\164\x61\144\x61\x74\141")) {
        goto NJ;
    }
    miniorange_generate_metadata();
    NJ:
    if (!mo_saml_is_customer_license_verified()) {
        goto Kz;
    }
    if (!(isset($_REQUEST["\157\x70\164\x69\x6f\x6e"]) && $_REQUEST["\157\160\164\151\x6f\156"] == "\163\x61\155\x6c\137\165\x73\x65\x72\x5f\154\x6f\x67\151\156" || isset($_REQUEST["\157\x70\164\151\157\156"]) && $_REQUEST["\x6f\x70\x74\x69\x6f\156"] == "\164\x65\x73\164\103\x6f\x6e\146\x69\x67" || isset($_REQUEST["\x6f\160\164\151\x6f\x6e"]) && $_REQUEST["\x6f\x70\164\x69\x6f\156"] == "\147\145\x74\x73\141\x6d\154\x72\145\161\x75\x65\x73\x74" || isset($_REQUEST["\157\160\x74\151\157\156"]) && $_REQUEST["\x6f\160\164\151\157\156"] == "\x67\x65\164\x73\x61\x6d\154\162\145\x73\x70\x6f\156\x73\145")) {
        goto ne;
    }
    if (mo_saml_is_sp_configured()) {
        goto gR;
    }
    if (!is_user_logged_in()) {
        goto br;
    }
    if (!isset($_REQUEST["\162\145\144\151\162\145\143\164\137\164\157"])) {
        goto xR;
    }
    $uW = htmlspecialchars($_REQUEST["\162\x65\x64\x69\162\145\x63\x74\x5f\x74\157"]);
    header("\114\x6f\x63\141\x74\x69\157\156\x3a\x20" . $uW);
    die;
    xR:
    br:
    goto Ac;
    gR:
    if (!(is_user_logged_in() and $_REQUEST["\157\x70\x74\151\x6f\156"] == "\x73\x61\x6d\154\137\165\x73\x65\162\137\x6c\157\x67\x69\156")) {
        goto aX;
    }
    if (!isset($_REQUEST["\162\145\x64\x69\x72\x65\143\164\x5f\164\157"])) {
        goto AY;
    }
    $uW = htmlspecialchars($_REQUEST["\x72\145\144\151\x72\x65\x63\164\x5f\164\x6f"]);
    header("\114\x6f\143\141\164\151\157\x6e\x3a\x20" . $uW);
    die;
    AY:
    return;
    aX:
    $Wz = get_site_option("\155\x6f\137\163\141\x6d\154\137\x73\160\x5f\x62\x61\x73\145\137\165\162\x6c");
    if (!empty($Wz)) {
        goto mM;
    }
    $Wz = get_network_site_url();
    mM:
    $pK = get_site_option("\163\141\x6d\154\x5f\163\163\x6f\137\163\x65\164\x74\x69\156\x67\163");
    $NU = get_current_blog_id();
    $Lm = Utilities::get_active_sites();
    if (in_array($NU, $Lm)) {
        goto bU;
    }
    return;
    bU:
    if (!(empty($pK[$NU]) && !empty($pK["\104\x45\106\x41\x55\x4c\124"]))) {
        goto Xq;
    }
    $pK[$NU] = $pK["\104\105\x46\x41\125\114\x54"];
    Xq:
    if ($_REQUEST["\x6f\x70\164\x69\x6f\x6e"] == "\164\145\x73\164\x43\x6f\156\146\151\x67") {
        goto Y6;
    }
    if ($_REQUEST["\x6f\160\164\151\157\x6e"] == "\x67\x65\x74\x73\141\x6d\154\162\x65\161\165\x65\163\x74") {
        goto yV;
    }
    if ($_REQUEST["\x6f\x70\x74\x69\157\x6e"] == "\x67\145\164\x73\141\155\154\162\145\163\x70\157\x6e\x73\145") {
        goto xA;
    }
    if (!empty($pK[$NU]["\x6d\157\x5f\x73\x61\x6d\x6c\x5f\162\x65\x6c\x61\171\137\x73\164\141\x74\x65"])) {
        goto Z1;
    }
    if (isset($_REQUEST["\162\145\x64\151\162\x65\143\164\x5f\x74\157"])) {
        goto bw;
    }
    $rX = saml_get_current_page_url();
    goto In;
    bw:
    $rX = $_REQUEST["\x72\x65\144\x69\x72\145\143\164\x5f\x74\x6f"];
    In:
    goto cU;
    Z1:
    $rX = $pK[$NU]["\155\x6f\137\163\x61\155\154\137\x72\x65\x6c\141\171\137\163\x74\141\164\145"];
    cU:
    goto I8;
    xA:
    $rX = "\144\151\163\160\154\141\171\x53\x41\x4d\x4c\x52\x65\163\160\157\x6e\x73\x65";
    I8:
    goto t8;
    yV:
    $rX = "\x64\x69\x73\x70\x6c\141\171\123\x41\115\114\122\x65\x71\x75\145\x73\164";
    t8:
    goto MN;
    Y6:
    $rX = "\x74\145\163\x74\x56\x61\154\x69\x64\141\x74\x65";
    MN:
    $g0 = get_site_option("\x73\141\155\x6c\137\x6c\157\x67\151\x6e\x5f\x75\162\x6c");
    $tV = !empty(get_site_option("\x73\141\x6d\x6c\137\154\x6f\x67\x69\x6e\137\142\x69\x6e\144\x69\x6e\x67\x5f\x74\x79\x70\145")) ? get_site_option("\x73\x61\x6d\154\x5f\154\157\147\151\156\x5f\142\x69\x6e\x64\x69\x6e\147\x5f\164\171\x70\x65") : "\110\x74\x74\x70\120\157\163\164";
    $pK = get_site_option("\x73\141\x6d\154\x5f\163\x73\x6f\x5f\163\x65\164\x74\151\156\x67\163");
    $NU = get_current_blog_id();
    $Lm = Utilities::get_active_sites();
    if (in_array($NU, $Lm)) {
        goto r9;
    }
    return;
    r9:
    if (!(empty($pK[$NU]) && !empty($pK["\x44\105\106\x41\x55\114\124"]))) {
        goto ua;
    }
    $pK[$NU] = $pK["\104\x45\x46\101\x55\x4c\x54"];
    ua:
    $zC = isset($pK[$NU]["\155\157\x5f\x73\x61\155\x6c\x5f\146\x6f\x72\143\x65\x5f\x61\x75\164\x68\x65\156\x74\151\143\x61\x74\x69\157\x6e"]) ? $pK[$NU]["\x6d\157\137\163\x61\x6d\x6c\137\x66\157\x72\x63\x65\x5f\141\165\x74\150\x65\156\x74\151\143\x61\x74\x69\157\156"] : '';
    $sq = $Wz . "\57";
    $iB = get_site_option("\x6d\x6f\x5f\x73\141\x6d\154\137\163\x70\137\145\x6e\164\x69\x74\x79\x5f\151\144");
    $Xq = get_site_option("\x73\x61\155\154\x5f\156\141\x6d\x65\x69\x64\137\146\157\162\x6d\x61\x74");
    if (!empty($Xq)) {
        goto eg;
    }
    $Xq = "\61\x2e\61\x3a\156\x61\x6d\x65\x69\144\x2d\146\x6f\162\155\141\x74\72\x75\156\163\x70\145\143\x69\146\151\x65\144";
    eg:
    if (!empty($iB)) {
        goto GI;
    }
    $iB = $Wz . "\x2f\x77\x70\x2d\x63\x6f\x6e\164\145\x6e\x74\x2f\x70\x6c\x75\x67\x69\x6e\x73\57\x6d\151\x6e\x69\x6f\162\x61\156\x67\x65\55\163\x61\155\x6c\x2d\x32\60\55\163\151\156\147\x6c\x65\x2d\x73\151\x67\156\x2d\157\x6e\57";
    GI:
    $GW = Utilities::createAuthnRequest($sq, $iB, $g0, $zC, $tV, $Xq);
    if (!($rX == "\144\151\163\160\154\141\x79\x53\x41\115\x4c\x52\x65\x71\165\x65\x73\x74")) {
        goto Wv;
    }
    mo_saml_show_SAML_log(Utilities::createAuthnRequest($sq, $iB, $g0, $zC, "\110\164\x74\160\x50\x6f\x73\x74", $Xq), $rX);
    Wv:
    $cJ = $g0;
    if (strpos($g0, "\x3f") !== false) {
        goto GS;
    }
    $cJ .= "\77";
    goto yQ;
    GS:
    $cJ .= "\46";
    yQ:
    $rX = mo_saml_relaystate_url($rX);
    if ($tV == "\x48\164\164\160\122\145\144\151\162\x65\x63\x74") {
        goto QW;
    }
    if (!(get_site_option("\163\141\155\x6c\137\162\x65\161\x75\145\x73\164\x5f\x73\x69\147\x6e\x65\x64") == "\x75\x6e\143\150\145\143\153\145\144")) {
        goto LM;
    }
    $Qv = base64_encode($GW);
    Utilities::postSAMLRequest($g0, $Qv, $rX);
    die;
    LM:
    $wZ = '';
    $z1 = '';
    $Qv = Utilities::signXML($GW, "\116\x61\155\145\111\104\x50\x6f\154\151\143\171");
    Utilities::postSAMLRequest($g0, $Qv, $rX);
    goto Ue;
    QW:
    if (!(get_site_option("\x73\141\155\154\137\x72\x65\x71\x75\145\x73\x74\137\163\151\147\156\145\144") == "\165\x6e\143\150\x65\143\153\145\x64")) {
        goto H4;
    }
    $cJ .= "\123\x41\x4d\x4c\x52\145\x71\165\x65\163\164\75" . $GW . "\x26\122\x65\154\141\171\123\164\141\x74\x65\75" . urlencode($rX);
    header("\x4c\157\143\x61\x74\x69\157\x6e\x3a\x20" . $cJ);
    die;
    H4:
    $GW = "\123\101\115\x4c\122\145\161\x75\145\x73\164\x3d" . $GW . "\46\122\145\x6c\141\171\123\x74\x61\164\145\75" . urlencode($rX) . "\x26\x53\x69\x67\101\154\x67\75" . urlencode(XMLSecurityKey::RSA_SHA256);
    $nQ = array("\164\171\x70\x65" => "\160\162\151\x76\141\x74\145");
    $Z1 = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, $nQ);
    $od = get_site_option("\155\x6f\137\x73\x61\155\x6c\137\143\165\x72\x72\x65\156\164\x5f\x63\x65\162\164\x5f\160\162\151\166\x61\x74\145\137\153\x65\x79");
    $Z1->loadKey($od, FALSE);
    $b4 = new XMLSecurityDSig();
    $sD = $Z1->signData($GW);
    $sD = base64_encode($sD);
    $cJ .= $GW . "\46\x53\151\147\156\x61\164\165\162\145\x3d" . urlencode($sD);
    header("\114\x6f\143\141\x74\151\157\156\72\x20" . $cJ);
    die;
    Ue:
    Ac:
    ne:
    if (!(array_key_exists("\x53\101\x4d\x4c\x52\x65\x73\x70\x6f\156\163\x65", $_REQUEST) && !empty($_REQUEST["\123\101\x4d\114\x52\145\163\160\157\x6e\163\145"]))) {
        goto DV;
    }
    if (array_key_exists("\x52\x65\154\x61\171\x53\x74\141\164\145", $_POST) && !empty($_POST["\122\x65\x6c\x61\171\123\x74\141\x74\x65"]) && $_POST["\122\145\154\x61\171\123\164\141\x74\145"] != "\x2f") {
        goto U8;
    }
    $AM = '';
    goto uv;
    U8:
    $AM = $_POST["\122\145\154\x61\x79\123\164\141\x74\x65"];
    uv:
    $AM = mo_saml_parse_url($AM);
    $Wz = get_site_option("\x6d\x6f\137\163\141\x6d\x6c\x5f\163\160\x5f\x62\141\163\x65\x5f\165\162\154");
    if (!empty($Wz)) {
        goto yr;
    }
    $Wz = get_network_site_url();
    yr:
    $pB = $_REQUEST["\x53\101\115\x4c\x52\145\163\160\157\156\x73\145"];
    $pB = base64_decode($pB);
    if (!($AM == "\x64\x69\x73\160\x6c\x61\x79\123\x41\115\x4c\122\145\163\160\x6f\x6e\x73\x65")) {
        goto wp;
    }
    mo_saml_show_SAML_log($pB, $AM);
    wp:
    if (!(array_key_exists("\x53\101\x4d\x4c\122\145\x73\x70\157\156\163\145", $_GET) && !empty($_GET["\123\101\x4d\114\122\x65\x73\160\x6f\x6e\x73\x65"]))) {
        goto mJ;
    }
    $pB = gzinflate($pB);
    mJ:
    $uz = new DOMDocument();
    $uz->loadXML($pB);
    $Cr = $uz->firstChild;
    $li = $uz->documentElement;
    $vV = new DOMXpath($uz);
    $vV->registerNamespace("\x73\141\155\x6c\x70", "\165\162\x6e\x3a\x6f\141\163\151\x73\72\x6e\x61\x6d\x65\x73\72\164\143\x3a\x53\x41\115\x4c\x3a\62\56\60\72\x70\162\x6f\x74\x6f\143\157\x6c");
    $vV->registerNamespace("\163\x61\x6d\x6c", "\x75\x72\156\72\157\x61\x73\x69\163\72\156\141\155\x65\x73\72\x74\143\72\x53\101\115\x4c\72\x32\56\60\x3a\141\163\163\x65\x72\164\x69\157\156");
    if ($Cr->localName == "\x4c\x6f\x67\x6f\165\x74\x52\x65\163\160\x6f\x6e\x73\145") {
        goto ZO;
    }
    $yw = $vV->query("\57\163\x61\x6d\154\160\x3a\x52\x65\163\160\x6f\156\163\145\x2f\x73\x61\155\154\160\72\123\164\x61\164\x75\163\x2f\163\141\x6d\x6c\160\72\x53\164\x61\164\x75\163\x43\x6f\x64\145", $li);
    $d3 = isset($yw) ? $yw->item(0)->getAttribute("\x56\x61\x6c\x75\x65") : '';
    $hk = explode("\72", $d3);
    if (!array_key_exists(7, $hk)) {
        goto Az;
    }
    $yw = $hk[7];
    Az:
    $R7 = $vV->query("\57\x73\x61\155\154\160\x3a\122\x65\163\x70\x6f\156\x73\145\x2f\163\141\x6d\154\x70\72\123\x74\141\x74\x75\x73\57\163\141\x6d\x6c\160\72\x53\164\141\x74\x75\x73\x4d\145\163\x73\x61\x67\145", $li);
    $I3 = isset($R7) ? $R7->item(0) : '';
    if (empty($I3)) {
        goto uG;
    }
    $I3 = $I3->nodeValue;
    uG:
    if (array_key_exists("\x52\x65\154\141\171\x53\164\x61\x74\145", $_POST) && !empty($_POST["\x52\145\x6c\x61\171\x53\164\141\x74\145"]) && $_POST["\122\145\x6c\x61\171\123\164\141\x74\x65"] != "\x2f") {
        goto Am;
    }
    $AM = '';
    goto yo;
    Am:
    $AM = $_POST["\122\145\154\141\171\x53\x74\141\164\145"];
    $AM = mo_saml_parse_url($AM);
    yo:
    if (!($yw != "\x53\165\143\x63\145\163\x73")) {
        goto g1;
    }
    show_status_error($yw, $AM, $I3);
    g1:
    if (!($AM !== "\164\145\x73\x74\126\x61\154\151\144\141\164\145")) {
        goto ib;
    }
    $UX = parse_url($AM, PHP_URL_HOST);
    $OQ = parse_url($Wz, PHP_URL_HOST);
    $oG = parse_url(get_current_base_url(), PHP_URL_HOST);
    if (!empty($AM)) {
        goto tY;
    }
    $AM = "\57";
    goto Zj;
    tY:
    $AM = mo_saml_parse_url($AM);
    Zj:
    if (!(!empty($UX) && $UX != $oG)) {
        goto rs;
    }
    Utilities::postSAMLResponse($AM, $_REQUEST["\x53\101\x4d\x4c\x52\x65\163\160\157\x6e\x73\x65"], mo_saml_relaystate_url($AM));
    rs:
    ib:
    $H3 = maybe_unserialize(get_site_option("\x73\141\x6d\154\137\x78\x35\x30\x39\x5f\143\x65\x72\x74\151\x66\x69\x63\141\x74\x65"));
    update_site_option("\x6d\x6f\x5f\x73\x61\x6d\x6c\137\162\x65\163\160\x6f\x6e\x73\145", base64_encode($pB));
    foreach ($H3 as $Z1 => $zF) {
        if (@openssl_x509_read($zF)) {
            goto lv;
        }
        unset($H3[$Z1]);
        lv:
        YB:
    }
    l0:
    $sq = $Wz . "\x2f";
    $pB = new SAML2_Response($Cr);
    $Nz = $pB->getSignatureData();
    $A3 = current($pB->getAssertions())->getSignatureData();
    if (!(empty($A3) && empty($Nz))) {
        goto hq;
    }
    if ($AM == "\164\x65\163\164\x56\141\154\151\x64\x61\164\x65") {
        goto aC;
    }
    wp_die("\127\x65\x20\x63\x6f\x75\x6c\144\40\156\157\x74\x20\x73\x69\x67\156\40\171\x6f\x75\x20\x69\156\x2e\x20\120\x6c\145\x61\163\x65\40\143\157\x6e\164\141\143\x74\40\141\144\155\151\x6e\151\x73\164\x72\x61\164\157\x72", "\105\x72\162\157\162\x3a\40\111\x6e\x76\x61\154\x69\x64\x20\x53\101\115\114\x20\x52\145\163\x70\157\156\x73\145");
    goto mL;
    aC:
    $Y2 = mo_options_error_constants::Error_no_certificate;
    $id = mo_options_error_constants::Cause_no_certificate;
    echo "\x3c\x64\151\x76\x20\163\164\x79\154\x65\75\x22\x66\x6f\156\x74\55\x66\x61\155\151\x6c\x79\72\103\x61\x6c\x69\142\162\151\x3b\x70\x61\x64\144\151\x6e\147\72\60\x20\63\x25\73\x22\76\12\11\x9\x9\x9\x9\11\74\x64\151\x76\40\x73\x74\171\154\x65\x3d\42\x63\x6f\154\x6f\x72\72\x20\x23\141\x39\x34\64\64\62\73\142\141\143\153\147\x72\157\165\x6e\144\55\x63\157\x6c\x6f\162\x3a\40\x23\x66\62\144\145\x64\145\x3b\x70\141\x64\144\151\156\147\x3a\40\x31\65\x70\170\x3b\155\x61\162\x67\151\x6e\x2d\142\x6f\164\164\x6f\x6d\72\x20\x32\60\160\x78\73\x74\145\170\164\55\x61\154\x69\x67\x6e\72\x63\x65\x6e\x74\x65\162\x3b\142\157\162\144\x65\x72\x3a\x31\160\x78\40\x73\x6f\x6c\151\144\40\x23\105\x36\102\63\102\62\x3b\x66\x6f\x6e\x74\55\163\151\172\145\72\x31\x38\160\x74\x3b\42\76\x20\105\122\x52\117\x52\x3c\57\x64\x69\x76\x3e\xa\11\x9\x9\x9\11\11\74\144\x69\166\x20\163\x74\171\154\145\x3d\x22\143\157\x6c\x6f\162\72\40\x23\141\71\64\64\x34\62\73\x66\x6f\x6e\x74\x2d\163\x69\x7a\x65\72\61\x34\160\x74\73\40\155\141\162\147\x69\x6e\x2d\x62\157\x74\x74\x6f\x6d\x3a\x32\x30\160\170\x3b\x22\76\x3c\160\x3e\x3c\x73\x74\x72\x6f\x6e\147\x3e\105\162\x72\157\x72\40\x20\72" . $Y2 . "\x20\x3c\x2f\x73\164\162\x6f\x6e\147\76\74\x2f\160\76\12\11\11\11\x9\x9\x9\xa\11\x9\11\11\x9\x9\74\x70\x3e\74\x73\x74\162\157\156\x67\x3e\x50\x6f\163\x73\151\x62\x6c\x65\x20\x43\141\165\163\x65\72\40" . $id . "\74\57\163\x74\x72\x6f\x6e\147\76\74\57\x70\76\12\11\x9\x9\x9\11\x9\12\11\x9\11\11\x9\x9\x3c\57\144\x69\x76\x3e\74\57\144\x69\x76\76";
    mo_saml_download_logs($Y2, $id);
    die;
    mL:
    hq:
    $zh = '';
    if (is_array($H3)) {
        goto Sp;
    }
    $bd = XMLSecurityKey::getRawThumbprint($H3);
    $bd = mo_saml_convert_to_windows_iconv($bd);
    $bd = preg_replace("\57\x5c\163\x2b\57", '', $bd);
    if (empty($Nz)) {
        goto m3;
    }
    $zh = Utilities::processResponse($sq, $bd, $Nz, $pB, 0, $AM);
    m3:
    if (empty($A3)) {
        goto Wk;
    }
    $zh = Utilities::processResponse($sq, $bd, $A3, $pB, 0, $AM);
    Wk:
    goto pf;
    Sp:
    foreach ($H3 as $Z1 => $zF) {
        $bd = XMLSecurityKey::getRawThumbprint($zF);
        $bd = mo_saml_convert_to_windows_iconv($bd);
        $bd = preg_replace("\x2f\134\163\53\57", '', $bd);
        if (empty($Nz)) {
            goto rV;
        }
        $zh = Utilities::processResponse($sq, $bd, $Nz, $pB, $Z1, $AM);
        rV:
        if (empty($A3)) {
            goto ef;
        }
        $zh = Utilities::processResponse($sq, $bd, $A3, $pB, $Z1, $AM);
        ef:
        if (!$zh) {
            goto Gb;
        }
        goto o7;
        Gb:
        CR:
    }
    o7:
    pf:
    if (empty($Nz)) {
        goto TS;
    }
    $AB = $Nz["\x43\145\162\x74\151\x66\151\x63\x61\164\x65\x73"][0];
    goto DQ;
    TS:
    $AB = $A3["\x43\145\x72\x74\x69\146\x69\x63\141\164\145\163"][0];
    DQ:
    if ($zh) {
        goto Mb;
    }
    if ($AM == "\164\145\x73\x74\126\141\154\151\x64\141\x74\145") {
        goto Qy;
    }
    wp_die("\127\145\x20\x63\x6f\165\x6c\x64\x20\x6e\157\164\x20\x73\x69\147\x6e\x20\171\x6f\165\40\151\156\x2e\40\120\x6c\x65\x61\x73\145\40\143\157\x6e\164\141\x63\164\x20\171\x6f\165\x72\x20\x41\x64\x6d\151\156\x69\163\164\162\141\164\x6f\162", "\105\162\x72\157\162\40\72\103\x65\x72\x74\x69\x66\151\143\141\x74\145\40\x6e\157\164\x20\146\157\165\156\144");
    goto qu;
    Qy:
    $Y2 = mo_options_error_constants::Error_wrong_certificate;
    $id = mo_options_error_constants::Cause_wrong_certificate;
    $vc = "\55\x2d\x2d\x2d\55\102\105\x47\x49\x4e\40\x43\x45\122\124\x49\106\111\103\101\x54\x45\55\x2d\x2d\x2d\x2d\74\142\x72\76" . chunk_split($AB, 64) . "\x3c\142\162\x3e\x2d\x2d\55\x2d\55\x45\x4e\x44\40\103\x45\x52\124\111\x46\x49\x43\101\x54\x45\x2d\x2d\x2d\x2d\x2d";
    echo "\74\144\151\166\40\x73\x74\171\x6c\145\x3d\x22\146\x6f\x6e\x74\x2d\x66\x61\x6d\151\x6c\171\72\103\141\154\x69\x62\x72\x69\73\160\141\144\x64\151\x6e\x67\x3a\60\40\x33\45\73\42\76";
    echo "\74\x64\151\x76\40\x73\x74\171\x6c\x65\75\42\143\157\x6c\x6f\x72\72\40\x23\141\71\64\x34\x34\62\x3b\142\141\x63\153\147\x72\x6f\x75\156\x64\x2d\x63\157\154\157\x72\x3a\40\x23\x66\62\144\x65\x64\145\x3b\x70\141\x64\x64\x69\x6e\147\x3a\40\x31\x35\160\x78\73\155\141\x72\147\x69\x6e\55\142\157\x74\164\x6f\x6d\x3a\x20\x32\60\x70\170\x3b\164\145\170\164\x2d\x61\x6c\151\147\156\x3a\143\145\x6e\164\145\162\x3b\x62\x6f\162\144\145\162\72\61\160\x78\40\x73\157\154\151\144\x20\x23\x45\x36\102\63\x42\x32\x3b\x66\x6f\x6e\164\55\163\x69\172\x65\x3a\x31\70\160\x74\x3b\42\76\40\x45\122\x52\117\x52\74\x2f\x64\x69\166\76\12\11\x9\11\11\x9\11\x9\x9\74\144\x69\x76\40\x73\x74\x79\x6c\145\x3d\x22\143\157\154\x6f\x72\x3a\40\x23\141\x39\64\x34\64\x32\x3b\x66\x6f\x6e\x74\55\163\x69\172\x65\x3a\x31\64\160\164\x3b\40\x6d\141\162\x67\x69\x6e\55\142\x6f\164\164\157\155\72\62\x30\160\x78\73\x22\76\74\x70\76\74\163\x74\162\x6f\156\x67\76\x45\162\x72\x6f\x72\x3a\40\74\57\163\164\x72\157\x6e\x67\76\125\156\141\142\x6c\145\x20\x74\157\x20\x66\151\x6e\144\x20\x61\x20\143\145\x72\x74\x69\146\151\x63\x61\x74\145\x20\x6d\x61\164\x63\150\151\156\x67\x20\x74\x68\145\x20\143\157\156\146\x69\x67\165\162\x65\x64\x20\x66\151\x6e\x67\x65\162\x70\162\x69\156\164\56\x3c\x2f\x70\76\12\x9\x9\11\x9\x9\11\x9\11\11\74\x70\76\x50\154\x65\x61\163\x65\40\x63\x6f\156\164\141\x63\x74\x20\x79\x6f\165\162\x20\x61\x64\x6d\151\x6e\x69\x73\164\162\141\164\x6f\162\x20\141\156\144\x20\162\x65\x70\157\x72\x74\x20\164\150\x65\40\146\x6f\154\x6c\x6f\x77\151\x6e\x67\40\145\x72\162\x6f\162\x3a\74\x2f\160\x3e\12\11\x9\11\x9\x9\x9\11\11\11\x3c\x70\x3e\x3c\x73\164\x72\157\156\147\76\120\157\163\163\x69\142\x6c\145\x20\x43\x61\165\163\x65\72\x20\x3c\57\163\x74\x72\x6f\x6e\147\76\x27\130\56\65\60\71\x20\103\x65\x72\x74\x69\x66\x69\x63\141\164\x65\x27\40\x66\x69\x65\x6c\144\40\x69\x6e\x20\160\x6c\165\x67\x69\x6e\40\144\x6f\145\x73\x20\156\157\164\40\x6d\141\164\x63\150\40\x74\150\145\x20\143\145\x72\x74\x69\x66\x69\143\141\164\x65\40\x66\x6f\x75\156\x64\x20\151\156\x20\x53\x41\x4d\x4c\40\x52\x65\163\160\157\x6e\x73\145\x2e\74\57\x70\x3e\xa\11\11\11\x9\11\11\11\x9\11\74\x70\x3e\74\x73\164\x72\157\x6e\147\x3e\103\145\x72\164\151\146\x69\143\x61\x74\x65\40\146\x6f\165\x6e\144\40\x69\x6e\40\x53\x41\115\x4c\x20\122\145\163\160\157\x6e\x73\x65\x3a\40\x3c\x2f\163\164\x72\x6f\x6e\x67\x3e\x3c\x66\x6f\x6e\164\x20\146\x61\143\x65\x3d\42\x43\157\x75\x72\151\x65\x72\x20\116\x65\167\42\x3e\74\142\x72\x3e\74\x62\162\x3e" . $vc . "\74\x2f\x70\x3e\74\x2f\x66\157\156\x74\x3e\12\11\x9\11\x9\11\x9\x9\11\11\x3c\160\76\74\163\x74\162\157\x6e\147\76\123\x6f\154\x75\x74\x69\157\x6e\72\x20\x3c\x2f\163\164\x72\157\156\x67\76\x3c\x2f\x70\x3e\xa\11\x9\x9\11\x9\11\x9\x9\11\74\157\154\76\xa\11\x9\x9\x9\11\11\x9\11\11\x20\x20\x20\x3c\x6c\x69\x3e\103\157\x70\171\x20\160\x61\x73\164\x65\x20\x74\x68\145\x20\143\x65\x72\164\x69\x66\x69\143\x61\x74\x65\40\160\x72\157\166\x69\144\145\x64\40\x61\142\157\x76\x65\x20\151\156\x20\x58\65\60\x39\x20\103\145\162\164\x69\146\151\143\x61\164\145\x20\x75\156\144\x65\162\x20\x53\145\x72\x76\x69\143\x65\40\120\x72\157\166\x69\144\145\x72\x20\123\145\164\x75\x70\40\164\x61\142\56\x3c\57\154\x69\x3e\xa\11\11\x9\x9\x9\x9\x9\11\x9\x20\40\40\x3c\154\x69\x3e\111\146\40\x69\x73\163\165\x65\x20\x70\x65\162\163\151\163\x74\163\40\x64\151\x73\x61\x62\154\145\x20\74\142\x3e\x43\150\x61\x72\x61\x63\x74\x65\162\x20\x65\156\x63\x6f\144\x69\x6e\x67\x3c\x2f\142\76\40\x75\x6e\144\x65\x72\40\123\145\x72\x76\151\x63\145\x20\120\162\157\x76\x64\x65\162\40\x53\145\164\165\x70\40\x74\x61\142\56\74\57\154\151\76\12\x9\11\11\x9\11\11\x9\x9\11\x3c\x2f\x6f\x6c\76\12\x9\11\x9\x9\11\11\11\x9\11\74\57\144\x69\x76\x3e\12\11\11\x9\11\11\x9\x9\x9\x3c\144\x69\x76\x20\163\x74\x79\154\x65\75\x22\x6d\x61\162\147\x69\156\72\x33\x25\73\x64\151\163\160\x6c\141\171\x3a\x62\154\157\x63\153\x3b\x74\x65\170\x74\55\141\x6c\x69\x67\x6e\72\x63\145\x6e\x74\x65\x72\x3b\42\76\12\11\x9\11\11\11\x9\x9\11\11\x9\x3c\x64\x69\x76\40\x73\164\171\x6c\145\x3d\x22\x6d\141\162\147\151\x6e\72\63\x25\x3b\144\x69\x73\x70\154\x61\x79\x3a\x62\x6c\157\x63\153\73\164\145\x78\164\55\141\154\x69\x67\156\72\x63\x65\156\164\145\162\73\x22\76\x3c\151\x6e\160\165\x74\x20\163\164\x79\154\x65\75\42\160\141\x64\144\x69\x6e\x67\x3a\61\45\x3b\x77\x69\144\164\x68\72\x31\x30\x30\160\170\x3b\x62\x61\143\x6b\147\162\157\165\x6e\144\x3a\x20\x23\60\60\71\x31\103\104\x20\156\157\156\145\40\x72\145\x70\x65\141\x74\40\163\143\162\157\x6c\x6c\40\60\45\40\60\45\73\143\165\x72\163\157\x72\72\40\x70\x6f\151\156\x74\145\x72\73\146\157\x6e\x74\55\163\x69\x7a\x65\72\61\65\160\170\x3b\x62\157\x72\x64\x65\162\x2d\167\151\x64\164\150\x3a\40\x31\160\x78\73\x62\157\x72\x64\x65\x72\55\163\164\171\154\145\72\40\163\x6f\x6c\151\x64\x3b\142\x6f\x72\x64\145\162\55\x72\x61\144\151\x75\163\x3a\x20\x33\x70\x78\73\167\x68\x69\x74\145\x2d\163\160\x61\x63\x65\72\x20\156\x6f\167\162\141\160\x3b\142\157\170\55\x73\151\172\x69\156\147\x3a\x20\142\x6f\162\x64\145\162\55\x62\157\170\73\142\157\162\x64\145\x72\x2d\x63\157\154\x6f\x72\72\x20\x23\x30\60\x37\x33\101\101\x3b\x62\157\x78\x2d\x73\150\x61\x64\157\x77\x3a\40\x30\160\170\40\61\x70\170\40\60\160\170\40\x72\147\x62\x61\50\61\x32\x30\54\x20\62\60\x30\x2c\40\62\63\60\x2c\x20\x30\56\x36\51\x20\x69\156\163\145\x74\73\x63\157\154\x6f\162\x3a\40\x23\x46\106\106\73\x22\x74\x79\160\145\x3d\42\142\x75\x74\164\x6f\x6e\42\x20\x76\x61\x6c\165\145\75\x22\x44\157\156\x65\42\x20\157\x6e\103\x6c\x69\x63\153\75\x22\163\x65\154\x66\x2e\143\154\x6f\x73\145\x28\51\73\x22\76\x3c\57\144\x69\x76\76";
    mo_saml_download_logs($Y2, $id);
    die;
    qu:
    Mb:
    $OS = get_site_option("\163\141\x6d\x6c\137\151\163\163\165\x65\x72");
    $iB = get_site_option("\x6d\157\137\163\x61\155\x6c\x5f\163\160\x5f\145\x6e\164\x69\164\x79\x5f\151\144");
    if (!empty($iB)) {
        goto Vw;
    }
    $iB = $Wz . "\x2f\x77\x70\x2d\143\157\x6e\x74\145\x6e\164\57\160\x6c\x75\x67\x69\x6e\x73\57\x6d\151\x6e\x69\157\x72\141\x6e\147\145\x2d\x73\141\x6d\x6c\55\x32\x30\55\x73\151\156\147\x6c\145\55\x73\x69\147\x6e\x2d\157\156\x2f";
    Vw:
    Utilities::validateIssuerAndAudience($pB, $iB, $OS, $AM);
    $Y3 = current(current($pB->getAssertions())->getNameId());
    $HO = current($pB->getAssertions())->getAttributes();
    $HO["\116\141\x6d\x65\111\104"] = array("\x30" => $Y3);
    $Kw = current($pB->getAssertions())->getSessionIndex();
    mo_saml_checkMapping($HO, $AM, $Kw);
    goto TC;
    ZO:
    if (!isset($_REQUEST["\x52\145\154\141\x79\123\x74\x61\164\x65"])) {
        goto au;
    }
    $Hr = $_REQUEST["\122\145\x6c\x61\x79\x53\x74\141\x74\x65"];
    au:
    wp_logout();
    if (empty($Hr)) {
        goto xS;
    }
    $Hr = mo_saml_parse_url($Hr);
    goto y7;
    xS:
    $Hr = $Wz;
    y7:
    header("\x4c\x6f\x63\x61\x74\151\x6f\156\x3a" . $Hr);
    die;
    TC:
    DV:
    if (!(array_key_exists("\123\101\115\x4c\122\145\161\165\145\x73\164", $_REQUEST) && !empty($_REQUEST["\123\x41\115\114\122\x65\x71\165\145\x73\x74"]))) {
        goto Fh;
    }
    $GW = $_REQUEST["\x53\101\115\114\122\x65\x71\165\145\x73\164"];
    $AM = "\x2f";
    if (!array_key_exists("\x52\145\x6c\141\x79\123\164\141\x74\145", $_REQUEST)) {
        goto e1;
    }
    $AM = $_REQUEST["\x52\145\154\x61\171\123\164\141\164\x65"];
    e1:
    $GW = base64_decode($GW);
    if (!(array_key_exists("\123\101\x4d\114\122\145\x71\x75\x65\163\x74", $_GET) && !empty($_GET["\x53\x41\x4d\x4c\122\x65\x71\x75\145\x73\164"]))) {
        goto Ae;
    }
    $GW = gzinflate($GW);
    Ae:
    $uz = new DOMDocument();
    $uz->loadXML($GW);
    $iY = $uz->firstChild;
    if (!($iY->localName == "\x4c\157\147\157\x75\x74\122\x65\161\165\x65\x73\164")) {
        goto e0;
    }
    $o9 = new SAML2_LogoutRequest($iY);
    if (!(!session_id() || session_id() == '' || !isset($_SESSION))) {
        goto hf;
    }
    session_start();
    hf:
    $_SESSION["\155\157\137\x73\x61\x6d\x6c\x5f\154\x6f\147\x6f\165\164\137\x72\145\161\165\x65\163\164"] = $GW;
    $_SESSION["\x6d\x6f\x5f\x73\141\155\154\137\154\x6f\147\157\165\164\x5f\x72\x65\154\x61\171\137\163\x74\141\164\145"] = $AM;
    wp_redirect(htmlspecialchars_decode(wp_logout_url()));
    die;
    e0:
    Fh:
    if (!(isset($_REQUEST["\x6f\160\164\x69\x6f\156"]) and !is_array($_REQUEST["\157\x70\164\x69\x6f\156"]) and strpos($_REQUEST["\157\x70\x74\x69\157\156"], "\162\145\141\144\x73\x61\x6d\154\154\157\147\x69\x6e") !== false)) {
        goto bP;
    }
    require_once dirname(__FILE__) . "\x2f\151\x6e\x63\x6c\x75\144\x65\163\57\154\151\142\x2f\145\x6e\143\162\171\x70\164\151\x6f\156\x2e\x70\150\160";
    if (isset($_POST["\x53\124\x41\124\x55\123"]) && $_POST["\123\x54\x41\x54\125\123"] == "\105\x52\x52\x4f\x52") {
        goto pS;
    }
    if (!(isset($_POST["\x53\124\101\124\125\123"]) && $_POST["\x53\x54\x41\124\x55\x53"] == "\123\125\103\x43\105\123\123")) {
        goto sa;
    }
    $dI = '';
    if (!(isset($_REQUEST["\162\145\144\x69\x72\x65\143\164\x5f\164\157"]) && !empty($_REQUEST["\x72\x65\144\151\162\x65\x63\164\x5f\x74\157"]) && $_REQUEST["\x72\x65\x64\x69\162\x65\x63\164\x5f\164\157"] != "\57")) {
        goto pT;
    }
    $dI = $_REQUEST["\x72\x65\144\x69\162\x65\143\x74\x5f\164\157"];
    pT:
    delete_site_option("\x6d\x6f\137\163\141\x6d\x6c\137\x72\x65\x64\x69\162\x65\x63\x74\137\x65\x72\162\x6f\162\x5f\x63\157\144\x65");
    delete_site_option("\155\157\x5f\163\141\x6d\154\x5f\162\145\144\151\162\145\143\x74\137\145\162\162\157\162\x5f\162\145\141\x73\157\156");
    try {
        $Ei = get_site_option("\x73\x61\155\154\x5f\141\x6d\137\145\x6d\x61\151\x6c");
        $Q7 = get_site_option("\163\x61\x6d\x6c\x5f\x61\x6d\x5f\x75\x73\145\x72\x6e\141\x6d\145");
        $ks = get_site_option("\x73\141\155\154\x5f\x61\x6d\137\146\x69\x72\x73\164\137\156\x61\x6d\145");
        $Ow = get_site_option("\x73\141\x6d\154\137\x61\155\x5f\x6c\x61\163\x74\137\x6e\x61\x6d\145");
        $NF = get_site_option("\163\x61\155\154\137\141\x6d\137\147\x72\x6f\x75\x70\x5f\x6e\141\x6d\145");
        $N1 = get_site_option("\163\141\x6d\x6c\137\141\155\x5f\144\145\x66\x61\165\154\x74\x5f\165\163\x65\x72\x5f\162\157\154\x65");
        $HR = get_site_option("\x73\x61\155\154\x5f\x61\155\137\x64\x6f\x6e\x74\137\141\154\154\157\x77\137\x75\156\x6c\151\163\x74\x65\x64\x5f\165\163\x65\162\x5f\x72\x6f\154\145");
        $Tq = get_site_option("\x73\141\155\x6c\x5f\141\155\137\x61\x63\143\x6f\165\x6e\x74\137\x6d\141\x74\143\150\145\162");
        $eq = '';
        $qy = '';
        $ks = str_replace("\56", "\x5f", $ks);
        $ks = str_replace("\40", "\137", $ks);
        if (!(!empty($ks) && array_key_exists($ks, $_POST))) {
            goto oW;
        }
        $ks = $_POST[$ks];
        oW:
        $Ow = str_replace("\56", "\x5f", $Ow);
        $Ow = str_replace("\40", "\x5f", $Ow);
        if (!(!empty($Ow) && array_key_exists($Ow, $_POST))) {
            goto yp;
        }
        $Ow = $_POST[$Ow];
        yp:
        $Q7 = str_replace("\56", "\137", $Q7);
        $Q7 = str_replace("\40", "\137", $Q7);
        if (!empty($Q7) && array_key_exists($Q7, $_POST)) {
            goto T0;
        }
        $qy = $_POST["\x4e\141\155\x65\x49\x44"];
        goto k_;
        T0:
        $qy = $_POST[$Q7];
        k_:
        $eq = str_replace("\56", "\x5f", $Ei);
        $eq = str_replace("\x20", "\137", $Ei);
        if (!empty($Ei) && array_key_exists($Ei, $_POST)) {
            goto D3;
        }
        $eq = $_POST["\x4e\x61\155\145\111\x44"];
        goto jP;
        D3:
        $eq = $_POST[$Ei];
        jP:
        $NF = str_replace("\56", "\137", $NF);
        $NF = str_replace("\40", "\137", $NF);
        if (!(!empty($NF) && array_key_exists($NF, $_POST))) {
            goto P8;
        }
        $NF = $_POST[$NF];
        P8:
        if (!empty($Tq)) {
            goto eS;
        }
        $Tq = "\145\x6d\x61\x69\x6c";
        eS:
        $Z1 = get_site_option("\155\x6f\137\x73\x61\x6d\x6c\x5f\143\165\x73\x74\157\155\x65\x72\x5f\x74\157\153\x65\x6e");
        if (!(isset($Z1) || trim($Z1) != '')) {
            goto L0;
        }
        $SK = AESEncryption::decrypt_data($eq, $Z1);
        $eq = $SK;
        L0:
        if (!(!empty($ks) && !empty($Z1))) {
            goto ax;
        }
        $Og = AESEncryption::decrypt_data($ks, $Z1);
        $ks = $Og;
        ax:
        if (!(!empty($Ow) && !empty($Z1))) {
            goto C8;
        }
        $uo = AESEncryption::decrypt_data($Ow, $Z1);
        $Ow = $uo;
        C8:
        if (!(!empty($qy) && !empty($Z1))) {
            goto Jv;
        }
        $tx = AESEncryption::decrypt_data($qy, $Z1);
        $qy = $tx;
        Jv:
        if (!(!empty($NF) && !empty($Z1))) {
            goto x9;
        }
        $m7 = AESEncryption::decrypt_data($NF, $Z1);
        $NF = $m7;
        x9:
    } catch (Exception $ZE) {
        echo sprintf("\101\156\40\x65\x72\x72\x6f\162\40\157\x63\x63\x75\x72\162\145\144\40\x77\150\x69\154\145\40\x70\x72\x6f\143\x65\163\x73\151\156\x67\40\164\150\x65\40\x53\x41\115\114\40\122\x65\163\160\157\156\x73\145\56");
        die;
    }
    $Eg = array($NF);
    mo_saml_login_user($eq, $ks, $Ow, $qy, $Eg, $HR, $N1, $dI, $Tq);
    sa:
    goto jD;
    pS:
    update_site_option("\155\157\137\163\141\155\154\x5f\162\145\x64\151\162\x65\143\164\137\145\162\x72\x6f\162\x5f\x63\x6f\x64\x65", $_POST["\x45\x52\122\117\x52\x5f\122\x45\101\123\117\116"]);
    update_site_option("\x6d\x6f\137\163\141\x6d\154\137\x72\145\x64\x69\x72\x65\143\164\x5f\145\162\162\157\x72\x5f\162\145\141\x73\x6f\x6e", $_POST["\105\122\x52\x4f\122\137\115\x45\x53\123\101\x47\105"]);
    jD:
    bP:
    Kz:
}
function mo_saml_relaystate_url($AM)
{
    $zk = parse_url($AM, PHP_URL_SCHEME);
    $AM = str_replace($zk . "\72\57\x2f", '', $AM);
    return $AM;
}
function mo_saml_hash_relaystate($AM)
{
    $zk = parse_url($AM, PHP_URL_SCHEME);
    $AM = str_replace($zk . "\72\x2f\x2f", '', $AM);
    $AM = base64_encode($AM);
    $Fq = cdjsurkhh($AM);
    $AM = $AM . "\x2e" . $Fq;
    return $AM;
}
function mo_saml_get_relaystate($AM)
{
    if (!filter_var($AM, FILTER_VALIDATE_URL)) {
        goto MV;
    }
    return $AM;
    MV:
    $lc = strpos($AM, "\56");
    if ($lc) {
        goto iL;
    }
    wp_die("\x41\x6e\40\145\x72\162\x6f\162\40\x6f\143\x63\165\x72\x65\x64\x2e\x20\120\154\145\141\x73\145\40\x63\157\x6e\164\x61\143\x74\40\171\157\x75\x72\40\x61\x64\x6d\151\156\151\x73\x74\162\x61\x74\x6f\x72\56", "\x45\162\x72\157\162\40\x3a\40\x4e\x6f\164\40\141\x20\x74\162\165\163\164\x65\144\40\x73\157\x75\x72\x63\145\x20\157\x66\x20\164\x68\145\x20\123\x41\x4d\x4c\40\162\x65\163\160\x6f\156\163\145");
    die;
    iL:
    $Hr = substr($AM, 0, $lc);
    $To = substr($AM, $lc + 1);
    $na = cdjsurkhh($Hr);
    if (!($To !== $na)) {
        goto cp;
    }
    wp_die("\x41\x6e\x20\145\x72\162\157\162\x20\x6f\143\x63\165\x72\145\x64\x2e\40\120\x6c\x65\x61\x73\x65\40\x63\157\x6e\x74\x61\143\164\40\x79\x6f\x75\x72\x20\x61\x64\x6d\x69\x6e\x69\x73\164\162\141\x74\x6f\x72\56", "\x45\162\x72\x6f\162\40\72\40\x4e\157\164\x20\141\40\x74\x72\x75\x73\x74\145\144\40\163\x6f\x75\x72\x63\145\40\x6f\x66\40\x74\150\x65\40\x53\x41\x4d\x4c\40\x72\145\163\x70\157\x6e\163\x65");
    die;
    cp:
    $Hr = base64_decode($Hr);
    return $Hr;
}
function cdjsurkhh($uR)
{
    $Fq = hash("\x73\150\x61\65\x31\62", $uR);
    $UK = substr($Fq, 7, 14);
    return $UK;
}
function mo_saml_parse_url($AM)
{
    if (!($AM != "\164\x65\x73\164\126\141\154\151\x64\141\164\145")) {
        goto VW;
    }
    $Wz = get_site_option("\x6d\x6f\137\x73\141\x6d\154\137\163\160\137\x62\x61\163\x65\137\165\162\154");
    if (!empty($Wz)) {
        goto sy;
    }
    $Wz = get_network_site_url();
    sy:
    $zk = parse_url($Wz, PHP_URL_SCHEME);
    if (filter_var($AM, FILTER_VALIDATE_URL)) {
        goto zn;
    }
    $AM = $zk . "\72\x2f\57" . $AM;
    zn:
    VW:
    return $AM;
}
function mo_saml_is_subsite($AM)
{
    $lF = parse_url($AM, PHP_URL_HOST);
    $dT = parse_url($AM, PHP_URL_PATH);
    if (is_subdomain_install()) {
        goto vM;
    }
    $jL = strpos($dT, "\x2f", 1) != false ? strpos($dT, "\57", 1) : strlen($dT) - 1;
    $dT = substr($dT, 0, $jL + 1);
    $blog_id = get_blog_id_from_url($lF, $dT);
    goto xs;
    vM:
    $blog_id = get_blog_id_from_url($lF);
    xs:
    if ($blog_id !== 0) {
        goto U2;
    }
    return false;
    goto sp;
    U2:
    return true;
    sp:
}
function mo_saml_show_SAML_log($iY, $Uj)
{
    header("\103\x6f\156\164\145\x6e\x74\x2d\124\171\160\145\72\x20\164\145\x78\x74\x2f\x68\164\x6d\154");
    $li = new DOMDocument();
    $li->preserveWhiteSpace = false;
    $li->formatOutput = true;
    $li->loadXML($iY);
    if ($Uj == "\x64\x69\x73\x70\x6c\141\171\123\101\x4d\114\x52\x65\x71\x75\145\x73\164") {
        goto KR;
    }
    $Wt = "\x53\x41\x4d\x4c\40\x52\145\163\x70\157\x6e\x73\145";
    goto LX;
    KR:
    $Wt = "\x53\101\115\x4c\x20\122\x65\x71\165\145\163\x74";
    LX:
    $Ik = $li->saveXML();
    $VL = htmlentities($Ik);
    $VL = rtrim($VL);
    $nb = simplexml_load_string($Ik);
    $wB = json_encode($nb);
    $Oa = json_decode($wB);
    $Oy = plugins_url("\151\156\x63\154\x75\x64\x65\163\57\x63\x73\163\57\163\x74\171\154\145\137\163\x65\x74\x74\x69\x6e\147\163\56\143\163\x73\x3f\x76\145\x72\x3d\x34\56\x38\56\x34\60", __FILE__);
    echo "\74\154\151\156\x6b\40\162\x65\x6c\75\x27\163\x74\x79\x6c\x65\x73\150\x65\145\x74\47\40\x69\x64\x3d\x27\155\157\137\x73\x61\155\x6c\137\x61\144\x6d\151\156\x5f\163\145\x74\x74\151\x6e\147\x73\137\163\164\171\154\x65\x2d\143\163\163\47\40\40\x68\x72\x65\146\x3d\47" . $Oy . "\x27\x20\164\x79\160\145\x3d\47\x74\x65\170\164\57\x63\x73\x73\47\40\x6d\x65\x64\x69\141\75\47\141\x6c\x6c\x27\x20\57\x3e\12\x20\x20\x20\x20\40\40\x20\x20\x20\40\x20\40\12\x9\x9\11\x3c\144\151\x76\x20\x63\154\141\163\x73\75\42\x6d\157\x2d\144\151\163\x70\154\x61\171\55\154\157\147\163\x22\x20\76\74\160\x20\164\171\160\x65\x3d\42\164\145\x78\164\x22\x20\x20\40\151\144\75\x22\123\101\x4d\x4c\137\164\x79\160\x65\42\x3e" . $Wt . "\74\x2f\160\x3e\74\x2f\144\151\166\x3e\xa\11\x9\11\11\12\x9\x9\x9\x3c\144\x69\x76\x20\x74\x79\x70\145\x3d\42\x74\x65\170\164\42\x20\151\x64\75\42\123\101\115\114\137\x64\x69\163\160\x6c\x61\171\x22\40\x63\x6c\141\x73\163\75\42\x6d\157\55\x64\x69\163\x70\154\x61\x79\55\142\x6c\157\x63\x6b\42\x3e\x3c\160\x72\145\x20\143\154\141\x73\163\x3d\x27\142\x72\165\x73\x68\x3a\x20\x78\155\x6c\73\x27\76" . $VL . "\x3c\x2f\x70\x72\145\x3e\74\57\144\x69\166\76\12\x9\11\x9\74\142\162\x3e\xa\x9\11\x9\x3c\144\x69\166\11\x20\163\x74\171\154\145\x3d\42\x6d\x61\162\147\x69\x6e\72\x33\x25\73\144\151\x73\160\x6c\141\x79\72\x62\154\157\x63\153\73\x74\145\x78\164\x2d\141\x6c\151\x67\156\x3a\x63\145\156\164\145\162\x3b\x22\x3e\12\40\x20\40\40\x20\x20\x20\x20\x20\x20\x20\x20\12\11\x9\11\x3c\x64\x69\166\x20\x73\x74\171\x6c\145\x3d\42\155\x61\162\147\x69\156\x3a\63\x25\x3b\144\x69\163\160\x6c\x61\171\x3a\142\x6c\x6f\x63\x6b\x3b\164\x65\170\x74\55\141\154\x69\x67\156\72\143\x65\x6e\x74\x65\x72\x3b\x22\x20\x3e\12\x9\12\40\40\x20\x20\40\40\40\x20\40\x20\x20\x20\74\x2f\x64\151\x76\x3e\12\11\11\x9\74\142\x75\164\x74\157\156\40\x69\144\75\42\143\157\160\171\x22\40\157\x6e\143\x6c\151\143\153\75\42\143\x6f\x70\x79\104\151\x76\x54\157\x43\154\x69\160\x62\157\141\162\144\x28\51\42\x20\x20\163\x74\x79\x6c\145\x3d\x22\160\x61\144\144\151\x6e\147\x3a\x31\x25\73\167\151\144\164\x68\x3a\61\60\60\160\x78\73\142\141\143\x6b\x67\x72\157\165\156\x64\72\x20\x23\x30\60\71\x31\x43\104\x20\x6e\x6f\156\x65\40\162\145\x70\145\141\164\40\x73\x63\162\x6f\154\x6c\x20\60\x25\x20\x30\45\x3b\143\x75\162\x73\157\x72\x3a\40\160\x6f\151\156\x74\x65\162\x3b\146\157\x6e\164\55\x73\151\x7a\145\72\61\65\160\x78\73\142\157\x72\x64\145\x72\x2d\x77\x69\x64\164\150\x3a\40\x31\x70\x78\x3b\x62\157\162\x64\145\162\x2d\x73\x74\171\x6c\145\72\x20\x73\x6f\154\151\x64\x3b\142\157\x72\144\145\162\x2d\162\x61\x64\151\165\163\72\x20\x33\x70\x78\x3b\167\150\151\x74\x65\55\x73\160\x61\x63\x65\x3a\x20\x6e\157\167\x72\141\160\x3b\142\x6f\170\55\x73\151\172\x69\156\147\x3a\40\142\x6f\x72\x64\145\x72\x2d\142\x6f\170\x3b\142\x6f\162\144\x65\x72\x2d\x63\x6f\154\x6f\162\72\40\43\x30\x30\67\x33\101\x41\73\142\x6f\170\x2d\x73\x68\x61\144\x6f\167\72\x20\60\160\x78\40\x31\x70\x78\40\60\x70\x78\40\162\x67\142\x61\x28\x31\x32\60\x2c\40\x32\x30\x30\54\40\x32\63\x30\54\40\60\x2e\x36\51\40\x69\x6e\163\x65\x74\73\x63\157\x6c\157\162\x3a\x20\43\x46\106\106\73\42\x20\76\x43\157\160\x79\x3c\57\x62\x75\x74\x74\x6f\156\76\xa\11\x9\x9\46\x6e\142\163\x70\x3b\12\40\40\x20\x20\40\40\x20\x20\40\x20\40\40\x20\x20\x20\74\x69\156\160\x75\164\40\151\144\75\x22\x64\x77\x6e\x2d\142\164\x6e\42\x20\x73\x74\171\x6c\145\75\42\x70\x61\144\x64\151\x6e\147\x3a\x31\45\73\x77\151\144\164\150\72\x31\60\x30\x70\170\73\x62\x61\143\x6b\x67\x72\x6f\165\156\144\x3a\40\43\x30\x30\71\x31\103\104\x20\x6e\x6f\x6e\145\x20\162\x65\160\145\141\164\40\163\x63\x72\157\x6c\154\x20\60\x25\x20\x30\x25\73\x63\165\x72\x73\157\x72\x3a\40\160\x6f\x69\156\164\x65\162\73\146\x6f\x6e\x74\55\163\151\172\x65\x3a\x31\65\x70\170\x3b\x62\x6f\x72\x64\x65\162\55\167\151\x64\164\150\72\x20\61\160\170\73\x62\x6f\162\144\145\x72\x2d\x73\x74\171\154\145\x3a\40\163\x6f\x6c\x69\144\x3b\142\157\x72\x64\x65\x72\55\162\x61\x64\151\165\163\x3a\x20\63\x70\x78\x3b\167\150\x69\x74\145\55\163\x70\141\143\x65\x3a\x20\x6e\x6f\167\x72\141\x70\x3b\142\x6f\170\55\x73\151\172\151\x6e\147\x3a\40\x62\157\162\x64\145\162\x2d\142\x6f\x78\73\142\x6f\x72\x64\x65\x72\x2d\x63\x6f\x6c\157\162\x3a\x20\x23\60\60\67\63\101\101\73\x62\x6f\x78\55\163\x68\x61\x64\157\167\72\40\x30\x70\x78\40\61\x70\170\x20\60\x70\170\40\162\147\x62\141\x28\61\x32\60\x2c\x20\62\x30\60\x2c\40\62\x33\x30\54\x20\x30\x2e\66\51\x20\x69\156\x73\x65\x74\73\x63\x6f\154\157\162\72\x20\x23\x46\106\x46\73\42\x74\x79\x70\x65\75\42\142\165\164\164\157\156\42\40\166\x61\x6c\165\145\75\42\x44\157\x77\x6e\x6c\157\141\144\x22\x20\xa\x20\x20\40\x20\40\x20\40\40\40\40\x20\40\40\40\x20\42\76\xa\11\11\11\x3c\57\144\x69\166\x3e\xa\x9\x9\11\x3c\x2f\x64\151\166\76\xa\x9\x9\x9\xa\11\11\xa\11\x9\x9";
    ob_end_flush();
    ?>

	<script>

        function copyDivToClipboard() {
            var aux = document.createElement("input");
            aux.setAttribute("value", document.getElementById("SAML_display").textContent);
            document.body.appendChild(aux);
            aux.select();
            document.execCommand("copy");
            document.body.removeChild(aux);
            document.getElementById('copy').textContent = "Copied";
            document.getElementById('copy').style.background = "grey";
            window.getSelection().selectAllChildren( document.getElementById( "SAML_display" ) );

        }

        function download(filename, text) {
            var element = document.createElement('a');
            element.setAttribute('href', 'data:Application/octet-stream;charset=utf-8,' + encodeURIComponent(text));
            element.setAttribute('download', filename);

            element.style.display = 'none';
            document.body.appendChild(element);

            element.click();

            document.body.removeChild(element);
        }

        document.getElementById("dwn-btn").addEventListener("click", function () {

            var filename = document.getElementById("SAML_type").textContent+".xml";
            var node = document.getElementById("SAML_display");
            htmlContent = node.innerHTML;
            text = node.textContent;
            console.log(text);
            download(filename, text);
        }, false);





    </script>
<?php 
    die;
}
function mo_saml_checkMapping($HO, $AM, $Kw)
{
    try {
        $Ei = get_site_option("\163\141\x6d\154\x5f\141\155\137\145\155\x61\x69\154");
        $Q7 = get_site_option("\163\x61\155\154\137\141\155\x5f\165\163\145\x72\x6e\x61\x6d\x65");
        $ks = get_site_option("\x73\x61\x6d\154\137\141\155\x5f\x66\x69\x72\x73\164\x5f\x6e\141\x6d\145");
        $Ow = get_site_option("\163\x61\x6d\x6c\137\x61\155\x5f\x6c\141\163\164\137\156\141\155\x65");
        $NF = get_site_option("\x73\141\x6d\x6c\x5f\141\155\x5f\147\x72\x6f\165\160\137\x6e\141\155\x65");
        $yQ = array();
        $yQ = maybe_unserialize(get_site_option("\x73\141\x6d\x6c\137\141\x6d\137\x72\x6f\x6c\145\137\x6d\x61\160\x70\x69\x6e\147"));
        $Tq = get_site_option("\x73\141\155\x6c\x5f\141\155\x5f\x61\x63\x63\157\165\156\x74\137\155\141\x74\143\150\x65\x72");
        $eq = '';
        $qy = '';
        if (empty($HO)) {
            goto jC;
        }
        if (!empty($ks) && array_key_exists($ks, $HO)) {
            goto aO;
        }
        $ks = '';
        goto zl;
        aO:
        $ks = $HO[$ks][0];
        zl:
        if (!empty($Ow) && array_key_exists($Ow, $HO)) {
            goto Zv;
        }
        $Ow = '';
        goto mr;
        Zv:
        $Ow = $HO[$Ow][0];
        mr:
        if (!empty($Q7) && array_key_exists($Q7, $HO)) {
            goto MJ;
        }
        $qy = $HO["\116\x61\155\x65\111\104"][0];
        goto h2;
        MJ:
        $qy = $HO[$Q7][0];
        h2:
        if (!empty($Ei) && array_key_exists($Ei, $HO)) {
            goto On;
        }
        $eq = $HO["\116\141\155\x65\x49\x44"][0];
        goto ob;
        On:
        $eq = $HO[$Ei][0];
        ob:
        if (!empty($NF) && array_key_exists($NF, $HO)) {
            goto gd;
        }
        $NF = array();
        goto gM;
        gd:
        $NF = $HO[$NF];
        gM:
        if (!empty($Tq)) {
            goto bO;
        }
        $Tq = "\x65\x6d\x61\151\154";
        bO:
        jC:
        if ($AM == "\x74\145\x73\x74\x56\141\x6c\x69\x64\141\164\x65") {
            goto sq;
        }
        mo_saml_login_user($eq, $ks, $Ow, $qy, $NF, $yQ, $AM, $Tq, $Kw, $HO["\116\141\155\145\x49\x44"][0], $HO);
        goto hh;
        sq:
        update_site_option("\x6d\x6f\137\163\141\155\154\x5f\x74\x65\163\164", "\124\x65\x73\164\x20\x53\x75\143\143\x65\x73\x73\x66\165\x6c");
        mo_saml_show_test_result($ks, $Ow, $eq, $NF, $HO);
        hh:
    } catch (Exception $ZE) {
        echo sprintf("\101\x6e\x20\x65\x72\x72\x6f\x72\x20\x6f\x63\x63\165\162\x72\x65\x64\40\167\150\x69\154\x65\x20\160\x72\157\x63\x65\163\163\151\156\x67\x20\164\150\145\40\x53\101\x4d\114\40\x52\145\x73\x70\157\156\163\145\x2e");
        die;
    }
}
function mo_saml_show_test_result($ks, $Ow, $eq, $NF, $HO)
{
    echo "\74\144\151\x76\x20\163\x74\171\x6c\145\x3d\x22\x66\x6f\x6e\x74\x2d\146\x61\x6d\151\154\x79\x3a\103\141\x6c\x69\x62\x72\x69\73\160\x61\144\x64\151\x6e\147\x3a\x30\40\63\45\x3b\x22\x3e";
    if (!empty($eq)) {
        goto jk;
    }
    echo "\74\x64\x69\166\x20\x73\164\171\x6c\145\75\x22\143\157\x6c\157\x72\x3a\40\43\x61\x39\x34\64\x34\62\73\142\x61\x63\x6b\147\162\157\x75\156\x64\55\x63\x6f\x6c\x6f\x72\x3a\x20\x23\146\62\x64\x65\144\145\73\x70\x61\144\x64\x69\156\x67\x3a\40\x31\65\x70\170\x3b\155\141\162\x67\151\x6e\55\142\x6f\164\164\157\x6d\x3a\x20\62\60\160\x78\x3b\164\145\170\164\55\x61\x6c\151\147\156\x3a\x63\x65\x6e\x74\x65\x72\73\142\x6f\162\x64\x65\x72\72\61\160\x78\x20\x73\x6f\x6c\151\144\40\43\x45\x36\102\x33\x42\62\x3b\x66\x6f\x6e\x74\55\x73\151\172\x65\x3a\x31\x38\160\x74\73\x22\x3e\x54\105\123\124\40\x46\101\111\114\x45\x44\x3c\x2f\x64\x69\166\76\12\x9\11\x9\x9\x9\x9\x3c\144\x69\x76\x20\163\x74\x79\x6c\145\x3d\x22\x63\157\154\x6f\162\72\x20\43\x61\x39\64\64\x34\x32\x3b\x66\157\156\164\55\163\x69\172\145\72\61\64\160\164\73\40\155\x61\162\147\x69\156\x2d\x62\x6f\164\x74\157\155\72\x32\60\160\170\73\x22\x3e\x57\101\122\x4e\111\x4e\107\x3a\40\123\157\155\145\40\x41\164\x74\x72\x69\x62\165\x74\145\x73\x20\x44\x69\x64\40\x4e\157\x74\x20\115\141\x74\x63\x68\x2e\x3c\x2f\x64\151\x76\76\12\11\11\11\x9\11\11\x3c\x64\x69\166\x20\x73\x74\x79\154\x65\x3d\x22\x64\151\163\x70\154\x61\x79\x3a\142\154\157\x63\x6b\73\x74\145\170\x74\55\x61\154\151\147\x6e\72\x63\145\156\164\x65\x72\73\x6d\x61\162\147\151\x6e\x2d\142\x6f\164\x74\x6f\x6d\x3a\x34\x25\x3b\42\x3e\x3c\x69\x6d\147\x20\163\x74\x79\x6c\145\75\x22\167\151\x64\x74\150\x3a\61\65\45\73\x22\x73\x72\x63\x3d\x22" . plugin_dir_url(__FILE__) . "\x69\x6d\x61\x67\x65\163\57\x77\x72\157\156\147\x2e\160\156\x67\x22\76\x3c\x2f\x64\x69\166\76";
    goto za;
    jk:
    update_site_option("\x6d\x6f\x5f\163\x61\155\154\137\x74\x65\x73\164\x5f\x63\x6f\x6e\146\151\147\137\141\164\x74\162\x73", $HO);
    echo "\x3c\x64\151\166\40\x73\x74\x79\x6c\145\x3d\42\x63\x6f\154\157\162\72\x20\x23\x33\143\67\66\x33\x64\73\12\x9\11\11\11\11\x9\x62\x61\143\153\x67\162\x6f\x75\156\x64\55\x63\x6f\154\x6f\162\x3a\x20\43\144\x66\146\60\x64\70\x3b\40\160\x61\x64\x64\x69\156\x67\72\x32\45\x3b\155\141\x72\147\151\x6e\55\142\x6f\x74\x74\x6f\155\x3a\x32\60\160\x78\x3b\x74\145\x78\164\55\141\154\151\x67\x6e\72\143\145\x6e\164\x65\x72\x3b\x20\x62\x6f\x72\x64\x65\x72\x3a\x31\160\x78\x20\163\x6f\154\x69\144\x20\43\x41\x45\104\102\x39\101\x3b\x20\146\x6f\156\x74\x2d\163\151\172\x65\72\61\x38\160\x74\x3b\x22\76\124\x45\x53\x54\x20\123\x55\x43\x43\x45\x53\123\106\x55\114\74\x2f\x64\151\166\x3e\12\11\x9\11\x9\x9\x9\74\x64\x69\166\40\x73\x74\171\x6c\x65\x3d\42\144\151\163\x70\x6c\141\171\x3a\x62\154\x6f\x63\153\x3b\164\x65\170\164\55\141\154\x69\147\x6e\72\143\x65\156\x74\x65\x72\73\155\141\162\147\151\156\55\x62\x6f\164\x74\157\x6d\72\64\45\x3b\42\76\x3c\151\x6d\x67\x20\163\164\x79\154\145\x3d\42\x77\151\144\x74\150\x3a\x31\65\x25\x3b\x22\x73\162\143\75\x22" . plugin_dir_url(__FILE__) . "\151\x6d\x61\x67\x65\x73\57\147\x72\x65\145\156\137\143\x68\x65\x63\153\56\160\x6e\147\42\x3e\74\x2f\144\151\x76\76";
    za:
    $VE = get_site_option("\x73\141\x6d\154\137\x61\155\x5f\141\x63\143\x6f\x75\156\164\137\155\x61\164\143\150\145\162") ? get_site_option("\x73\x61\155\x6c\137\x61\155\x5f\x61\143\x63\157\165\x6e\164\137\x6d\141\164\x63\x68\x65\162") : "\145\x6d\141\151\154";
    if (!($VE == "\145\x6d\141\151\x6c" && !filter_var($HO["\116\141\155\x65\111\104"][0], FILTER_VALIDATE_EMAIL))) {
        goto o2;
    }
    echo "\74\160\x3e\74\x66\x6f\x6e\164\x20\143\157\x6c\157\162\x3d\42\x23\106\x46\60\60\60\60\x22\40\163\164\171\x6c\145\75\x22\x66\157\x6e\164\x2d\163\151\172\145\x3a\61\64\160\x74\x22\76\50\x57\x61\x72\x6e\151\156\x67\72\40\x54\150\145\40\116\x61\155\x65\111\104\x20\166\141\154\x75\145\40\151\163\x20\156\157\x74\40\141\x20\x76\x61\x6c\151\x64\40\105\x6d\141\151\x6c\40\111\x44\51\74\57\146\x6f\x6e\164\x3e\74\57\x70\76";
    o2:
    echo "\x3c\x73\160\141\x6e\x20\163\x74\171\x6c\145\x3d\x22\x66\x6f\x6e\x74\x2d\x73\x69\172\145\72\61\x34\160\164\73\x22\76\x3c\x62\76\x48\x65\154\x6c\157\x3c\57\x62\x3e\54\40" . $eq . "\x3c\57\163\160\141\x6e\x3e\x3c\142\x72\x2f\76\x3c\x70\40\163\x74\x79\154\145\75\42\146\157\156\164\55\167\x65\151\147\150\164\72\x62\157\154\144\73\146\157\156\164\x2d\x73\151\x7a\145\72\61\x34\160\x74\73\x6d\141\162\147\151\156\55\x6c\x65\146\164\72\x31\45\x3b\42\76\101\x54\124\122\x49\x42\125\x54\105\x53\x20\x52\x45\103\x45\x49\x56\105\104\72\74\x2f\x70\76\12\x9\11\11\x9\11\x3c\x74\x61\142\154\145\x20\163\x74\x79\154\145\75\42\142\x6f\162\x64\145\162\x2d\x63\157\154\x6c\x61\x70\x73\145\x3a\x63\x6f\x6c\x6c\x61\160\x73\x65\x3b\x62\x6f\162\x64\x65\162\x2d\163\160\141\x63\151\x6e\x67\x3a\x30\73\40\144\151\163\x70\154\141\171\x3a\164\141\x62\154\x65\x3b\167\x69\144\164\x68\72\61\x30\x30\45\73\40\146\157\x6e\164\x2d\163\x69\172\x65\x3a\x31\64\x70\x74\x3b\x62\141\143\153\x67\162\157\165\156\144\x2d\x63\157\x6c\x6f\162\x3a\43\105\x44\x45\x44\105\x44\x3b\42\76\xa\x9\x9\x9\11\11\x9\74\x74\x72\x20\163\x74\171\154\x65\x3d\x22\164\145\170\164\x2d\141\154\x69\x67\x6e\x3a\x63\145\156\164\145\162\73\42\76\74\164\x64\x20\x73\x74\171\154\x65\x3d\42\146\x6f\x6e\x74\x2d\167\x65\x69\x67\x68\x74\x3a\x62\x6f\x6c\x64\x3b\142\x6f\x72\x64\145\162\72\62\160\x78\40\x73\x6f\154\x69\x64\40\x23\x39\x34\x39\60\x39\x30\73\160\141\x64\x64\151\156\147\72\62\x25\73\x22\76\x41\124\x54\x52\111\102\125\124\105\x20\x4e\101\115\x45\74\x2f\x74\144\76\74\164\x64\40\163\164\x79\x6c\x65\x3d\x22\x66\157\x6e\x74\x2d\167\145\x69\147\150\164\72\142\x6f\154\144\x3b\x70\x61\144\x64\151\x6e\147\72\62\x25\x3b\142\x6f\x72\x64\x65\162\72\62\160\x78\x20\163\157\154\151\x64\x20\43\x39\x34\71\x30\x39\x30\x3b\40\x77\157\162\x64\x2d\x77\x72\141\x70\x3a\142\162\145\x61\x6b\x2d\167\x6f\x72\x64\73\x22\x3e\101\124\124\122\111\102\x55\124\105\x20\126\101\x4c\x55\x45\x3c\x2f\164\144\76\74\57\x74\x72\x3e";
    if (!empty($HO)) {
        goto WS;
    }
    echo "\x4e\157\x20\101\164\164\x72\151\142\x75\164\145\x73\x20\122\x65\143\x65\x69\166\145\x64\x2e";
    goto je;
    WS:
    foreach ($HO as $Z1 => $zF) {
        echo "\74\x74\162\76\74\x74\x64\x20\163\164\171\x6c\x65\x3d\x27\146\x6f\x6e\x74\x2d\x77\145\151\147\x68\164\72\x62\157\154\x64\x3b\x62\157\162\x64\x65\162\72\62\160\170\40\x73\157\154\x69\x64\40\x23\71\64\71\60\71\60\x3b\x70\141\144\x64\x69\156\147\72\62\45\73\x27\x3e" . $Z1 . "\74\x2f\164\144\x3e\x3c\x74\144\40\x73\164\171\154\145\75\47\x70\141\144\x64\151\x6e\147\x3a\x32\x25\x3b\x62\x6f\x72\144\x65\162\x3a\x32\160\x78\x20\163\157\x6c\151\144\x20\43\x39\x34\x39\60\x39\x30\73\x20\x77\x6f\162\144\x2d\x77\x72\141\160\x3a\x62\x72\x65\x61\153\x2d\x77\157\x72\x64\73\x27\76" . implode("\x3c\x68\x72\57\76", $zF) . "\74\57\164\x64\x3e\x3c\x2f\x74\x72\x3e";
        rM:
    }
    Fk:
    je:
    echo "\74\x2f\164\x61\x62\154\145\76\74\x2f\144\151\x76\x3e";
    echo "\74\144\151\166\x20\x73\164\x79\x6c\145\x3d\42\155\x61\162\147\x69\x6e\x3a\63\x25\73\144\x69\163\x70\x6c\x61\171\72\142\x6c\157\143\x6b\x3b\x74\x65\x78\x74\55\141\154\x69\147\x6e\72\143\x65\156\x74\145\x72\x3b\42\76\xa\x9\11\x9\x9\11\x9\11\x3c\151\156\x70\x75\164\40\163\164\171\x6c\x65\75\42\160\x61\x64\x64\151\156\147\x3a\61\x25\73\x77\151\144\x74\x68\x3a\x32\65\x30\160\x78\x3b\x62\141\143\x6b\x67\x72\x6f\x75\x6e\x64\72\x20\x23\60\60\71\x31\x43\x44\40\156\157\x6e\145\40\162\145\x70\x65\141\x74\40\x73\x63\x72\x6f\x6c\x6c\x20\x30\x25\40\60\45\x3b\143\x75\162\163\x6f\x72\x3a\x20\160\157\151\156\x74\145\162\x3b\146\157\156\164\x2d\163\151\172\145\x3a\x31\65\x70\x78\73\x62\157\162\x64\x65\x72\55\167\x69\x64\x74\150\x3a\40\x31\x70\x78\73\142\x6f\x72\144\x65\162\x2d\163\x74\x79\154\x65\x3a\40\163\157\154\151\144\73\x62\x6f\x72\144\145\x72\x2d\162\141\x64\x69\x75\163\72\x20\63\x70\170\x3b\x77\x68\x69\x74\x65\55\163\x70\141\143\145\x3a\40\156\157\x77\x72\x61\160\73\x62\157\x78\x2d\163\151\x7a\x69\156\147\72\x20\142\x6f\x72\x64\x65\x72\x2d\142\157\170\73\x62\x6f\162\x64\x65\162\x2d\x63\157\x6c\157\x72\72\40\x23\60\x30\67\63\101\101\x3b\x62\157\170\x2d\163\x68\x61\x64\157\167\72\x20\60\160\170\x20\x31\160\x78\40\x30\160\170\x20\162\147\x62\x61\50\x31\x32\60\54\40\62\x30\x30\x2c\40\62\63\60\x2c\40\x30\x2e\66\x29\x20\x69\x6e\x73\x65\164\73\143\157\154\x6f\x72\x3a\40\x23\x46\x46\106\x3b\42\12\x9\11\11\x9\x9\x9\11\x74\171\160\x65\75\x22\x62\165\x74\164\157\156\x22\40\166\x61\154\165\x65\x3d\x22\103\157\x6e\146\151\x67\x75\x72\x65\x20\x41\164\164\x72\151\x62\x75\164\x65\57\x52\157\154\x65\40\115\x61\x70\160\x69\x6e\x67\x22\40\157\156\x43\154\151\x63\x6b\75\42\x63\x6c\157\163\145\x5f\141\x6e\x64\137\x72\x65\144\x69\x72\145\143\x74\x28\x29\73\x22\x3e\x20\46\x6e\x62\163\x70\x3b\40\x3c\x69\x6e\x70\x75\164\40\x73\x74\x79\154\145\x3d\42\160\x61\x64\x64\151\x6e\x67\x3a\x31\x25\73\x77\151\x64\164\150\72\x31\x30\x30\160\x78\x3b\142\x61\x63\x6b\147\162\157\165\x6e\x64\72\x20\x23\60\60\x39\x31\x43\x44\40\156\157\156\x65\40\162\145\160\145\x61\164\x20\x73\x63\x72\157\154\x6c\40\60\45\x20\60\x25\73\143\165\x72\163\157\162\x3a\x20\x70\x6f\151\x6e\164\x65\162\x3b\146\157\x6e\164\55\x73\151\x7a\x65\x3a\x31\x35\160\x78\73\142\157\162\144\145\162\x2d\x77\x69\144\x74\x68\72\x20\x31\x70\170\x3b\x62\157\162\x64\145\x72\x2d\163\164\171\154\x65\x3a\x20\163\157\154\151\x64\x3b\x62\x6f\x72\x64\x65\x72\55\162\x61\144\151\x75\163\x3a\40\x33\x70\170\73\167\x68\151\164\145\x2d\163\x70\x61\x63\x65\72\x20\156\157\167\162\x61\160\x3b\142\x6f\x78\x2d\x73\x69\172\x69\x6e\x67\x3a\x20\142\157\x72\x64\x65\x72\55\x62\157\x78\73\x62\157\162\144\x65\x72\x2d\143\157\x6c\x6f\162\x3a\40\x23\60\60\x37\63\101\101\x3b\142\157\x78\x2d\163\150\141\x64\x6f\167\72\x20\x30\x70\x78\x20\61\160\x78\x20\60\x70\x78\40\x72\147\142\x61\50\61\x32\x30\x2c\x20\62\x30\60\x2c\40\62\63\x30\54\x20\x30\x2e\66\x29\x20\x69\156\x73\x65\164\73\143\x6f\x6c\157\162\x3a\40\43\106\106\x46\x3b\42\164\x79\x70\x65\x3d\42\142\165\164\164\157\156\x22\x20\166\x61\x6c\x75\x65\75\42\104\x6f\156\145\x22\40\157\156\x43\154\151\x63\x6b\x3d\42\163\x65\154\x66\56\143\x6c\157\x73\x65\x28\x29\x3b\x22\76\12\x9\x9\x9\x9\11\x9\11\74\57\x64\x69\x76\x3e\xa\11\11\11\x9\x9\x9\x9\x3c\x73\x63\x72\151\160\164\76\12\40\x20\x20\x20\x20\x20\x20\x20\40\x20\x20\40\xa\x9\11\x9\x9\x9\x9\11\146\x75\156\x63\164\x69\x6f\156\x20\x63\154\157\x73\145\x5f\141\x6e\x64\x5f\162\145\144\x69\x72\145\143\164\x28\51\173\xa\x9\x9\x9\x9\x9\11\x9\x9\x77\x69\156\144\157\167\56\x6f\x70\145\x6e\x65\x72\56\x72\x65\144\x69\162\x65\143\164\137\164\157\137\141\x74\164\x72\151\142\165\164\145\137\155\x61\x70\x70\151\156\147\50\51\73\xa\11\x9\11\x9\11\11\x9\11\x73\x65\154\146\56\143\154\x6f\163\x65\50\51\73\xa\x9\11\11\11\x9\x9\11\x7d\xa\11\11\x9\x9\x9\11\x9\12\11\11\11\11\x9\x9\11\x3c\x2f\163\x63\x72\x69\160\164\x3e";
    die;
}
function mo_saml_convert_to_windows_iconv($bd)
{
    $wy = get_site_option("\155\157\137\163\141\x6d\154\x5f\145\156\143\157\x64\x69\156\147\x5f\x65\x6e\141\142\x6c\x65\144");
    if (!($wy === '')) {
        goto oz;
    }
    return $bd;
    oz:
    return iconv("\x55\x54\x46\55\70", "\103\120\x31\62\65\62\x2f\x2f\x49\107\116\x4f\x52\x45", $bd);
}
function mo_saml_login_user($eq, $ks, $Ow, $qy, $NF, $yQ, $AM, $Tq, $Kw = '', $Xe = '', $HO = null)
{
    do_action("\155\157\x5f\141\142\x72\x5f\146\x69\154\x74\x65\x72\137\154\157\x67\151\x6e", $HO);
    $qy = mo_saml_sanitize_username($qy);
    if (get_site_option("\155\x6f\137\163\141\x6d\x6c\137\144\x69\163\x61\x62\154\145\x5f\162\x6f\154\x65\x5f\155\141\160\x70\151\x6e\x67")) {
        goto Rb;
    }
    check_if_user_allowed_to_login_due_to_role_restriction($NF);
    Rb:
    $Wz = get_site_option("\x6d\x6f\x5f\x73\x61\x6d\x6c\x5f\x73\160\137\x62\x61\x73\x65\x5f\x75\162\154");
    mo_saml_restrict_users_based_on_domain($eq);
    if (!empty($yQ)) {
        goto w4;
    }
    $yQ["\104\105\106\x41\125\114\124"]["\144\x65\x66\141\x75\x6c\164\x5f\x72\x6f\x6c\145"] = "\163\x75\x62\x73\x63\162\x69\x62\x65\162";
    $yQ["\x44\105\106\101\x55\x4c\124"]["\x64\157\x6e\164\137\x61\x6c\154\157\x77\137\x75\156\154\151\163\x74\145\144\137\x75\x73\145\162"] = '';
    $yQ["\104\x45\x46\101\x55\x4c\124"]["\x64\157\x6e\164\137\143\162\x65\141\x74\145\137\165\x73\x65\x72"] = '';
    $yQ["\x44\105\x46\x41\125\114\124"]["\153\145\145\x70\137\145\170\x69\x73\164\151\156\x67\137\x75\x73\145\162\163\137\x72\157\x6c\145"] = '';
    $yQ["\x44\x45\106\101\x55\114\x54"]["\155\x6f\137\x73\x61\155\x6c\x5f\x64\157\156\x74\137\141\x6c\x6c\157\x77\x5f\x75\x73\x65\162\137\x74\157\154\x6f\147\151\156\x5f\143\162\x65\141\164\145\137\x77\151\x74\x68\x5f\147\151\x76\145\156\137\x67\x72\157\165\x70\x73"] = '';
    $yQ["\104\105\x46\x41\x55\x4c\x54"]["\155\x6f\137\x73\141\x6d\x6c\x5f\162\145\x73\164\162\151\x63\164\137\x75\163\x65\x72\x73\137\x77\151\164\x68\137\x67\162\x6f\x75\x70\x73"] = '';
    w4:
    global $wpdb;
    $vE = get_current_blog_id();
    $m9 = "\165\156\x63\x68\x65\143\153\145\144";
    if (!empty($Wz)) {
        goto b5;
    }
    $Wz = get_network_site_url();
    b5:
    if (email_exists($eq) || username_exists($qy)) {
        goto Dg;
    }
    $tN = Utilities::get_active_sites();
    $xL = get_site_option("\155\x6f\137\x61\x70\x70\154\x79\x5f\162\x6f\154\145\x5f\x6d\x61\160\160\151\156\x67\137\x66\157\162\137\x73\x69\x74\x65\163");
    if (!get_site_option("\155\x6f\137\163\141\x6d\154\x5f\144\x69\x73\141\x62\154\x65\137\x72\157\x6c\x65\137\155\141\x70\x70\x69\156\147")) {
        goto nR;
    }
    $qU = mo_saml_add_user_to_blog($eq, $qy);
    goto N_;
    nR:
    $qU = mo_saml_assign_roles_to_new_user($tN, $xL, $yQ, $NF, $qy, $eq);
    N_:
    switch_to_blog($vE);
    if (!empty($qU)) {
        goto Rv;
    }
    if (!get_site_option("\155\157\x5f\x73\x61\x6d\x6c\x5f\x64\151\x73\x61\142\x6c\x65\x5f\x72\157\x6c\145\x5f\155\x61\x70\x70\151\x6e\x67")) {
        goto XH;
    }
    wp_die("\x57\145\x20\143\157\165\x6c\x64\x20\156\157\164\x20\163\151\147\156\x20\x79\x6f\x75\40\151\x6e\x2e\40\x50\154\145\x61\x73\145\40\x63\157\156\x74\141\143\164\40\141\x64\x6d\151\156\x69\163\x74\x72\x61\164\x6f\162", "\x4c\157\147\151\x6e\40\x46\x61\151\154\x65\x64\41");
    goto Bl;
    XH:
    $CN = get_site_option("\x6d\157\x5f\163\141\x6d\x6c\x5f\141\x63\143\157\165\156\164\137\143\162\145\x61\164\x69\x6f\156\x5f\x64\x69\163\141\x62\x6c\x65\x64\x5f\x6d\163\147");
    if (!empty($CN)) {
        goto VY;
    }
    $CN = "\127\145\40\x63\157\x75\x6c\144\40\156\x6f\x74\40\x73\151\147\156\x20\171\x6f\x75\x20\151\156\56\40\x50\x6c\145\141\x73\x65\x20\143\x6f\156\x74\x61\x63\164\x20\x79\157\165\162\x20\101\x64\155\x69\x6e\x69\163\x74\x72\141\164\157\x72\56";
    VY:
    wp_die($CN, "\105\x72\162\x6f\x72\72\x20\x4e\157\164\x20\141\x20\127\x6f\162\x64\x50\162\145\163\x73\40\x4d\145\155\x62\145\x72");
    Bl:
    Rv:
    $user = get_user_by("\151\x64", $qU);
    mo_saml_map_basic_attributes($user, $ks, $Ow, $HO);
    mo_saml_map_custom_attributes($qU, $HO);
    $Dk = mo_saml_get_redirect_url($Wz, $AM);
    do_action("\155\x69\x6e\151\157\x72\141\156\x67\x65\137\160\157\x73\164\x5f\141\x75\164\x68\145\x6e\164\151\143\x61\x74\145\137\165\163\145\x72\137\154\157\x67\151\x6e", $user, null, $Dk, true);
    mo_saml_set_auth_cookie($user, $Kw, $Xe, true);
    do_action("\x6d\x6f\x5f\x73\141\155\154\x5f\141\164\164\x72\x69\142\x75\164\x65\x73", $qy, $eq, $ks, $Ow, $NF, null, true);
    goto Rr;
    Dg:
    if (email_exists($eq)) {
        goto id;
    }
    $user = get_user_by("\x6c\157\147\151\156", $qy);
    goto vp;
    id:
    $user = get_user_by("\x65\155\141\x69\x6c", $eq);
    vp:
    $qU = $user->ID;
    if (!(!empty($eq) and strcasecmp($eq, $user->user_email) != 0)) {
        goto A0;
    }
    $qU = wp_update_user(array("\111\x44" => $qU, "\x75\163\x65\x72\x5f\x65\155\141\x69\x6c" => $eq));
    A0:
    mo_saml_map_basic_attributes($user, $ks, $Ow, $HO);
    mo_saml_map_custom_attributes($qU, $HO);
    $tN = Utilities::get_active_sites();
    $xL = get_site_option("\x6d\x6f\137\141\160\x70\154\x79\x5f\x72\x6f\x6c\145\x5f\x6d\x61\160\160\151\x6e\147\x5f\x66\x6f\x72\x5f\163\x69\x74\x65\x73");
    if (get_site_option("\x6d\157\x5f\x73\x61\155\154\x5f\x64\x69\163\141\x62\x6c\145\137\x72\157\154\x65\137\155\x61\160\x70\151\156\x67")) {
        goto JK;
    }
    foreach ($tN as $blog_id) {
        switch_to_blog($blog_id);
        $user = get_user_by("\x69\x64", $qU);
        $QD = '';
        if ($xL) {
            goto ie;
        }
        $QD = $blog_id;
        goto Rc;
        ie:
        $QD = 0;
        Rc:
        if (empty($yQ)) {
            goto Vn;
        }
        if (!empty($yQ[$QD])) {
            goto LO;
        }
        if (!empty($yQ["\x44\x45\106\101\x55\x4c\x54"])) {
            goto QU;
        }
        $N1 = "\163\x75\142\163\143\x72\151\x62\x65\162";
        $HR = '';
        $m9 = '';
        $M2 = '';
        goto CY;
        QU:
        $N1 = isset($yQ["\x44\105\106\x41\x55\x4c\124"]["\x64\145\x66\141\x75\154\x74\x5f\x72\157\x6c\145"]) ? $yQ["\x44\x45\x46\101\x55\x4c\124"]["\144\145\146\x61\x75\154\164\137\162\157\x6c\145"] : "\163\165\142\x73\143\162\151\x62\x65\x72";
        $HR = isset($yQ["\104\105\x46\x41\125\114\124"]["\144\157\156\164\137\x61\154\x6c\157\x77\x5f\165\x6e\x6c\x69\x73\164\x65\x64\x5f\x75\163\x65\162"]) ? $yQ["\x44\105\x46\x41\x55\114\124"]["\144\157\156\164\137\x61\154\x6c\x6f\167\137\165\x6e\154\151\x73\164\145\144\137\165\x73\x65\162"] : '';
        $m9 = isset($yQ["\104\x45\x46\x41\125\x4c\124"]["\144\157\156\x74\137\x63\x72\x65\141\x74\145\x5f\x75\x73\x65\162"]) ? $yQ["\104\105\x46\x41\x55\x4c\124"]["\144\x6f\x6e\164\x5f\143\x72\x65\x61\x74\145\x5f\165\x73\x65\162"] : '';
        $M2 = isset($yQ["\x44\105\x46\x41\125\114\x54"]["\x6b\x65\x65\x70\137\145\x78\151\163\164\x69\156\147\137\x75\163\x65\162\163\x5f\162\x6f\x6c\x65"]) ? $yQ["\104\105\106\x41\x55\114\124"]["\153\x65\x65\160\137\x65\x78\151\163\164\x69\156\x67\x5f\165\163\x65\162\163\137\162\157\x6c\x65"] : '';
        CY:
        goto Ex;
        LO:
        $N1 = isset($yQ[$QD]["\144\x65\146\141\x75\x6c\x74\137\x72\x6f\x6c\145"]) ? $yQ[$QD]["\x64\145\146\141\x75\154\x74\137\162\157\154\x65"] : '';
        $HR = isset($yQ[$QD]["\144\x6f\x6e\x74\137\x61\x6c\154\157\x77\x5f\x75\156\x6c\x69\163\164\x65\144\137\x75\163\x65\162"]) ? $yQ[$QD]["\144\x6f\156\x74\x5f\x61\x6c\154\x6f\167\137\165\x6e\x6c\151\163\164\145\x64\137\165\163\x65\162"] : '';
        $m9 = isset($yQ[$QD]["\x64\157\x6e\x74\137\x63\x72\145\x61\x74\x65\137\165\x73\145\x72"]) ? $yQ[$QD]["\144\157\x6e\164\x5f\x63\162\x65\141\164\x65\137\x75\163\x65\162"] : '';
        $M2 = isset($yQ[$QD]["\153\x65\145\x70\137\x65\170\x69\163\x74\151\x6e\147\x5f\165\163\145\162\x73\137\162\x6f\154\x65"]) ? $yQ[$QD]["\153\x65\145\160\137\x65\170\151\x73\x74\x69\x6e\147\137\x75\x73\145\162\x73\x5f\162\x6f\154\145"] : '';
        Ex:
        Vn:
        if (!is_user_member_of_blog($qU, $blog_id)) {
            goto K_;
        }
        if (isset($M2) && $M2 == "\143\x68\x65\x63\x6b\145\144") {
            goto V0;
        }
        $lt = assign_roles_to_user($user, $yQ, $blog_id, $NF, $QD);
        goto tR;
        V0:
        $lt = false;
        tR:
        if (is_administrator_user($user)) {
            goto oD;
        }
        if (isset($M2) && $M2 == "\143\150\145\x63\x6b\x65\x64") {
            goto B7;
        }
        if ($lt !== true && !empty($HR) && $HR == "\143\x68\145\143\153\x65\x64") {
            goto UN;
        }
        if ($lt !== true && !empty($N1) && $N1 !== "\x66\141\x6c\163\x65") {
            goto k7;
        }
        if ($lt !== true && is_user_member_of_blog($qU, $blog_id)) {
            goto kr;
        }
        goto Lr;
        B7:
        goto Lr;
        UN:
        $qU = wp_update_user(array("\111\x44" => $qU, "\x72\x6f\x6c\145" => false));
        goto Lr;
        k7:
        $qU = wp_update_user(array("\111\x44" => $qU, "\162\x6f\154\145" => $N1));
        goto Lr;
        kr:
        $cT = get_site_option("\144\x65\146\x61\165\154\x74\x5f\x72\157\154\145");
        $qU = wp_update_user(array("\x49\x44" => $qU, "\x72\157\x6c\x65" => $cT));
        Lr:
        oD:
        goto Vk;
        K_:
        $J5 = TRUE;
        $pK = get_site_option("\163\141\155\154\x5f\x73\x73\157\x5f\163\x65\164\164\151\156\x67\163");
        if (!empty($pK[$blog_id])) {
            goto Vj;
        }
        $pK[$blog_id] = $pK["\x44\x45\x46\101\x55\114\124"];
        Vj:
        if (empty($yQ)) {
            goto Cp;
        }
        if (array_key_exists($QD, $yQ)) {
            goto zE;
        }
        if (!array_key_exists("\x44\x45\x46\101\x55\114\124", $yQ)) {
            goto ml;
        }
        $LJ = get_saml_roles_to_assign($yQ, $QD, $NF);
        if (!(empty($LJ) && strcmp($yQ["\x44\x45\106\x41\125\114\124"]["\144\157\156\164\x5f\143\x72\145\141\x74\x65\137\x75\x73\145\x72"], "\143\x68\x65\x63\x6b\x65\x64") == 0)) {
            goto ge;
        }
        $J5 = FALSE;
        ge:
        ml:
        goto uP;
        zE:
        $LJ = get_saml_roles_to_assign($yQ, $QD, $NF);
        if (!(empty($LJ) && strcmp($yQ[$QD]["\144\157\156\x74\x5f\143\x72\x65\x61\164\145\x5f\x75\163\x65\162"], "\x63\x68\x65\143\x6b\x65\144") == 0)) {
            goto Jq;
        }
        $J5 = FALSE;
        Jq:
        uP:
        Cp:
        if (!$J5) {
            goto OE;
        }
        add_user_to_blog($blog_id, $qU, false);
        $lt = assign_roles_to_user($user, $yQ, $blog_id, $NF, $QD);
        if ($lt !== true && !empty($HR) && $HR == "\143\150\145\x63\153\x65\x64") {
            goto Gz;
        }
        if ($lt !== true && !empty($N1) && $N1 !== "\x66\141\x6c\x73\x65") {
            goto zy;
        }
        if ($lt !== true) {
            goto Wt;
        }
        goto lq;
        Gz:
        $qU = wp_update_user(array("\111\x44" => $qU, "\162\157\154\145" => false));
        goto lq;
        zy:
        $qU = wp_update_user(array("\111\x44" => $qU, "\162\x6f\x6c\x65" => $N1));
        goto lq;
        Wt:
        $cT = get_site_option("\x64\x65\146\141\165\154\x74\137\162\x6f\x6c\x65");
        $qU = wp_update_user(array("\111\104" => $qU, "\x72\157\154\x65" => $cT));
        lq:
        OE:
        Vk:
        HW:
    }
    YF:
    JK:
    switch_to_blog($vE);
    if ($qU) {
        goto RY;
    }
    wp_die("\x49\156\x76\141\154\x69\144\x20\165\163\x65\x72\56\40\x50\x6c\145\x61\x73\x65\x20\x74\162\171\40\x61\147\x61\x69\x6e\56");
    RY:
    $user = get_user_by("\151\x64", $qU);
    mo_saml_set_auth_cookie($user, $Kw, $Xe, true);
    do_action("\x6d\x6f\x5f\x73\x61\155\154\x5f\141\164\x74\x72\151\x62\165\164\145\x73", $qy, $eq, $ks, $Ow, $NF);
    Rr:
    mo_saml_post_login_redirection($Wz, $AM);
}
function mo_saml_add_user_to_blog($eq, $qy, $blog_id = 0)
{
    if (email_exists($eq)) {
        goto CK;
    }
    if (!empty($qy)) {
        goto HZ;
    }
    $qU = mo_saml_create_user($eq, $eq, $blog_id);
    goto ea;
    HZ:
    $qU = mo_saml_create_user($qy, $eq, $blog_id);
    ea:
    goto nm;
    CK:
    $user = get_user_by("\145\x6d\x61\151\x6c", $eq);
    $qU = $user->ID;
    if (empty($blog_id)) {
        goto Ix;
    }
    add_user_to_blog($blog_id, $qU, false);
    Ix:
    nm:
    return $qU;
}
function mo_saml_create_user($qy, $eq, $blog_id)
{
    $rd = wp_generate_password(10, false);
    if (username_exists($qy)) {
        goto Cy;
    }
    $qU = wp_create_user($qy, $rd, $eq);
    goto c4;
    Cy:
    $user = get_user_by("\154\x6f\x67\x69\156", $qy);
    $qU = $user->ID;
    if (!$blog_id) {
        goto cs;
    }
    add_user_to_blog($blog_id, $qU, false);
    cs:
    c4:
    if (!is_wp_error($qU)) {
        goto xT;
    }
    echo "\74\163\164\162\x6f\x6e\147\x3e\105\x52\122\117\x52\x3c\57\x73\164\x72\157\156\x67\76\x3a\40\105\155\160\164\171\40\x55\x73\x65\x72\40\x4e\141\x6d\145\40\141\x6e\144\40\x45\155\x61\x69\x6c\56\x20\x50\154\x65\x61\x73\x65\x20\x63\157\x6e\164\x61\x63\x74\x20\x79\157\x75\x72\x20\x61\144\x6d\x69\x6e\x69\163\x74\x72\141\164\157\x72\x2e";
    die;
    xT:
    return $qU;
}
function mo_saml_assign_roles_to_new_user($tN, $xL, $yQ, $NF, $qy, $eq)
{
    global $wpdb;
    $fP = false;
    foreach ($tN as $blog_id) {
        $AO = TRUE;
        $QD = '';
        if ($xL) {
            goto ME;
        }
        $QD = $blog_id;
        goto FI;
        ME:
        $QD = 0;
        FI:
        $pK = get_site_option("\x73\141\155\x6c\137\x73\x73\157\x5f\x73\145\164\164\151\156\147\163");
        if (!empty($pK[$blog_id])) {
            goto Xd;
        }
        $pK[$blog_id] = $pK["\x44\105\x46\x41\125\x4c\124"];
        Xd:
        if (empty($yQ)) {
            goto oI;
        }
        if (!empty($yQ[$QD])) {
            goto zH;
        }
        if (!empty($yQ["\x44\x45\106\101\x55\x4c\x54"])) {
            goto rh;
        }
        $N1 = "\163\165\x62\x73\143\162\151\142\145\x72";
        $HR = '';
        $M2 = '';
        $LJ = '';
        goto PB;
        rh:
        $N1 = isset($yQ["\x44\x45\x46\x41\125\x4c\x54"]["\x64\145\146\141\165\154\x74\x5f\162\x6f\x6c\x65"]) ? $yQ["\x44\105\106\x41\x55\114\124"]["\x64\x65\x66\141\165\x6c\x74\x5f\x72\x6f\x6c\145"] : '';
        $HR = isset($yQ["\x44\105\106\x41\x55\x4c\x54"]["\x64\157\156\x74\x5f\141\x6c\154\x6f\167\x5f\165\156\154\151\x73\x74\x65\144\x5f\165\163\145\x72"]) ? $yQ["\104\105\x46\x41\125\x4c\x54"]["\144\x6f\x6e\x74\x5f\x61\x6c\x6c\157\x77\137\x75\x6e\x6c\x69\163\164\145\x64\137\x75\x73\x65\162"] : '';
        $M2 = array_key_exists("\x6b\x65\x65\160\137\x65\170\x69\x73\x74\151\156\x67\x5f\x75\163\145\x72\163\137\162\157\x6c\x65", $yQ["\x44\x45\x46\101\125\x4c\x54"]) ? $yQ["\104\x45\x46\101\x55\114\124"]["\x6b\145\145\160\x5f\145\170\151\x73\x74\x69\156\x67\x5f\x75\x73\x65\162\163\137\162\157\x6c\145"] : '';
        $LJ = get_saml_roles_to_assign($yQ, $QD, $NF);
        if (!(empty($LJ) && strcmp($yQ["\104\x45\106\x41\x55\114\124"]["\x64\x6f\x6e\164\x5f\143\162\145\141\164\145\x5f\x75\x73\145\x72"], "\143\150\x65\143\x6b\x65\144") == 0)) {
            goto d5;
        }
        $AO = FALSE;
        d5:
        PB:
        goto tz;
        zH:
        $N1 = isset($yQ[$QD]["\144\145\x66\141\x75\154\x74\x5f\162\157\154\145"]) ? $yQ[$QD]["\x64\145\x66\141\x75\x6c\164\x5f\162\x6f\x6c\x65"] : '';
        $HR = isset($yQ[$QD]["\144\157\x6e\x74\137\141\154\x6c\x6f\x77\x5f\165\156\154\x69\x73\x74\x65\x64\137\x75\163\145\162"]) ? $yQ[$QD]["\x64\x6f\x6e\164\x5f\x61\154\154\x6f\x77\x5f\165\156\x6c\151\163\164\145\x64\x5f\165\x73\145\162"] : '';
        $M2 = array_key_exists("\x6b\145\x65\160\x5f\x65\170\x69\x73\164\151\156\147\137\x75\163\145\162\x73\137\162\157\154\x65", $yQ[$QD]) ? $yQ[$QD]["\153\145\145\x70\x5f\x65\170\151\x73\x74\151\156\x67\137\165\x73\x65\x72\x73\137\162\157\154\x65"] : '';
        $LJ = get_saml_roles_to_assign($yQ, $QD, $NF);
        if (!(empty($LJ) && strcmp($yQ[$QD]["\x64\x6f\x6e\164\x5f\143\x72\145\141\x74\145\x5f\x75\163\x65\x72"], "\143\150\x65\143\153\145\144") == 0)) {
            goto Us;
        }
        $AO = FALSE;
        Us:
        tz:
        oI:
        if (!$AO) {
            goto IZ;
        }
        $qU = NULL;
        switch_to_blog($blog_id);
        $qU = mo_saml_add_user_to_blog($eq, $qy, $blog_id);
        $user = get_user_by("\151\144", $qU);
        $lt = assign_roles_to_user($user, $yQ, $blog_id, $NF, $QD);
        if ($lt !== true && !empty($HR) && $HR == "\x63\x68\x65\x63\153\x65\144") {
            goto F2;
        }
        if ($lt !== true && !empty($N1) && $N1 !== "\x66\x61\154\x73\145") {
            goto j1;
        }
        if ($lt !== true) {
            goto TQ;
        }
        goto cl;
        F2:
        $qU = wp_update_user(array("\111\x44" => $qU, "\162\x6f\x6c\x65" => false));
        goto cl;
        j1:
        $qU = wp_update_user(array("\111\x44" => $qU, "\x72\x6f\154\x65" => $N1));
        goto cl;
        TQ:
        $cT = get_site_option("\144\145\146\x61\x75\x6c\x74\137\162\x6f\x6c\x65");
        $qU = wp_update_user(array("\x49\104" => $qU, "\162\x6f\x6c\x65" => $cT));
        cl:
        $sj = $user->{$wpdb->prefix . "\x63\x61\x70\141\142\151\x6c\x69\164\151\x65\163"};
        if (isset($wp_roles)) {
            goto wj;
        }
        $wp_roles = new WP_Roles($QD);
        wj:
        IZ:
        ku:
    }
    A9:
    return $user->ID;
}
function mo_saml_sanitize_username($qy)
{
    $zq = sanitize_user($qy, true);
    $M4 = apply_filters("\160\x72\x65\x5f\165\x73\x65\162\x5f\154\x6f\x67\x69\x6e", $zq);
    $qy = trim($M4);
    return $qy;
}
function mo_saml_map_basic_attributes($user, $ks, $Ow, $HO)
{
    $qU = $user->ID;
    if (empty($ks)) {
        goto n5;
    }
    $qU = wp_update_user(array("\111\104" => $qU, "\x66\151\x72\163\164\137\156\x61\x6d\x65" => $ks));
    n5:
    if (empty($Ow)) {
        goto jr;
    }
    $qU = wp_update_user(array("\x49\104" => $qU, "\154\x61\x73\x74\x5f\x6e\x61\x6d\x65" => $Ow));
    jr:
    if (is_null($HO)) {
        goto EJ;
    }
    update_user_meta($qU, "\155\157\x5f\x73\141\x6d\x6c\137\165\163\145\x72\137\x61\x74\164\x72\x69\142\x75\164\145\x73", $HO);
    $zs = get_site_option("\163\141\x6d\154\137\x61\155\137\x64\151\163\160\x6c\141\171\x5f\156\x61\155\145");
    if (empty($zs)) {
        goto Ur;
    }
    if (strcmp($zs, "\125\x53\105\x52\x4e\x41\115\105") == 0) {
        goto VL;
    }
    if (strcmp($zs, "\x46\x4e\101\x4d\105") == 0 && !empty($ks)) {
        goto iC;
    }
    if (strcmp($zs, "\x4c\x4e\x41\x4d\x45") == 0 && !empty($Ow)) {
        goto Zm;
    }
    if (strcmp($zs, "\106\116\101\x4d\105\137\114\x4e\101\115\x45") == 0 && !empty($Ow) && !empty($ks)) {
        goto zf;
    }
    if (!(strcmp($zs, "\x4c\116\x41\x4d\105\137\x46\116\x41\x4d\105") == 0 && !empty($Ow) && !empty($ks))) {
        goto H2;
    }
    $qU = wp_update_user(array("\111\104" => $qU, "\144\151\163\x70\x6c\141\x79\137\156\x61\155\x65" => $Ow . "\x20" . $ks));
    H2:
    goto PT;
    zf:
    $qU = wp_update_user(array("\111\104" => $qU, "\x64\x69\163\160\x6c\x61\171\137\156\141\155\145" => $ks . "\x20" . $Ow));
    PT:
    goto QE;
    Zm:
    $qU = wp_update_user(array("\111\x44" => $qU, "\x64\151\x73\x70\x6c\141\x79\x5f\x6e\x61\155\145" => $Ow));
    QE:
    goto Rj;
    iC:
    $qU = wp_update_user(array("\x49\104" => $qU, "\144\151\163\160\154\x61\x79\137\x6e\141\155\145" => $ks));
    Rj:
    goto Tq;
    VL:
    $qU = wp_update_user(array("\x49\104" => $qU, "\x64\x69\x73\160\x6c\141\x79\x5f\x6e\x61\155\145" => $user->user_login));
    Tq:
    Ur:
    EJ:
}
function mo_saml_map_custom_attributes($qU, $HO)
{
    if (!get_site_option("\155\x6f\137\x73\x61\155\x6c\137\x63\165\x73\x74\x6f\x6d\x5f\x61\164\164\x72\163\x5f\x6d\x61\x70\160\151\156\x67")) {
        goto qv;
    }
    $Ve = maybe_unserialize(get_site_option("\155\x6f\x5f\163\x61\x6d\154\137\143\165\x73\164\157\155\x5f\141\164\x74\162\163\137\x6d\x61\160\160\151\x6e\147"));
    foreach ($Ve as $Z1 => $zF) {
        if (!array_key_exists($zF, $HO)) {
            goto O6;
        }
        $Mp = false;
        if (!(count($HO[$zF]) == 1)) {
            goto BL;
        }
        $Mp = true;
        BL:
        if (!$Mp) {
            goto LC;
        }
        update_user_meta($qU, $Z1, $HO[$zF][0]);
        goto X0;
        LC:
        $kx = array();
        foreach ($HO[$zF] as $dM) {
            array_push($kx, $dM);
            VP:
        }
        mc:
        update_user_meta($qU, $Z1, $kx);
        X0:
        O6:
        vn:
    }
    F0:
    qv:
}
function mo_saml_restrict_users_based_on_domain($eq)
{
    $wq = get_site_option("\x6d\x6f\x5f\x73\x61\x6d\154\x5f\145\156\141\142\154\x65\137\144\x6f\x6d\141\151\156\x5f\162\145\163\164\162\151\143\x74\151\x6f\x6e\x5f\154\x6f\x67\x69\x6e");
    if (!$wq) {
        goto Np;
    }
    $cG = get_site_option("\x73\141\155\x6c\137\x61\155\137\145\x6d\141\x69\x6c\137\144\157\x6d\x61\x69\x6e\x73");
    $JH = explode("\x3b", $cG);
    $he = explode("\100", $eq);
    $F1 = array_key_exists("\61", $he) ? $he[1] : '';
    $AE = get_site_option("\155\157\137\x73\141\x6d\x6c\x5f\141\154\154\157\167\137\x64\145\x6e\x79\x5f\x75\163\x65\162\137\167\151\164\150\137\144\157\x6d\141\x69\x6e");
    $CN = get_site_option("\x6d\x6f\x5f\163\141\155\154\137\x72\x65\x73\164\x72\151\143\164\x65\x64\137\x64\157\155\x61\151\156\x5f\x65\x72\162\157\162\x5f\x6d\x73\x67");
    if (!empty($CN)) {
        goto U7;
    }
    $CN = "\131\x6f\x75\40\141\162\145\40\156\x6f\164\40\141\154\154\x6f\167\x65\144\x20\164\157\40\x6c\157\147\151\156\x2e\40\x50\154\x65\141\x73\145\40\x63\157\x6e\x74\141\x63\164\x20\171\157\x75\x72\40\x41\144\x6d\x69\x6e\x69\x73\x74\x72\x61\164\157\162\x2e";
    U7:
    if (!empty($AE) && $AE == "\144\x65\156\x79") {
        goto yh;
    }
    if (in_array($F1, $JH)) {
        goto fi;
    }
    wp_die($CN, "\120\x65\x72\155\x69\163\163\x69\x6f\156\x20\104\x65\x6e\x69\145\x64\x20\x45\x72\x72\157\x72\x20\x2d\40\x32");
    fi:
    goto Ez;
    yh:
    if (!in_array($F1, $JH)) {
        goto Pf;
    }
    wp_die($CN, "\120\x65\162\x6d\x69\163\x73\x69\157\x6e\x20\x44\145\156\x69\x65\144\x20\105\162\162\x6f\x72\40\55\x20\x31");
    Pf:
    Ez:
    Np:
}
function mo_saml_set_auth_cookie($user, $Kw, $Xe, $dV)
{
    $qU = $user->ID;
    do_action("\x77\x70\x5f\154\x6f\147\151\156", $user->user_login, $user);
    if (empty($Kw)) {
        goto Rp;
    }
    update_user_meta($qU, "\155\x6f\137\x73\x61\x6d\x6c\137\163\145\x73\163\151\x6f\156\137\x69\156\x64\145\170", $Kw);
    Rp:
    if (empty($Xe)) {
        goto Zd;
    }
    update_user_meta($qU, "\x6d\157\137\163\141\155\154\x5f\156\141\155\x65\x5f\151\144", $Xe);
    Zd:
    if (!(!session_id() || session_id() == '' || !isset($_SESSION))) {
        goto CJ;
    }
    session_start();
    CJ:
    $_SESSION["\x6d\x6f\137\x73\141\155\154"]["\154\x6f\x67\x67\x65\x64\x5f\x69\x6e\137\167\151\x74\150\137\151\x64\160"] = TRUE;
    update_user_meta($qU, "\155\x6f\x5f\163\x61\155\x6c\137\151\144\160\x5f\154\157\147\x69\x6e", "\x74\162\165\145");
    wp_set_current_user($qU);
    $P9 = false;
    $P9 = apply_filters("\x6d\157\137\162\145\155\145\x6d\142\145\162\x5f\155\145", $P9);
    wp_set_auth_cookie($qU, $P9);
    if (!$dV) {
        goto fu;
    }
    do_action("\165\x73\145\162\x5f\162\145\x67\x69\163\x74\x65\162", $qU);
    fu:
}
function mo_saml_post_login_redirection($Wz, $AM)
{
    $uW = mo_saml_get_redirect_url($Wz, $AM);
    wp_redirect($uW);
    die;
}
function mo_saml_get_redirect_url($Wz, $AM)
{
    $Dk = '';
    $pK = get_site_option("\163\x61\155\x6c\x5f\x73\x73\157\x5f\x73\145\x74\164\151\x6e\x67\163");
    $NU = get_current_blog_id();
    if (!(empty($pK[$NU]) && !empty($pK["\104\105\106\101\125\x4c\124"]))) {
        goto bL;
    }
    $pK[$NU] = $pK["\104\x45\106\x41\125\114\124"];
    bL:
    $Pv = isset($pK[$NU]["\155\x6f\x5f\x73\141\155\x6c\137\x72\x65\154\x61\171\x5f\163\164\x61\x74\145"]) ? $pK[$NU]["\155\x6f\x5f\x73\x61\155\x6c\x5f\x72\145\154\141\171\137\163\164\141\164\x65"] : '';
    if (!empty($Pv)) {
        goto V4;
    }
    if (!empty($AM)) {
        goto Tt;
    }
    $Dk = $Wz;
    goto Wu;
    Tt:
    $Dk = $AM;
    Wu:
    goto yq;
    V4:
    $Dk = $Pv;
    yq:
    return $Dk;
}
function check_if_user_allowed_to_login($user, $Wz)
{
    $qU = $user->ID;
    global $wpdb;
    if (get_user_meta($qU, "\155\x6f\137\x73\x61\x6d\x6c\137\165\x73\145\162\x5f\164\x79\x70\145", true)) {
        goto Fv;
    }
    if (get_site_option("\x6d\157\x5f\163\141\155\154\137\x75\163\x72\137\x6c\x6d\164")) {
        goto Ui;
    }
    update_user_meta($qU, "\x6d\x6f\137\x73\x61\x6d\x6c\x5f\x75\163\145\162\x5f\x74\x79\x70\145", "\x73\x73\x6f\137\165\x73\145\x72");
    goto aU;
    Ui:
    $Z1 = get_site_option("\x6d\x6f\x5f\x73\x61\155\154\x5f\143\x75\x73\164\x6f\x6d\x65\x72\x5f\x74\x6f\x6b\x65\156");
    $XO = AESEncryption::decrypt_data(get_site_option("\155\x6f\137\x73\x61\x6d\154\x5f\165\x73\x72\x5f\154\x6d\x74"), $Z1);
    $wQ = "\123\x45\x4c\105\103\124\x20\x43\x4f\125\x4e\x54\x28\x2a\51\40\x46\x52\117\x4d\40" . $wpdb->prefix . "\x75\x73\x65\162\155\x65\164\x61\x20\x57\110\105\122\105\40\155\x65\164\141\137\153\145\171\75\x27\155\157\137\163\141\x6d\154\137\165\x73\x65\x72\137\164\171\160\145\x27";
    $eK = $wpdb->get_var($wQ);
    if ($eK >= $XO) {
        goto Ry;
    }
    update_user_meta($qU, "\x6d\157\137\x73\141\x6d\x6c\x5f\x75\x73\x65\x72\137\x74\x79\160\x65", "\x73\x73\x6f\137\x75\163\x65\162");
    goto Mn;
    Ry:
    if (get_site_option("\x75\x73\x65\162\137\x61\x6c\145\162\x74\137\145\155\x61\151\154\x5f\163\145\x6e\164")) {
        goto l9;
    }
    $EF = new Customersaml();
    $EF->mo_saml_send_user_exceeded_alert_email($XO, $this);
    l9:
    if (is_administrator_user($user)) {
        goto i7;
    }
    wp_redirect($Wz);
    die;
    goto RC;
    i7:
    update_user_meta($qU, "\155\157\137\x73\x61\x6d\x6c\137\165\x73\x65\x72\137\x74\171\x70\x65", "\x73\x73\157\x5f\165\163\145\162");
    RC:
    Mn:
    aU:
    Fv:
}
function check_if_user_allowed_to_login_due_to_role_restriction($NF)
{
    $yQ = maybe_unserialize(get_site_option("\x73\x61\x6d\x6c\x5f\x61\x6d\x5f\x72\x6f\154\145\137\x6d\x61\x70\160\x69\x6e\x67"));
    $tN = Utilities::get_active_sites();
    $xL = get_site_option("\155\x6f\137\141\160\x70\x6c\171\x5f\162\x6f\x6c\145\x5f\x6d\141\x70\160\x69\x6e\147\137\x66\157\162\137\163\151\164\145\x73");
    if ($yQ) {
        goto Yp;
    }
    $yQ = array();
    Yp:
    if (array_key_exists("\104\x45\x46\101\125\114\x54", $yQ)) {
        goto fT;
    }
    $yQ["\104\x45\x46\x41\125\114\124"] = array();
    fT:
    foreach ($tN as $blog_id) {
        if ($xL) {
            goto NX;
        }
        $QD = $blog_id;
        goto zM;
        NX:
        $QD = 0;
        zM:
        if (isset($yQ[$QD])) {
            goto Q1;
        }
        $Is = $yQ["\104\x45\x46\x41\x55\x4c\124"];
        goto l7;
        Q1:
        $Is = $yQ[$QD];
        l7:
        if (empty($Is)) {
            goto N3;
        }
        $qt = isset($Is["\155\x6f\137\x73\x61\x6d\154\137\144\x6f\156\164\x5f\x61\154\x6c\x6f\167\137\x75\x73\145\162\x5f\164\157\154\x6f\147\151\x6e\137\x63\x72\145\x61\x74\145\x5f\x77\x69\164\x68\x5f\x67\151\x76\x65\x6e\x5f\x67\162\x6f\x75\160\x73"]) ? $Is["\x6d\x6f\x5f\x73\x61\x6d\x6c\137\x64\x6f\x6e\164\137\x61\154\154\x6f\x77\137\165\163\145\162\x5f\x74\157\x6c\157\x67\x69\x6e\x5f\x63\162\x65\x61\164\145\x5f\x77\x69\x74\150\x5f\x67\151\166\x65\x6e\x5f\x67\162\157\x75\160\x73"] : '';
        if (!($qt == "\x63\x68\145\143\153\x65\x64")) {
            goto SV;
        }
        if (empty($NF)) {
            goto Lv;
        }
        $uO = $Is["\155\157\137\x73\141\x6d\154\x5f\x72\145\163\x74\162\x69\143\164\x5f\165\x73\145\162\163\x5f\x77\x69\x74\150\x5f\x67\162\157\x75\x70\x73"];
        $yR = explode("\x3b", $uO);
        foreach ($yR as $hU) {
            foreach ($NF as $fG) {
                $fG = trim($fG);
                if (!(!empty($fG) && $fG == $hU)) {
                    goto jb;
                }
                wp_die("\x59\x6f\x75\x20\x61\x72\x65\40\156\157\x74\x20\x61\x75\164\150\x6f\x72\151\x7a\x65\x64\x20\164\x6f\40\x6c\x6f\147\151\x6e\56\x20\120\x6c\x65\141\x73\x65\x20\143\x6f\x6e\x74\x61\x63\x74\40\x79\x6f\165\162\40\141\x64\x6d\x69\156\x69\x73\x74\x72\141\164\x6f\x72\x2e", "\x45\x72\162\x6f\162");
                jb:
                WX:
            }
            RM:
            Ss:
        }
        oA:
        Lv:
        SV:
        N3:
        LJ:
    }
    GF:
}
function assign_roles_to_user($user, $yQ, $blog_id, $NF, $QD)
{
    $lt = false;
    if (!(!empty($NF) && !empty($yQ) && !is_administrator_user($user) && is_user_member_of_blog($user->ID, $blog_id))) {
        goto QK;
    }
    if (!empty($yQ[$QD])) {
        goto Wh;
    }
    if (empty($yQ["\104\x45\x46\x41\x55\x4c\x54"])) {
        goto sd;
    }
    $Is = $yQ["\x44\x45\x46\101\x55\x4c\124"];
    sd:
    goto kN;
    Wh:
    $Is = $yQ[$QD];
    kN:
    if (empty($Is)) {
        goto tF;
    }
    $user->set_role(false);
    $M9 = '';
    $Eb = false;
    unset($Is["\144\145\146\x61\165\154\164\x5f\162\x6f\154\145"]);
    unset($Is["\x64\157\x6e\164\137\x63\x72\145\x61\x74\x65\137\x75\x73\x65\162"]);
    unset($Is["\144\157\156\x74\137\141\x6c\x6c\x6f\167\137\165\156\154\151\x73\164\x65\144\x5f\x75\163\145\x72"]);
    unset($Is["\x6b\145\145\x70\x5f\145\170\151\x73\164\151\x6e\x67\x5f\x75\163\145\x72\x73\x5f\x72\157\154\145"]);
    unset($Is["\x6d\157\137\x73\x61\x6d\154\137\144\157\x6e\x74\137\141\x6c\x6c\x6f\167\137\165\163\145\162\x5f\164\x6f\x6c\157\x67\151\156\x5f\143\x72\x65\x61\164\145\x5f\x77\151\164\x68\137\147\151\166\145\x6e\x5f\x67\x72\157\x75\x70\x73"]);
    unset($Is["\x6d\x6f\137\x73\x61\155\154\137\x72\x65\x73\x74\x72\151\143\x74\x5f\165\163\145\x72\x73\137\167\151\x74\x68\137\x67\162\x6f\x75\160\x73"]);
    foreach ($Is as $Ps => $g6) {
        $yR = explode("\73", $g6);
        foreach ($yR as $hU) {
            if (!(!empty($hU) && in_array($hU, $NF))) {
                goto VS;
            }
            $lt = true;
            $user->add_role($Ps);
            VS:
            ec:
        }
        LD:
        zW:
    }
    nX:
    tF:
    QK:
    $WD = get_site_option("\x6d\x6f\137\x73\141\155\x6c\x5f\x73\165\x70\145\162\137\141\x64\x6d\151\x6e\x5f\162\x6f\154\145\137\155\x61\x70\160\151\156\147");
    $xf = array();
    if (empty($WD)) {
        goto iH;
    }
    $xf = explode("\x3b", $WD);
    iH:
    if (!(!empty($NF) && !empty($xf))) {
        goto t1;
    }
    foreach ($xf as $hU) {
        if (!in_array($hU, $NF)) {
            goto Tb;
        }
        grant_super_admin($user->ID);
        Tb:
        Mj:
    }
    DX:
    t1:
    return $lt;
}
function get_saml_roles_to_assign($yQ, $blog_id, $NF)
{
    $LJ = array();
    if (!(!empty($NF) && !empty($yQ))) {
        goto jn;
    }
    if (!empty($yQ[$blog_id])) {
        goto Dq;
    }
    if (empty($yQ["\104\105\106\x41\x55\x4c\x54"])) {
        goto Cq;
    }
    $Is = $yQ["\x44\x45\106\x41\x55\x4c\x54"];
    Cq:
    goto si;
    Dq:
    $Is = $yQ[$blog_id];
    si:
    if (empty($Is)) {
        goto NB;
    }
    unset($Is["\144\x65\146\141\x75\154\x74\137\x72\x6f\154\145"]);
    unset($Is["\144\x6f\156\164\x5f\143\x72\x65\141\164\x65\137\165\x73\145\162"]);
    unset($Is["\x64\x6f\156\x74\137\x61\154\x6c\x6f\167\137\165\x6e\x6c\151\163\164\x65\x64\x5f\x75\x73\145\x72"]);
    unset($Is["\x6b\145\x65\x70\x5f\x65\170\151\x73\x74\151\156\147\137\165\x73\145\x72\163\x5f\162\x6f\x6c\x65"]);
    unset($Is["\x6d\157\x5f\x73\141\155\x6c\x5f\x64\157\x6e\x74\137\141\x6c\x6c\157\167\137\165\163\145\x72\137\164\x6f\154\157\147\x69\156\137\143\162\x65\141\x74\145\137\167\151\164\150\137\x67\151\x76\145\x6e\x5f\x67\x72\x6f\165\x70\163"]);
    unset($Is["\155\157\x5f\x73\141\155\x6c\137\x72\x65\x73\x74\162\151\143\x74\137\x75\x73\145\x72\x73\137\x77\151\164\150\x5f\x67\x72\x6f\165\x70\163"]);
    foreach ($Is as $Ps => $g6) {
        $yR = explode("\x3b", $g6);
        foreach ($yR as $hU) {
            if (!(!empty($hU) and in_array($hU, $NF))) {
                goto n7;
            }
            array_push($LJ, $Ps);
            n7:
            q2:
        }
        a2:
        S_:
    }
    gH:
    NB:
    jn:
    return $LJ;
}
function is_administrator_user($user)
{
    $lH = $user->roles;
    if (!is_null($lH) && in_array("\x61\x64\155\151\156\151\x73\x74\162\x61\164\157\x72", $lH)) {
        goto v2;
    }
    return false;
    goto NG;
    v2:
    return true;
    NG:
}
function mo_saml_is_customer_registered()
{
    $sf = get_site_option("\x6d\157\x5f\163\x61\155\154\137\x61\144\155\151\156\137\145\x6d\141\151\x6c");
    $Z8 = get_site_option("\x6d\157\137\x73\x61\x6d\154\137\x61\144\155\x69\x6e\137\143\165\x73\x74\157\x6d\x65\x72\x5f\153\x65\171");
    if (!$sf || !$Z8 || !is_numeric(trim($Z8))) {
        goto n3;
    }
    return 1;
    goto v9;
    n3:
    return 0;
    v9:
}
function mo_saml_is_customer_license_verified()
{
    $Z1 = get_site_option("\x6d\157\x5f\x73\x61\155\x6c\x5f\x63\165\x73\164\157\x6d\145\x72\137\164\x6f\x6b\x65\156");
    $MI = AESEncryption::decrypt_data(get_site_option("\164\137\163\151\x74\x65\x5f\163\164\x61\164\x75\163"), $Z1);
    $N3 = get_site_option("\x73\x6d\x6c\x5f\154\x6b");
    $sf = get_site_option("\155\x6f\x5f\x73\141\x6d\x6c\x5f\141\144\x6d\151\x6e\137\x65\x6d\141\151\x6c");
    $Z8 = get_site_option("\155\x6f\137\x73\x61\x6d\154\137\x61\x64\155\151\x6e\137\x63\165\x73\164\x6f\155\145\x72\137\x6b\145\x79");
    $Kp = AESEncryption::decrypt_data(get_site_option("\156\x6f\137\x73\142\x73"), $Z1);
    $vf = false;
    if (!get_site_option("\156\157\x5f\163\x62\163")) {
        goto g5;
    }
    $mM = Utilities::get_sites();
    $vf = $Kp < count($mM);
    g5:
    if ($MI != "\x74\x72\x75\145" && !$N3 || !$sf || !$Z8 || !is_numeric(trim($Z8)) || $vf) {
        goto gq;
    }
    return 1;
    goto Qj;
    gq:
    return 0;
    Qj:
}
function show_status_error($j8, $AM)
{
    if ($AM == "\x74\x65\163\x74\126\141\x6c\x69\x64\x61\164\x65") {
        goto QN;
    }
    wp_die("\x57\145\40\x63\157\165\154\144\x20\x6e\157\x74\x20\x73\x69\x67\x6e\40\171\x6f\165\40\x69\x6e\56\40\x50\x6c\x65\141\x73\x65\x20\x63\x6f\x6e\164\x61\143\x74\40\171\x6f\x75\x72\x20\101\144\x6d\x69\x6e\x69\163\164\162\x61\164\157\x72\56", "\105\162\x72\157\162\x3a\x20\x49\x6e\x76\x61\154\151\x64\x20\123\x41\x4d\x4c\40\122\x65\x73\160\157\x6e\163\145\x20\123\x74\141\164\x75\x73");
    goto lo;
    QN:
    echo "\74\144\x69\x76\x20\x73\164\x79\154\x65\75\42\146\157\156\164\x2d\x66\141\155\151\x6c\x79\72\x43\141\154\x69\142\162\x69\73\160\141\x64\144\151\156\147\72\x30\x20\x33\x25\x3b\x22\x3e";
    echo "\x3c\x64\151\x76\x20\x73\164\x79\154\x65\75\42\x63\157\x6c\157\162\72\x20\x23\x61\x39\64\x34\x34\x32\x3b\x62\x61\x63\x6b\147\162\x6f\165\x6e\144\x2d\x63\157\x6c\157\162\x3a\40\43\x66\62\144\x65\x64\145\x3b\160\x61\x64\x64\151\156\147\72\x20\x31\65\160\170\x3b\x6d\141\x72\147\x69\156\x2d\x62\157\164\164\x6f\x6d\72\40\62\x30\160\x78\73\x74\145\170\164\x2d\141\x6c\x69\147\156\72\x63\x65\156\x74\x65\162\x3b\142\x6f\162\x64\x65\x72\72\x31\x70\x78\x20\163\157\154\x69\x64\40\43\105\66\102\63\102\62\73\x66\157\156\164\55\163\151\x7a\145\72\x31\70\160\x74\73\42\76\40\x45\122\x52\x4f\x52\x3c\x2f\x64\x69\x76\76\12\11\x9\11\x9\11\x9\x9\x3c\x64\x69\x76\x20\x73\164\171\154\x65\75\x22\143\x6f\x6c\157\x72\72\40\43\141\71\64\x34\x34\x32\73\146\157\x6e\164\x2d\x73\151\x7a\x65\x3a\x31\64\160\164\73\40\x6d\x61\162\x67\x69\156\x2d\x62\x6f\164\164\157\155\x3a\62\60\160\170\73\42\x3e\x3c\x70\x3e\74\163\x74\x72\x6f\x6e\147\76\x45\x72\x72\x6f\162\x3a\x20\74\x2f\163\164\162\x6f\156\147\76\40\111\156\x76\x61\x6c\x69\x64\40\123\x41\115\114\x20\x52\x65\x73\160\x6f\x6e\163\145\40\x53\x74\141\164\165\x73\56\74\57\x70\x3e\12\x9\x9\x9\11\x9\11\11\11\x3c\160\76\x3c\x73\x74\x72\157\156\x67\x3e\103\141\165\x73\x65\x73\74\57\x73\164\x72\157\156\x67\x3e\72\40\111\144\x65\156\164\151\x74\x79\40\120\x72\x6f\x76\x69\x64\145\x72\x20\x68\x61\163\40\163\x65\156\x74\40\x27" . $j8 . "\47\x20\x73\x74\x61\164\x75\163\x20\143\157\144\x65\x20\151\156\40\123\x41\115\x4c\40\122\145\x73\160\x6f\x6e\163\145\x2e\x20\x3c\x2f\160\76\xa\x9\11\11\11\11\11\x9\x9\x3c\x70\76\74\163\x74\162\x6f\156\x67\x3e\122\x65\141\x73\x6f\x6e\x3c\x2f\163\164\162\x6f\x6e\x67\76\72\x20" . get_status_message($j8) . "\74\57\x70\76\x3c\142\162\x3e";
    if (empty($RL)) {
        goto Dj;
    }
    echo "\x3c\x70\x3e\74\163\164\162\157\x6e\147\x3e\123\164\x61\x74\165\163\40\115\x65\163\163\x61\147\x65\40\x69\x6e\40\x74\150\145\40\x53\101\115\114\x20\122\x65\163\160\x6f\x6e\x73\x65\x3a\74\x2f\x73\x74\162\x6f\x6e\147\x3e\40\74\x62\x72\57\76" . $RL . "\74\x2f\x70\76\74\142\x72\76";
    Dj:
    echo "\xa\11\x9\11\x9\11\11\x9\x3c\57\x64\x69\x76\x3e\xa\12\x9\11\x9\x9\11\11\11\74\144\151\166\x20\163\164\x79\x6c\x65\x3d\x22\155\x61\x72\x67\151\x6e\x3a\x33\45\73\x64\x69\x73\x70\154\x61\171\x3a\142\154\x6f\143\153\x3b\x74\145\x78\164\x2d\x61\154\x69\x67\156\72\143\145\x6e\x74\145\x72\73\x22\x3e\12\x9\x9\x9\11\11\x9\x9\11\74\x64\151\x76\40\163\164\171\154\145\x3d\x22\x6d\141\x72\x67\151\156\72\63\45\x3b\144\x69\163\x70\x6c\141\171\72\142\x6c\x6f\143\x6b\x3b\x74\145\170\x74\55\x61\x6c\x69\x67\x6e\x3a\143\x65\x6e\x74\145\162\73\x22\x3e\x3c\151\x6e\160\165\164\40\x73\x74\x79\x6c\145\75\x22\160\x61\x64\144\x69\x6e\x67\72\x31\x25\x3b\167\x69\144\164\150\72\61\60\x30\x70\170\x3b\142\x61\x63\153\x67\x72\x6f\x75\156\144\72\x20\x23\x30\x30\x39\x31\x43\104\x20\156\157\156\x65\x20\162\x65\x70\x65\141\164\40\x73\x63\x72\157\x6c\154\x20\x30\x25\x20\x30\x25\x3b\143\x75\x72\x73\157\x72\72\x20\160\x6f\x69\156\164\x65\162\x3b\146\x6f\x6e\164\x2d\163\x69\x7a\x65\72\61\65\x70\170\x3b\x62\157\x72\x64\145\162\x2d\x77\151\144\x74\x68\72\x20\61\x70\x78\73\142\157\x72\x64\x65\x72\x2d\163\x74\x79\x6c\145\72\x20\x73\x6f\x6c\151\x64\x3b\142\157\x72\144\x65\x72\55\x72\141\144\151\165\x73\x3a\x20\63\160\x78\73\167\150\x69\164\x65\x2d\x73\160\x61\x63\145\72\40\x6e\x6f\x77\x72\141\160\73\x62\157\x78\55\x73\151\172\x69\x6e\147\72\x20\x62\x6f\162\144\x65\162\x2d\142\x6f\170\73\142\157\x72\144\145\x72\55\143\x6f\154\x6f\x72\x3a\40\43\60\60\x37\x33\101\x41\x3b\x62\x6f\170\x2d\163\x68\x61\x64\157\x77\x3a\x20\60\160\x78\x20\x31\160\x78\x20\x30\x70\x78\x20\162\x67\x62\141\x28\61\62\x30\x2c\x20\62\x30\x30\x2c\x20\62\x33\60\54\40\x30\56\x36\x29\40\x69\x6e\x73\145\x74\73\143\157\x6c\x6f\162\x3a\x20\x23\x46\106\x46\x3b\42\164\171\160\x65\x3d\42\x62\165\164\x74\x6f\156\x22\40\x76\x61\x6c\165\145\x3d\42\x44\157\x6e\x65\42\40\x6f\x6e\x43\x6c\x69\143\153\x3d\42\x73\145\x6c\x66\x2e\x63\154\157\x73\145\x28\x29\x3b\x22\76\x3c\x2f\x64\x69\x76\76";
    die;
    lo:
}
function addLink($oo, $Sp)
{
    $WV = "\x3c\x61\40\150\162\x65\146\75\x22" . $Sp . "\42\76" . $oo . "\x3c\x2f\141\x3e";
    return $WV;
}
function get_status_message($j8)
{
    switch ($j8) {
        case "\x52\145\x71\x75\145\163\164\x65\162":
            return "\124\150\x65\x20\162\x65\x71\x75\145\x73\164\x20\x63\x6f\x75\x6c\144\x20\x6e\157\164\x20\x62\145\40\160\145\162\146\157\x72\155\x65\x64\40\144\x75\145\x20\164\x6f\40\141\x6e\x20\145\x72\x72\157\x72\x20\157\x6e\40\164\150\145\x20\160\141\x72\164\x20\x6f\146\40\164\150\x65\40\x72\x65\161\x75\145\163\164\x65\x72\x2e";
            goto UW;
        case "\x52\145\x73\160\x6f\156\x64\145\162":
            return "\124\150\145\x20\x72\x65\x71\x75\x65\x73\x74\x20\143\157\165\154\x64\40\x6e\157\164\40\x62\145\40\160\x65\x72\146\157\162\155\x65\144\x20\x64\x75\x65\40\x74\x6f\40\141\x6e\x20\x65\162\162\x6f\162\40\x6f\156\40\164\x68\x65\40\x70\x61\162\x74\x20\157\146\40\x74\150\x65\40\x53\x41\x4d\114\x20\x72\145\163\x70\x6f\x6e\x64\145\x72\40\x6f\162\x20\123\x41\115\x4c\40\141\x75\164\x68\157\162\151\164\x79\56";
            goto UW;
        case "\x56\x65\x72\163\x69\157\x6e\x4d\x69\x73\x6d\141\x74\143\150":
            return "\124\150\x65\x20\123\101\115\114\x20\x72\145\x73\x70\157\156\144\145\x72\x20\x63\157\165\154\144\x20\156\x6f\164\x20\x70\x72\157\143\145\163\163\x20\164\x68\x65\x20\162\x65\161\x75\x65\163\x74\x20\x62\x65\143\141\165\163\x65\40\x74\150\x65\40\x76\145\x72\163\x69\x6f\156\40\157\146\x20\x74\150\145\x20\162\x65\x71\x75\145\163\x74\x20\x6d\x65\x73\x73\141\x67\145\x20\167\x61\163\x20\151\156\x63\x6f\x72\162\145\143\x74\56";
            goto UW;
        default:
            return "\x55\156\153\156\157\x77\156";
    }
    zN:
    UW:
}
function saml_get_current_page_url()
{
    $xM = $_SERVER["\x48\124\124\120\x5f\x48\x4f\123\124"];
    if (!(substr($xM, -1) == "\57")) {
        goto xy;
    }
    $xM = substr($xM, 0, -1);
    xy:
    $EJ = $_SERVER["\x52\x45\121\x55\105\x53\x54\x5f\125\x52\111"];
    if (!(substr($EJ, 0, 1) == "\57")) {
        goto hS;
    }
    $EJ = substr($EJ, 1);
    hS:
    $eQ = isset($_SERVER["\x48\124\x54\120\x53"]) && strcasecmp($_SERVER["\x48\124\124\x50\x53"], "\157\156") == 0;
    $Hr = "\x68\164\x74\160" . ($eQ ? "\163" : '') . "\x3a\57\x2f" . $xM . "\x2f" . $EJ;
    return $Hr;
}
function get_network_site_url()
{
    $Oy = network_site_url();
    if (!(substr($Oy, -1) == "\x2f")) {
        goto oQ;
    }
    $Oy = substr($Oy, 0, -1);
    oQ:
    return $Oy;
}
function get_current_base_url()
{
    return sprintf("\x25\x73\72\57\57\45\163\x2f", isset($_SERVER["\x48\x54\x54\120\x53"]) && $_SERVER["\x48\x54\x54\x50\x53"] != "\157\x66\146" ? "\x68\164\x74\x70\x73" : "\x68\164\x74\x70", $_SERVER["\110\x54\x54\120\x5f\110\117\x53\124"]);
}
add_action("\x77\x69\144\147\145\164\x73\x5f\151\x6e\151\164", function () {
    register_widget("\x6d\x6f\x5f\x6c\157\147\x69\x6e\137\167\x69\144");
});
add_action("\x69\156\151\164", "\x6d\x6f\x5f\154\157\147\151\156\137\166\x61\x6c\151\x64\141\164\x65");
?>
