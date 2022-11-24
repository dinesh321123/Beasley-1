<?php


include_once dirname(__FILE__) . "\x2f\125\x74\x69\x6c\x69\x74\x69\x65\x73\56\x70\x68\160";
include_once dirname(__FILE__) . "\x2f\x52\145\x73\x70\x6f\x6e\163\145\x2e\x70\x68\x70";
include_once dirname(__FILE__) . "\57\x4c\x6f\x67\x6f\x75\164\x52\x65\161\165\x65\x73\164\56\x70\150\x70";
require_once dirname(__FILE__) . "\x2f\x69\156\x63\x6c\x75\x64\145\x73\x2f\x6c\x69\142\x2f\x65\x6e\x63\162\x79\160\x74\151\157\156\x2e\160\150\160";
include_once "\170\155\154\x73\x65\143\154\151\142\163\x2e\160\150\x70";
use RobRichards\XMLSecLibs\XMLSecurityKey;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecEnc;
class mo_login_wid extends WP_Widget
{
    public function __construct()
    {
        $aC = get_site_option("\x73\141\x6d\x6c\x5f\x69\144\x65\x6e\x74\151\164\171\137\x6e\x61\155\145");
        parent::__construct("\123\x61\155\x6c\x5f\x4c\157\147\x69\156\x5f\x57\151\x64\x67\145\x74", "\x4c\x6f\x67\151\x6e\x20\167\x69\164\150\x20" . $aC, array("\x64\x65\163\x63\162\151\x70\164\151\157\156" => __("\x54\x68\151\x73\x20\151\x73\x20\x61\40\x6d\151\x6e\x69\x4f\162\141\x6e\147\x65\40\123\x41\x4d\114\x20\154\x6f\x67\x69\x6e\40\x77\151\144\x67\145\x74\x2e", "\x6d\157\x73\x61\x6d\154")));
    }
    public function widget($Yb, $n4)
    {
        extract($Yb);
        $gd = apply_filters("\167\151\144\147\x65\x74\137\x74\x69\x74\154\x65", $n4["\167\151\144\137\x74\151\x74\x6c\x65"]);
        echo $Yb["\142\145\146\157\x72\x65\137\x77\151\144\x67\145\164"];
        if (empty($gd)) {
            goto qS;
        }
        echo $Yb["\x62\x65\x66\157\162\x65\x5f\x74\x69\x74\154\x65"] . $gd . $Yb["\141\146\164\x65\x72\137\x74\x69\164\x6c\x65"];
        qS:
        $this->loginForm();
        echo $Yb["\141\146\x74\145\x72\x5f\x77\x69\x64\x67\145\164"];
    }
    public function update($Wo, $IN)
    {
        $n4 = array();
        $n4["\x77\151\x64\x5f\x74\151\x74\154\x65"] = strip_tags($Wo["\x77\x69\x64\x5f\164\x69\x74\154\x65"]);
        return $n4;
    }
    public function form($n4)
    {
        $gd = '';
        if (!array_key_exists("\x77\x69\x64\137\x74\151\x74\x6c\x65", $n4)) {
            goto zm;
        }
        $gd = $n4["\x77\x69\x64\x5f\164\x69\164\x6c\x65"];
        zm:
        echo "\15\12\11\x9\74\160\x3e\74\x6c\x61\142\x65\154\40\x66\157\162\x3d\x22" . $this->get_field_id("\x77\151\144\x5f\x74\x69\x74\x6c\145") . "\x20\x22\x3e" . _e("\x54\151\x74\154\x65\72") . "\x20\74\x2f\154\x61\142\x65\154\76\15\xa\11\11\x9\x3c\x69\156\x70\165\164\40\x63\x6c\141\x73\x73\75\x22\x77\x69\x64\x65\x66\141\x74\x22\40\x69\x64\75\42" . $this->get_field_id("\167\151\144\137\x74\151\164\x6c\x65") . "\42\x20\156\141\155\145\75\42" . $this->get_field_name("\x77\x69\x64\x5f\164\x69\164\x6c\145") . "\42\40\x74\171\160\x65\x3d\x22\x74\x65\170\164\x22\x20\x76\141\x6c\x75\145\x3d\x22" . $gd . "\x22\x20\57\76\xd\12\11\11\74\57\160\x3e";
    }
    public function loginForm()
    {
        global $post;
        $sT = get_site_option("\x73\141\x6d\x6c\x5f\x73\x73\x6f\x5f\163\145\164\164\151\x6e\x67\163");
        $LQ = get_current_blog_id();
        $bO = Utilities::get_active_sites();
        if (in_array($LQ, $bO)) {
            goto ur;
        }
        return;
        ur:
        if (!(empty($sT[$LQ]) && !empty($sT["\x44\105\106\101\x55\x4c\124"]))) {
            goto Zi;
        }
        $sT[$LQ] = $sT["\104\105\x46\101\x55\114\x54"];
        Zi:
        if (!is_user_logged_in()) {
            goto uA;
        }
        $current_user = wp_get_current_user();
        $Lb = "\x48\x65\x6c\154\x6f\54";
        if (empty($sT[$LQ]["\x6d\157\137\163\x61\155\x6c\x5f\143\x75\163\x74\157\155\137\147\162\x65\x65\x74\151\156\147\137\x74\145\170\164"])) {
            goto Hw;
        }
        $Lb = $sT[$LQ]["\x6d\x6f\137\x73\141\x6d\x6c\x5f\x63\165\163\164\157\x6d\137\147\x72\145\145\x74\151\156\147\x5f\164\145\x78\x74"];
        Hw:
        $we = '';
        if (empty($sT[$LQ]["\x6d\x6f\x5f\x73\141\x6d\154\x5f\x67\162\145\145\164\x69\x6e\x67\137\x6e\141\x6d\145"])) {
            goto wS;
        }
        switch ($sT[$LQ]["\155\157\137\x73\x61\155\x6c\137\x67\162\145\x65\x74\151\x6e\147\137\x6e\141\x6d\145"]) {
            case "\x55\123\105\122\x4e\x41\x4d\x45":
                $we = $current_user->user_login;
                goto oW;
            case "\105\x4d\x41\x49\x4c":
                $we = $current_user->user_email;
                goto oW;
            case "\106\x4e\x41\x4d\105":
                $we = $current_user->user_firstname;
                goto oW;
            case "\114\116\x41\115\x45":
                $we = $current_user->user_lastname;
                goto oW;
            case "\106\x4e\x41\x4d\x45\x5f\x4c\116\101\x4d\105":
                $we = $current_user->user_firstname . "\x20" . $current_user->user_lastname;
                goto oW;
            case "\x4c\116\x41\115\x45\137\x46\116\101\x4d\x45":
                $we = $current_user->user_lastname . "\x20" . $current_user->user_firstname;
                goto oW;
            default:
                $we = $current_user->user_login;
        }
        Dh:
        oW:
        wS:
        if (!empty(trim($we))) {
            goto i8;
        }
        $we = $current_user->user_login;
        i8:
        $Ks = $Lb . "\40" . $we;
        $vg = "\114\157\x67\x6f\x75\x74";
        if (empty($sT[$LQ]["\155\x6f\x5f\x73\141\x6d\154\137\143\x75\x73\x74\157\155\137\x6c\x6f\147\157\x75\164\x5f\x74\x65\x78\x74"])) {
            goto AN;
        }
        $vg = $sT[$LQ]["\155\x6f\137\163\x61\155\154\137\143\x75\x73\164\x6f\155\x5f\154\157\x67\157\165\164\137\x74\145\170\x74"];
        AN:
        echo $Ks . "\40\x7c\40\x3c\x61\40\x68\162\145\146\75\x22" . wp_logout_url(home_url()) . "\x22\40\x74\x69\x74\154\145\75\42\154\x6f\x67\x6f\x75\164\x22\40\76" . $vg . "\x3c\57\x61\x3e\74\57\x6c\151\76";
        goto SD;
        uA:
        echo "\xd\12\x9\11\x9\74\163\x63\x72\x69\160\164\76\15\12\11\11\11\11\x66\x75\156\143\x74\x69\x6f\x6e\x20\163\165\x62\155\151\164\123\141\x6d\x6c\x46\157\x72\x6d\x28\51\173\x20\144\x6f\x63\x75\155\x65\x6e\164\x2e\147\145\x74\105\154\145\155\145\156\x74\x42\171\111\144\x28\42\x6c\x6f\x67\x69\156\42\x29\56\x73\x75\x62\155\151\164\50\51\73\x20\x7d\15\12\11\x9\x9\74\57\x73\143\x72\x69\160\164\76\xd\12\11\11\x9\74\x66\x6f\162\x6d\40\x6e\141\x6d\145\75\x22\154\x6f\147\x69\156\42\40\151\x64\75\42\154\157\x67\151\x6e\42\x20\155\145\164\x68\x6f\x64\x3d\x22\x70\157\163\x74\x22\x20\x61\143\164\x69\x6f\156\75\x22\42\x3e\15\12\x9\11\x9\11\x3c\151\x6e\160\x75\x74\40\x74\x79\x70\145\x3d\x22\x68\151\144\x64\145\x6e\x22\40\x6e\x61\155\x65\75\42\x6f\160\x74\151\x6f\156\42\40\166\141\x6c\x75\145\75\42\163\141\x6d\154\x5f\165\163\145\162\137\x6c\x6f\x67\x69\x6e\x22\40\x2f\76\15\xa\xd\xa\11\x9\x9\11\74\x66\157\x6e\164\x20\163\151\x7a\x65\x3d\42\53\x31\42\x20\163\164\x79\x6c\145\75\x22\x76\x65\x72\164\x69\143\141\x6c\x2d\x61\x6c\151\x67\x6e\x3a\x74\x6f\160\73\x22\76\x20\74\57\x66\x6f\x6e\164\x3e";
        $Xs = get_site_option("\x73\x61\x6d\154\x5f\151\x64\145\x6e\164\x69\x74\171\137\x6e\x61\x6d\145");
        $D8 = get_site_option("\x73\x61\x6d\x6c\137\170\x35\x30\x39\137\143\145\162\164\151\146\x69\x63\x61\164\145");
        if (!empty($Xs) && !empty($D8)) {
            goto bz;
        }
        echo "\x50\x6c\x65\141\x73\x65\40\x63\157\x6e\x66\151\147\x75\x72\145\x20\164\150\x65\x20\155\151\156\151\117\162\141\156\x67\145\x20\123\101\x4d\x4c\x20\120\x6c\x75\x67\151\x6e\x20\x66\151\162\163\x74\56";
        goto jb;
        bz:
        $b6 = "\x4c\x6f\147\x69\x6e\x20\167\x69\164\x68\40\43\43\x49\x44\x50\x23\43";
        if (empty($sT[$LQ]["\155\x6f\x5f\163\141\155\x6c\137\x63\x75\x73\164\x6f\x6d\137\x6c\157\147\x69\156\137\164\145\x78\x74"])) {
            goto Ge;
        }
        $b6 = $sT[$LQ]["\155\x6f\137\x73\x61\x6d\x6c\137\x63\165\163\164\x6f\x6d\x5f\x6c\157\147\151\x6e\x5f\x74\x65\x78\164"];
        Ge:
        $b6 = str_replace("\43\x23\111\104\x50\x23\43", $Xs, $b6);
        $Jr = false;
        if (!(isset($sT[$LQ]["\x6d\x6f\137\163\x61\x6d\x6c\137\165\163\x65\x5f\142\165\164\x74\x6f\156\x5f\141\x73\137\167\x69\144\147\145\164"]) && $sT[$LQ]["\x6d\x6f\137\x73\141\155\x6c\137\165\x73\x65\x5f\x62\x75\164\164\x6f\x6e\137\x61\x73\137\x77\151\x64\147\x65\164"] == "\164\x72\x75\145")) {
            goto cs;
        }
        $Jr = true;
        cs:
        if (!$Jr) {
            goto iH;
        }
        $dC = isset($sT[$LQ]["\155\x6f\x5f\x73\141\155\154\137\142\165\x74\164\x6f\156\x5f\x77\151\x64\x74\150"]) ? $sT[$LQ]["\x6d\x6f\137\163\141\x6d\x6c\x5f\142\165\164\x74\x6f\x6e\137\167\151\x64\164\150"] : "\61\x30\x30";
        $M2 = isset($sT[$LQ]["\x6d\x6f\137\x73\141\x6d\154\x5f\x62\165\x74\164\x6f\156\x5f\x68\x65\x69\x67\x68\164"]) ? $sT[$LQ]["\155\x6f\137\x73\x61\155\154\137\x62\x75\x74\x74\x6f\x6e\x5f\x68\x65\x69\x67\x68\164"] : "\65\60";
        $YW = isset($sT[$LQ]["\155\157\x5f\163\x61\x6d\154\x5f\x62\x75\x74\x74\x6f\156\x5f\163\151\x7a\x65"]) ? $sT[$LQ]["\155\x6f\x5f\163\141\155\x6c\x5f\142\165\164\x74\157\156\137\163\x69\x7a\145"] : "\x35\60";
        $gZ = isset($sT[$LQ]["\x6d\157\x5f\x73\141\155\x6c\137\142\165\164\x74\157\156\137\x63\x75\162\166\x65"]) ? $sT[$LQ]["\x6d\157\137\163\141\x6d\x6c\137\142\x75\x74\164\x6f\156\x5f\x63\x75\x72\166\x65"] : "\x35";
        $op = isset($sT[$LQ]["\155\x6f\137\x73\x61\155\154\137\142\165\x74\x74\157\156\x5f\143\157\x6c\x6f\x72"]) ? $sT[$LQ]["\x6d\157\x5f\163\x61\155\x6c\x5f\x62\x75\164\x74\157\x6e\137\x63\x6f\x6c\157\x72"] : "\60\x30\70\x35\142\141";
        $oy = isset($sT[$LQ]["\x6d\x6f\x5f\x73\x61\x6d\154\x5f\142\x75\x74\x74\157\x6e\x5f\164\150\x65\155\145"]) ? $sT[$LQ]["\155\x6f\x5f\163\141\155\x6c\137\x62\165\x74\164\157\x6e\137\164\150\145\155\145"] : "\154\x6f\156\x67\142\165\164\x74\157\156";
        $rh = isset($sT[$LQ]["\155\157\137\x73\x61\x6d\x6c\x5f\142\x75\164\x74\x6f\x6e\137\164\x65\170\x74"]) ? $sT[$LQ]["\155\x6f\x5f\163\x61\155\x6c\x5f\142\x75\x74\164\x6f\x6e\137\164\145\170\164"] : (get_site_option("\x73\141\x6d\x6c\x5f\x69\x64\145\x6e\x74\x69\164\171\x5f\156\141\155\x65") ? get_site_option("\163\x61\155\154\137\x69\144\145\156\x74\151\164\171\137\x6e\x61\x6d\145") : "\114\157\x67\x69\156");
        $V1 = isset($sT[$LQ]["\x6d\x6f\x5f\163\141\155\154\x5f\x66\x6f\x6e\164\137\x63\x6f\154\x6f\162"]) ? $sT[$LQ]["\x6d\157\137\x73\x61\155\x6c\137\x66\x6f\x6e\164\137\143\x6f\x6c\x6f\162"] : "\146\146\x66\146\146\x66";
        $xb = isset($sT[$LQ]["\x6d\x6f\x5f\x73\x61\155\x6c\137\146\x6f\156\x74\x5f\163\151\x7a\145"]) ? $sT[$LQ]["\x6d\157\137\x73\x61\x6d\x6c\x5f\146\x6f\x6e\164\x5f\x73\151\x7a\x65"] : "\62\x30";
        $f2 = isset($sT[$LQ]["\x73\x73\x6f\x5f\142\165\x74\164\157\x6e\137\x6c\157\147\x69\x6e\x5f\x66\157\162\x6d\137\x70\157\163\151\x74\x69\x6f\x6e"]) ? $sT[$LQ]["\x73\x73\x6f\x5f\x62\165\164\164\x6f\156\137\154\157\x67\x69\156\137\146\157\162\155\x5f\160\157\x73\151\164\151\x6f\156"] : "\141\142\x6f\x76\x65";
        $b6 = "\x3c\151\156\x70\165\x74\40\x74\x79\160\145\x3d\x22\142\165\x74\x74\157\156\42\40\x6e\141\x6d\145\x3d\42\x6d\x6f\x5f\163\x61\x6d\154\137\167\160\137\x73\163\157\x5f\x62\165\x74\x74\157\156\42\x20\166\x61\x6c\x75\x65\x3d\x22" . $rh . "\x22\40\x73\x74\171\154\x65\75\42";
        $V7 = '';
        if ($oy == "\154\157\156\147\x62\165\164\x74\157\156") {
            goto lh;
        }
        if ($oy == "\143\x69\x72\143\x6c\x65") {
            goto ru;
        }
        if ($oy == "\x6f\x76\x61\x6c") {
            goto vJ;
        }
        if ($oy == "\x73\161\x75\x61\162\145") {
            goto FR;
        }
        goto w2;
        ru:
        $V7 = $V7 . "\167\151\144\x74\x68\72" . $YW . "\x70\x78\73";
        $V7 = $V7 . "\x68\x65\151\147\150\x74\x3a" . $YW . "\160\170\73";
        $V7 = $V7 . "\x62\x6f\162\x64\145\x72\55\162\x61\x64\x69\165\x73\x3a\71\x39\x39\x70\x78\x3b";
        goto w2;
        vJ:
        $V7 = $V7 . "\x77\151\144\164\150\72" . $YW . "\160\170\x3b";
        $V7 = $V7 . "\x68\x65\151\x67\x68\x74\x3a" . $YW . "\160\x78\x3b";
        $V7 = $V7 . "\x62\x6f\x72\x64\x65\162\55\x72\x61\x64\151\165\x73\72\65\x70\170\73";
        goto w2;
        FR:
        $V7 = $V7 . "\167\x69\144\x74\x68\72" . $YW . "\x70\170\x3b";
        $V7 = $V7 . "\x68\x65\x69\147\x68\x74\72" . $YW . "\x70\x78\x3b";
        $V7 = $V7 . "\142\x6f\162\x64\x65\x72\55\x72\141\x64\151\x75\x73\x3a\60\160\x78\x3b";
        w2:
        goto i1;
        lh:
        $V7 = $V7 . "\167\151\x64\x74\x68\x3a" . $dC . "\x70\x78\73";
        $V7 = $V7 . "\x68\145\x69\147\150\x74\x3a" . $M2 . "\x70\x78\x3b";
        $V7 = $V7 . "\142\157\162\x64\x65\162\x2d\x72\x61\x64\151\165\163\72" . $gZ . "\160\170\73";
        i1:
        $V7 = $V7 . "\142\x61\143\x6b\147\162\157\x75\156\144\x2d\x63\157\154\x6f\162\x3a\x23" . $op . "\x3b";
        $V7 = $V7 . "\142\157\x72\x64\x65\x72\x2d\x63\x6f\x6c\157\x72\x3a\x74\x72\x61\x6e\163\160\x61\x72\x65\156\164\x3b";
        $V7 = $V7 . "\x63\x6f\154\x6f\162\x3a\x23" . $V1 . "\x3b";
        $V7 = $V7 . "\146\x6f\x6e\164\55\x73\151\x7a\x65\x3a" . $xb . "\160\170\x3b";
        $V7 = $V7 . "\160\141\144\144\151\156\x67\72\x30\160\170\x3b";
        $b6 = $b6 . $V7 . "\x22\x2f\76";
        iH:
        echo "\x20\74\x61\x20\x68\x72\x65\x66\75\x22\x23\x22\x20\x6f\156\x43\x6c\151\x63\153\75\x22\163\x75\142\155\x69\164\x53\141\155\x6c\106\x6f\x72\x6d\50\x29\42\x3e";
        echo $b6;
        echo "\x3c\x2f\x61\76\x3c\57\146\x6f\162\155\x3e\x20";
        jb:
        if ($this->mo_saml_check_empty_or_null_val(get_site_option("\155\x6f\x5f\163\141\x6d\154\137\162\x65\144\x69\x72\145\143\x74\137\x65\162\x72\157\162\137\143\157\144\145"))) {
            goto Jr;
        }
        echo "\74\x64\x69\x76\x3e\x3c\x2f\144\x69\x76\76\x3c\144\x69\x76\x20\x74\x69\x74\x6c\145\x3d\42\114\x6f\x67\151\x6e\x20\105\x72\x72\157\x72\x22\x3e\74\146\x6f\x6e\164\40\143\x6f\x6c\x6f\x72\75\42\162\145\x64\x22\x3e\127\145\x20\x63\x6f\x75\x6c\x64\x20\156\157\x74\x20\163\x69\x67\x6e\x20\x79\157\165\x20\x69\156\x2e\40\x50\154\145\141\163\x65\x20\143\157\156\164\x61\143\x74\40\171\x6f\165\x72\40\101\144\155\151\x6e\151\163\164\162\141\x74\157\x72\56\74\57\146\x6f\x6e\x74\x3e\74\x2f\x64\x69\x76\x3e";
        delete_site_option("\155\157\137\x73\141\x6d\154\x5f\162\x65\144\x69\x72\145\143\164\137\145\162\162\157\162\x5f\x63\x6f\x64\x65");
        delete_site_option("\155\157\137\x73\x61\x6d\x6c\137\162\x65\144\x69\162\x65\x63\164\x5f\145\x72\162\x6f\162\137\162\145\x61\x73\157\x6e");
        Jr:
        echo "\74\141\x20\x68\162\x65\146\75\42\150\164\x74\160\72\57\57\x6d\x69\156\151\x6f\x72\141\156\x67\145\56\x63\x6f\x6d\x2f\167\x6f\x72\144\160\162\145\x73\x73\55\x6c\144\141\x70\55\154\157\x67\x69\156\x22\40\163\164\171\154\x65\x3d\x22\144\151\163\x70\x6c\141\x79\72\156\x6f\156\145\42\x3e\x4c\157\147\x69\x6e\40\x74\157\40\x57\157\162\x64\120\162\x65\163\163\x20\165\163\151\x6e\x67\x20\x4c\104\x41\120\74\x2f\141\76\15\xa\11\11\x9\11\x3c\x61\40\150\x72\x65\146\75\42\150\164\x74\160\x3a\57\x2f\155\x69\156\151\x6f\x72\141\156\147\x65\56\x63\157\155\x2f\143\154\x6f\x75\144\55\x69\x64\145\156\x74\151\164\171\55\142\x72\x6f\x6b\145\x72\55\x73\x65\x72\166\151\143\145\x22\x20\163\x74\171\x6c\x65\75\42\x64\151\x73\160\x6c\x61\171\72\156\157\x6e\x65\x22\76\x43\154\157\x75\144\40\111\144\x65\156\164\151\164\x79\x20\142\162\x6f\x6b\x65\x72\40\x73\x65\162\166\x69\143\145\x3c\x2f\141\x3e\15\xa\x9\x9\x9\11\74\x61\x20\150\162\145\x66\75\x22\150\164\164\160\x3a\x2f\x2f\155\x69\x6e\x69\157\x72\141\x6e\147\x65\x2e\143\x6f\155\x2f\163\x74\x72\157\x6e\147\137\141\x75\164\150\x22\40\x73\164\171\154\145\x3d\42\144\151\x73\x70\154\x61\x79\x3a\x6e\157\x6e\x65\73\x22\x3e\x3c\57\141\x3e\xd\xa\x9\x9\11\11\74\141\x20\x68\x72\145\146\75\42\x68\x74\164\160\x3a\x2f\x2f\x6d\151\156\x69\157\x72\141\x6e\x67\145\x2e\x63\x6f\155\57\x73\151\156\x67\x6c\145\x2d\163\x69\x67\156\x2d\157\156\55\x73\163\157\x22\x20\163\164\x79\154\145\75\42\x64\x69\x73\160\154\x61\x79\x3a\156\157\x6e\145\x3b\42\76\x3c\x2f\141\x3e\xd\xa\11\11\x9\11\74\141\40\x68\x72\145\x66\75\x22\150\x74\164\x70\x3a\x2f\x2f\x6d\x69\x6e\151\x6f\162\141\156\x67\x65\x2e\143\x6f\x6d\57\146\162\x61\x75\x64\x22\40\x73\164\171\154\x65\75\42\x64\x69\163\160\154\x61\x79\72\x6e\157\156\x65\73\x22\76\74\57\x61\x3e\15\12\15\xa\x9\11\11\74\57\x75\154\76\xd\xa\x9\11\x3c\57\x66\157\x72\155\x3e";
        SD:
    }
    public function mo_saml_check_empty_or_null_val($wE)
    {
        if (!(!isset($wE) || empty($wE))) {
            goto Bs;
        }
        return true;
        Bs:
        return false;
    }
    function mo_saml_logout($vE)
    {
        $user = get_user_by("\x69\144", $vE);
        $H1 = get_site_option("\x73\x61\x6d\154\x5f\154\x6f\x67\157\x75\x74\137\x75\x72\x6c");
        $ho = get_site_option("\x73\141\x6d\x6c\x5f\x6c\157\x67\x6f\165\x74\x5f\142\151\x6e\x64\x69\156\x67\137\x74\x79\x70\145");
        $current_user = $user;
        $AO = get_user_meta($current_user->ID, "\155\157\x5f\x73\x61\x6d\x6c\137\x69\144\160\137\x6c\157\x67\x69\x6e");
        $AO = isset($AO[0]) ? $AO[0] : '';
        $s0 = wp_get_referer();
        if (!empty($s0)) {
            goto v0;
        }
        $s0 = !empty(get_site_option("\155\x6f\x5f\163\141\x6d\x6c\137\x73\160\x5f\x62\141\163\x65\137\165\x72\154")) ? get_site_option("\x6d\157\x5f\163\141\155\154\137\x73\160\137\x62\141\x73\145\x5f\x75\x72\154") : get_network_site_url();
        v0:
        if (empty($H1)) {
            goto I1;
        }
        if (!(!session_id() || session_id() == '' || !isset($_SESSION))) {
            goto OG;
        }
        session_start();
        OG:
        if (isset($_SESSION["\155\x6f\x5f\x73\x61\155\x6c\x5f\x6c\x6f\147\x6f\x75\x74\137\x72\x65\161\x75\145\x73\x74"])) {
            goto wa;
        }
        if ($AO == "\x74\162\x75\x65") {
            goto Cl;
        }
        goto lF;
        wa:
        self::createLogoutResponseAndRedirect($H1, $ho);
        exit;
        goto lF;
        Cl:
        delete_user_meta($current_user->ID, "\x6d\157\137\163\x61\155\154\x5f\151\144\160\x5f\x6c\157\x67\x69\156");
        $Ix = get_user_meta($current_user->ID, "\155\157\x5f\x73\141\155\154\x5f\156\141\155\145\x5f\x69\x64");
        $L_ = get_user_meta($current_user->ID, "\x6d\157\137\x73\x61\x6d\x6c\x5f\x73\x65\x73\x73\151\x6f\156\137\151\x6e\x64\145\170");
        mo_saml_create_logout_request($Ix, $L_, $H1, $ho, $s0);
        lF:
        I1:
        wp_redirect($s0);
        exit;
    }
    function createLogoutResponseAndRedirect($H1, $ho)
    {
        $ia = get_site_option("\x6d\x6f\137\x73\x61\x6d\154\137\x73\160\137\142\x61\x73\145\137\x75\x72\154");
        if (!empty($ia)) {
            goto qE;
        }
        $ia = get_network_site_url();
        qE:
        $OW = $_SESSION["\x6d\157\x5f\163\141\155\x6c\x5f\154\x6f\x67\x6f\x75\164\x5f\162\145\x71\x75\x65\163\x74"];
        $tR = $_SESSION["\155\157\137\x73\141\x6d\154\x5f\x6c\x6f\147\x6f\165\x74\137\162\x65\154\141\171\137\x73\164\x61\164\145"];
        unset($_SESSION["\155\x6f\137\163\141\155\x6c\137\x6c\157\x67\157\x75\164\137\x72\x65\161\x75\145\x73\164"]);
        unset($_SESSION["\155\x6f\137\x73\x61\155\x6c\137\154\157\147\x6f\x75\164\x5f\x72\x65\x6c\x61\171\137\163\x74\141\164\x65"]);
        $Lx = new DOMDocument();
        $Lx->loadXML($OW);
        $OW = $Lx->firstChild;
        if (!($OW->localName == "\x4c\157\x67\157\x75\164\x52\x65\161\x75\145\x73\164")) {
            goto mm;
        }
        $DD = new SAML2_LogoutRequest($OW);
        $P6 = get_site_option("\155\157\x5f\x73\141\155\154\x5f\163\x70\137\x65\156\x74\x69\164\x79\137\x69\144");
        if (!empty($P6)) {
            goto me;
        }
        $P6 = $ia . "\57\167\x70\55\143\157\156\164\145\x6e\x74\57\x70\x6c\x75\x67\151\156\163\x2f\155\151\156\x69\157\162\141\156\147\145\55\163\x61\x6d\x6c\55\62\x30\55\163\151\x6e\147\x6c\145\x2d\163\x69\147\156\x2d\157\156\x2f";
        me:
        $Nn = $H1;
        $CK = Utilities::createLogoutResponse($DD->getId(), $P6, $Nn, $ho);
        if (empty($ho) || $ho == "\110\164\x74\x70\x52\x65\144\151\x72\145\143\164") {
            goto hc;
        }
        if (!(get_site_option("\x73\141\155\x6c\137\x72\145\x71\165\145\163\x74\137\163\151\147\x6e\145\144") == "\x75\156\x63\150\145\143\153\145\x64")) {
            goto zg;
        }
        $mW = base64_encode($CK);
        Utilities::postSAMLResponse($H1, $mW, $tR);
        exit;
        zg:
        $js = '';
        $Z2 = '';
        $mW = Utilities::signXML($CK, "\123\164\141\164\x75\163");
        Utilities::postSAMLResponse($H1, $mW, $tR);
        goto H2;
        hc:
        $YR = $H1;
        if (strpos($H1, "\x3f") !== false) {
            goto hU;
        }
        $YR .= "\77";
        goto Bv;
        hU:
        $YR .= "\x26";
        Bv:
        if (!(get_site_option("\x73\141\x6d\154\137\x72\x65\161\x75\x65\163\164\x5f\x73\x69\147\x6e\145\144") == "\165\x6e\143\x68\x65\x63\153\x65\144")) {
            goto yu;
        }
        $YR .= "\x53\101\x4d\x4c\x52\145\x73\160\x6f\x6e\163\145\x3d" . $CK . "\x26\x52\x65\154\141\171\x53\164\x61\x74\145\75" . urlencode($tR);
        header("\x4c\157\143\x61\164\x69\x6f\x6e\72\x20" . $YR);
        exit;
        yu:
        $YR .= "\123\x41\x4d\114\x52\x65\x73\x70\x6f\156\163\x65\75" . $CK . "\x26\122\x65\x6c\141\x79\x53\164\141\164\x65\x3d" . urlencode($tR);
        header("\114\157\x63\x61\164\151\x6f\x6e\x3a\x20" . $YR);
        exit;
        H2:
        mm:
    }
}
function mo_saml_create_logout_request($Ix, $L_, $H1, $ho, $s0)
{
    $ia = get_site_option("\x6d\157\x5f\x73\141\155\154\137\163\160\x5f\142\141\x73\x65\x5f\165\162\x6c");
    if (!empty($ia)) {
        goto OB;
    }
    $ia = get_network_site_url();
    OB:
    $P6 = get_site_option("\155\x6f\x5f\163\x61\155\154\x5f\x73\160\137\x65\x6e\164\x69\x74\x79\137\151\x64");
    if (!empty($P6)) {
        goto wt;
    }
    $P6 = $ia . "\57\167\x70\x2d\143\157\156\x74\x65\x6e\164\x2f\x70\x6c\165\x67\x69\x6e\x73\x2f\x6d\x69\156\151\157\162\x61\x6e\147\145\55\163\141\155\x6c\x2d\62\x30\x2d\163\x69\x6e\x67\154\145\x2d\163\151\x67\x6e\55\x6f\x6e\57";
    wt:
    $Nn = $H1;
    $Io = $s0;
    if (!empty($Io)) {
        goto ZZ;
    }
    $Io = saml_get_current_page_url();
    if (!strpos($Io, "\x3f")) {
        goto KH;
    }
    $Io = get_network_site_url();
    KH:
    ZZ:
    $Io = mo_saml_relaystate_url($Io);
    $Da = Utilities::createLogoutRequest($Ix, $P6, $Nn, $L_, $ho);
    if (empty($ho) || $ho == "\110\x74\164\x70\x52\x65\x64\151\162\145\x63\164") {
        goto pp;
    }
    if (!(get_site_option("\163\141\x6d\154\x5f\162\x65\x71\x75\145\x73\x74\137\163\151\x67\156\145\x64") == "\165\156\x63\x68\x65\x63\x6b\x65\x64")) {
        goto wR;
    }
    $mW = base64_encode($Da);
    Utilities::postSAMLRequest($H1, $mW, $Io);
    exit;
    wR:
    $js = '';
    $Z2 = '';
    $mW = Utilities::signXML($Da, "\116\x61\x6d\x65\111\104\x50\157\154\151\143\x79");
    Utilities::postSAMLRequest($H1, $mW, $Io);
    goto kN;
    pp:
    $YR = $H1;
    if (strpos($H1, "\x3f") !== false) {
        goto mU;
    }
    $YR .= "\77";
    goto VK;
    mU:
    $YR .= "\46";
    VK:
    if (!(get_site_option("\163\141\155\154\137\162\145\x71\165\145\163\x74\x5f\x73\x69\147\156\145\144") == "\165\x6e\143\x68\x65\x63\x6b\145\x64")) {
        goto Ym;
    }
    $YR .= "\123\x41\x4d\x4c\x52\145\161\x75\x65\x73\x74\x3d" . $Da . "\x26\122\145\x6c\x61\x79\x53\164\x61\164\x65\75" . urlencode($Io);
    header("\x4c\157\x63\x61\164\151\x6f\156\x3a\x20" . $YR);
    exit;
    Ym:
    $Da = "\123\101\x4d\x4c\122\x65\x71\x75\x65\163\164\x3d" . $Da . "\46\x52\145\154\141\x79\x53\164\x61\164\x65\75" . urlencode($Io) . "\x26\123\x69\x67\101\x6c\x67\75" . urlencode(XMLSecurityKey::RSA_SHA256);
    $Es = array("\x74\x79\x70\145" => "\160\x72\x69\x76\141\x74\145");
    $XC = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, $Es);
    $Ge = get_site_option("\x6d\157\137\163\141\x6d\x6c\x5f\x63\x75\x72\x72\145\156\164\137\x63\x65\162\164\137\160\162\x69\x76\141\x74\x65\137\153\145\171");
    $XC->loadKey($Ge, FALSE);
    $p0 = new XMLSecurityDSig();
    $l8 = $XC->signData($Da);
    $l8 = base64_encode($l8);
    $YR .= $Da . "\x26\123\x69\147\156\141\164\x75\162\x65\x3d" . urlencode($l8);
    header("\114\157\x63\141\x74\151\x6f\x6e\72" . $YR);
    exit;
    kN:
}
function mo_login_validate()
{
    if (!(isset($_REQUEST["\157\160\x74\151\157\156"]) && $_REQUEST["\x6f\160\164\x69\157\156"] == "\x6d\157\163\141\x6d\x6c\137\155\145\164\x61\x64\x61\164\141")) {
        goto B0;
    }
    miniorange_generate_metadata();
    B0:
    if (!mo_saml_is_customer_license_verified()) {
        goto Km;
    }
    if (!(isset($_REQUEST["\x6f\x70\x74\x69\157\x6e"]) && $_REQUEST["\x6f\160\x74\151\x6f\x6e"] == "\x73\x61\155\154\137\x75\x73\x65\x72\137\x6c\157\x67\x69\156" || isset($_REQUEST["\157\160\x74\151\x6f\x6e"]) && $_REQUEST["\157\160\x74\x69\157\x6e"] == "\x74\145\163\x74\103\x6f\x6e\x66\x69\147" || isset($_REQUEST["\157\160\x74\151\157\x6e"]) && $_REQUEST["\x6f\160\x74\x69\x6f\156"] == "\147\145\x74\163\141\155\x6c\162\145\x71\x75\x65\x73\x74" || isset($_REQUEST["\x6f\160\164\151\157\156"]) && $_REQUEST["\x6f\160\x74\x69\157\156"] == "\x67\x65\x74\x73\141\x6d\154\x72\145\x73\160\157\x6e\x73\145")) {
        goto YS;
    }
    if (mo_saml_is_sp_configured()) {
        goto mK;
    }
    if (!is_user_logged_in()) {
        goto k1;
    }
    if (!isset($_REQUEST["\x72\145\x64\151\x72\145\143\x74\x5f\x74\157"])) {
        goto g0;
    }
    $p5 = htmlspecialchars($_REQUEST["\x72\145\x64\x69\x72\x65\x63\164\137\x74\157"]);
    header("\114\x6f\x63\x61\x74\x69\x6f\156\72\x20" . $p5);
    exit;
    g0:
    k1:
    goto Mc;
    mK:
    if (!(is_user_logged_in() and $_REQUEST["\x6f\160\x74\151\x6f\x6e"] == "\163\x61\x6d\154\x5f\165\x73\x65\x72\x5f\154\x6f\147\x69\156")) {
        goto WP;
    }
    if (!isset($_REQUEST["\x72\x65\144\151\162\x65\143\x74\137\x74\157"])) {
        goto Ve;
    }
    $p5 = htmlspecialchars($_REQUEST["\162\145\x64\x69\x72\x65\x63\164\137\x74\x6f"]);
    header("\114\x6f\x63\141\164\151\x6f\x6e\72\40" . $p5);
    exit;
    Ve:
    return;
    WP:
    $ia = get_site_option("\155\x6f\137\x73\141\155\x6c\x5f\x73\x70\x5f\142\141\163\145\137\165\162\x6c");
    if (!empty($ia)) {
        goto AA;
    }
    $ia = get_network_site_url();
    AA:
    $sT = get_site_option("\x73\141\155\x6c\137\163\x73\157\x5f\x73\x65\x74\164\151\x6e\147\x73");
    $LQ = get_current_blog_id();
    $bO = Utilities::get_active_sites();
    if (in_array($LQ, $bO)) {
        goto hh;
    }
    return;
    hh:
    if (!(empty($sT[$LQ]) && !empty($sT["\x44\x45\106\x41\x55\114\124"]))) {
        goto Zm;
    }
    $sT[$LQ] = $sT["\104\105\x46\x41\x55\x4c\x54"];
    Zm:
    if ($_REQUEST["\157\x70\164\x69\157\156"] == "\x74\x65\163\x74\103\157\156\x66\x69\147" and array_key_exists("\156\x65\167\143\x65\162\164", $_REQUEST)) {
        goto m4;
    }
    if ($_REQUEST["\157\160\x74\151\x6f\x6e"] == "\164\x65\x73\x74\x43\x6f\156\x66\151\x67") {
        goto aa;
    }
    if ($_REQUEST["\157\x70\164\151\x6f\x6e"] == "\147\145\164\x73\x61\155\154\x72\x65\161\165\x65\x73\x74") {
        goto Rn;
    }
    if ($_REQUEST["\x6f\160\x74\x69\x6f\156"] == "\147\145\164\163\x61\x6d\154\x72\145\163\160\x6f\156\x73\x65") {
        goto mC;
    }
    if (!empty($sT[$LQ]["\x6d\x6f\137\163\x61\155\x6c\137\x72\x65\154\x61\171\137\163\164\x61\x74\145"])) {
        goto IX;
    }
    if (isset($_REQUEST["\x72\145\x64\151\162\145\x63\164\137\164\157"])) {
        goto wI;
    }
    $Io = saml_get_current_page_url();
    goto Je;
    wI:
    $Io = $_REQUEST["\162\x65\x64\x69\x72\145\143\x74\137\x74\x6f"];
    Je:
    goto nx;
    IX:
    $Io = $sT[$LQ]["\155\x6f\x5f\x73\141\155\x6c\137\x72\145\154\x61\171\x5f\163\x74\x61\164\145"];
    nx:
    goto t3;
    mC:
    $Io = "\144\x69\x73\160\154\x61\x79\123\x41\115\x4c\122\145\x73\160\x6f\156\x73\145";
    t3:
    goto dP;
    Rn:
    $Io = "\144\x69\x73\160\154\141\171\123\x41\x4d\x4c\122\145\161\x75\x65\x73\164";
    dP:
    goto qM;
    aa:
    $Io = "\x74\145\x73\164\x56\141\154\x69\144\141\x74\x65";
    qM:
    goto DL;
    m4:
    $Io = "\164\145\163\x74\116\x65\167\103\145\x72\x74\151\146\151\143\x61\x74\x65";
    DL:
    $ZB = get_site_option("\163\141\155\154\137\x6c\x6f\x67\x69\156\x5f\165\162\x6c");
    $cS = !empty(get_site_option("\x73\141\155\x6c\137\x6c\x6f\147\x69\x6e\x5f\142\151\156\x64\151\156\x67\x5f\x74\x79\x70\145")) ? get_site_option("\163\141\x6d\154\137\x6c\x6f\x67\x69\156\x5f\142\x69\156\144\151\x6e\147\137\x74\x79\x70\145") : "\110\x74\x74\x70\x50\x6f\x73\x74";
    $sT = get_site_option("\163\141\x6d\x6c\137\x73\163\x6f\x5f\163\x65\x74\164\x69\156\147\163");
    $LQ = get_current_blog_id();
    $bO = Utilities::get_active_sites();
    if (in_array($LQ, $bO)) {
        goto YE;
    }
    return;
    YE:
    if (!(empty($sT[$LQ]) && !empty($sT["\x44\x45\106\101\x55\114\124"]))) {
        goto HU;
    }
    $sT[$LQ] = $sT["\104\x45\x46\101\125\x4c\x54"];
    HU:
    $ny = isset($sT[$LQ]["\155\157\x5f\x73\x61\155\x6c\137\x66\157\162\x63\x65\x5f\141\165\164\x68\x65\x6e\x74\151\x63\141\x74\151\157\x6e"]) ? $sT[$LQ]["\x6d\157\137\x73\x61\155\x6c\x5f\x66\157\x72\143\145\x5f\x61\165\x74\150\x65\156\164\151\143\x61\164\151\157\156"] : '';
    $lZ = $ia . "\x2f";
    $P6 = get_site_option("\155\157\x5f\163\x61\155\154\x5f\163\160\x5f\145\x6e\164\151\x74\171\137\151\144");
    $L3 = get_site_option("\163\x61\x6d\x6c\137\x6e\x61\x6d\145\x69\x64\x5f\x66\x6f\x72\155\141\164");
    if (!empty($L3)) {
        goto WB;
    }
    $L3 = "\x31\x2e\x31\x3a\x6e\x61\155\145\151\x64\55\x66\157\x72\x6d\x61\164\x3a\165\156\x73\160\x65\x63\x69\x66\151\145\144";
    WB:
    if (!empty($P6)) {
        goto Ew;
    }
    $P6 = $ia . "\57\x77\x70\x2d\143\x6f\156\164\x65\156\164\57\160\x6c\165\x67\151\156\x73\57\155\151\x6e\151\157\162\x61\x6e\x67\x65\55\x73\141\155\154\55\x32\60\55\163\x69\x6e\x67\x6c\145\x2d\x73\x69\147\x6e\x2d\x6f\156\x2f";
    Ew:
    $Da = Utilities::createAuthnRequest($lZ, $P6, $ZB, $ny, $cS, $L3);
    if (!($Io == "\x64\151\x73\x70\154\x61\171\123\x41\115\114\122\145\161\x75\x65\163\164")) {
        goto MQ;
    }
    mo_saml_show_SAML_log(Utilities::createAuthnRequest($lZ, $P6, $ZB, $ny, "\x48\164\x74\x70\120\157\163\x74", $L3), $Io);
    MQ:
    $YR = htmlspecialchars_decode($ZB);
    if (strpos($ZB, "\x3f") !== false) {
        goto hv;
    }
    $YR .= "\77";
    goto xc;
    hv:
    $YR .= "\46";
    xc:
    $Io = mo_saml_relaystate_url($Io);
    if ($cS == "\x48\x74\164\x70\x52\x65\x64\151\x72\145\143\164") {
        goto KQ;
    }
    if (!(get_site_option("\163\x61\155\x6c\137\162\145\x71\165\145\x73\164\x5f\163\151\147\x6e\x65\144") == "\x75\x6e\143\150\145\143\153\145\144")) {
        goto tL;
    }
    $mW = base64_encode($Da);
    Utilities::postSAMLRequest($ZB, $mW, $Io);
    exit;
    tL:
    $js = '';
    $Z2 = '';
    if ($_REQUEST["\x6f\x70\x74\151\x6f\156"] == "\164\x65\x73\164\103\x6f\156\146\x69\147" && array_key_exists("\x6e\x65\167\143\145\162\164", $_REQUEST)) {
        goto PM;
    }
    $mW = Utilities::signXML($Da, "\x4e\x61\x6d\145\x49\x44\x50\157\154\151\143\171");
    goto fg;
    PM:
    $mW = Utilities::signXML($Da, "\116\x61\155\145\x49\104\120\x6f\x6c\x69\x63\x79", true);
    fg:
    Utilities::postSAMLRequest($ZB, $mW, $Io);
    update_site_option("\155\x6f\x5f\x73\x61\155\154\137\x6e\145\x77\137\x63\x65\162\164\137\x74\145\x73\164", true);
    goto cz;
    KQ:
    if (!(get_site_option("\163\141\x6d\x6c\x5f\x72\145\161\165\x65\x73\164\x5f\x73\151\147\x6e\145\x64") == "\x75\156\143\x68\x65\143\153\x65\x64")) {
        goto od;
    }
    $YR .= "\123\x41\115\114\x52\x65\161\x75\x65\x73\x74\x3d" . $Da . "\46\x52\x65\154\141\x79\x53\x74\141\x74\145\x3d" . urlencode($Io);
    header("\x4c\x6f\x63\141\x74\151\x6f\156\x3a\40" . $YR);
    exit;
    od:
    $Da = "\123\101\115\x4c\122\145\161\165\145\163\x74\x3d" . $Da . "\x26\x52\145\154\141\x79\123\x74\141\x74\x65\75" . urlencode($Io) . "\x26\x53\x69\x67\x41\154\x67\x3d" . urlencode(XMLSecurityKey::RSA_SHA256);
    $Es = array("\x74\171\x70\145" => "\x70\x72\x69\x76\141\x74\145");
    $XC = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, $Es);
    if ($_REQUEST["\157\160\164\151\157\x6e"] == "\164\x65\163\164\103\x6f\156\x66\x69\x67" && array_key_exists("\156\145\x77\x63\145\x72\x74", $_REQUEST)) {
        goto Mg;
    }
    $Ge = get_site_option("\155\x6f\137\x73\141\155\x6c\137\x63\x75\162\162\145\156\x74\137\x63\145\x72\164\x5f\x70\162\x69\x76\141\164\x65\x5f\x6b\x65\171");
    goto jR;
    Mg:
    $Ge = file_get_contents(plugin_dir_path(__FILE__) . "\162\x65\x73\157\x75\162\x63\x65\163" . DIRECTORY_SEPARATOR . mo_options_enum_default_sp_certificate::SP_Private_Key);
    jR:
    $XC->loadKey($Ge, FALSE);
    $p0 = new XMLSecurityDSig();
    $l8 = $XC->signData($Da);
    $l8 = base64_encode($l8);
    $YR .= $Da . "\46\123\x69\147\x6e\x61\164\165\162\145\75" . urlencode($l8);
    header("\x4c\157\143\x61\164\151\157\x6e\x3a\40" . $YR);
    exit;
    cz:
    Mc:
    YS:
    if (!(array_key_exists("\x53\x41\x4d\114\122\x65\x73\x70\x6f\x6e\x73\x65", $_REQUEST) && !empty($_REQUEST["\123\x41\x4d\114\122\145\163\x70\157\156\x73\x65"]))) {
        goto H8;
    }
    if (array_key_exists("\122\145\x6c\x61\x79\x53\x74\141\x74\145", $_POST) && !empty($_POST["\x52\x65\x6c\x61\x79\123\164\141\164\145"]) && $_POST["\x52\145\154\141\x79\123\164\141\164\145"] != "\57") {
        goto Ou;
    }
    $Z0 = '';
    goto bW;
    Ou:
    $Z0 = $_POST["\122\x65\154\141\171\x53\x74\141\164\x65"];
    bW:
    $Z0 = mo_saml_parse_url($Z0);
    $ia = get_site_option("\155\157\137\163\x61\x6d\154\137\x73\x70\x5f\x62\141\x73\x65\137\x75\x72\154");
    if (!empty($ia)) {
        goto OI;
    }
    $ia = get_network_site_url();
    OI:
    $sn = $_REQUEST["\123\101\x4d\x4c\122\x65\163\160\x6f\156\x73\x65"];
    $sn = base64_decode($sn);
    if (!($Z0 == "\144\x69\x73\160\x6c\x61\x79\x53\101\x4d\x4c\122\145\163\x70\x6f\156\163\145")) {
        goto pl;
    }
    mo_saml_show_SAML_log($sn, $Z0);
    pl:
    if (!(array_key_exists("\123\101\x4d\x4c\122\x65\x73\x70\x6f\x6e\x73\145", $_GET) && !empty($_GET["\x53\x41\x4d\x4c\122\x65\x73\160\157\156\163\x65"]))) {
        goto YK;
    }
    $sn = gzinflate($sn);
    YK:
    $Lx = new DOMDocument();
    $Lx->loadXML($sn);
    $gj = $Lx->firstChild;
    $ZR = $Lx->documentElement;
    $VM = new DOMXpath($Lx);
    $VM->registerNamespace("\163\141\155\154\x70", "\x75\x72\156\x3a\157\x61\x73\151\163\72\x6e\x61\155\145\163\72\164\x63\x3a\x53\x41\x4d\x4c\72\x32\56\x30\72\160\162\157\164\157\x63\157\154");
    $VM->registerNamespace("\x73\x61\x6d\x6c", "\x75\x72\156\72\157\x61\163\151\x73\72\156\141\155\x65\x73\x3a\164\x63\x3a\x53\x41\115\114\x3a\x32\56\x30\x3a\x61\x73\163\145\x72\164\x69\x6f\x6e");
    if ($gj->localName == "\x4c\157\x67\157\165\164\122\x65\x73\x70\x6f\x6e\x73\145") {
        goto i5;
    }
    $YK = $VM->query("\x2f\x73\x61\155\x6c\x70\72\x52\x65\x73\x70\x6f\156\163\x65\57\x73\x61\155\x6c\160\x3a\123\164\141\164\165\x73\x2f\x73\x61\155\154\160\x3a\123\x74\x61\x74\x75\x73\x43\x6f\144\145", $ZR);
    $vs = isset($YK) ? $YK->item(0)->getAttribute("\x56\x61\154\165\x65") : '';
    $aJ = explode("\x3a", $vs);
    if (!array_key_exists(7, $aJ)) {
        goto ac;
    }
    $YK = $aJ[7];
    ac:
    $w0 = $VM->query("\x2f\163\x61\155\154\x70\72\122\145\x73\x70\157\156\163\145\x2f\163\141\x6d\154\x70\x3a\123\164\x61\164\x75\163\x2f\x73\141\155\x6c\x70\x3a\123\164\x61\164\165\163\115\x65\163\163\x61\147\145", $ZR);
    $Xi = isset($w0) ? $w0->item(0) : '';
    if (empty($Xi)) {
        goto d2;
    }
    $Xi = $Xi->nodeValue;
    d2:
    if (array_key_exists("\122\145\154\x61\x79\x53\164\141\x74\145", $_POST) && !empty($_POST["\x52\145\154\x61\x79\x53\x74\141\164\x65"]) && $_POST["\x52\x65\x6c\141\171\123\164\141\x74\x65"] != "\x2f") {
        goto Io;
    }
    $Z0 = '';
    goto OO;
    Io:
    $Z0 = $_POST["\122\145\154\141\171\123\164\x61\164\145"];
    $Z0 = mo_saml_parse_url($Z0);
    OO:
    if (!($YK != "\123\x75\143\143\145\163\x73")) {
        goto c2;
    }
    show_status_error($YK, $Z0, $Xi);
    c2:
    if (!($Z0 !== "\164\x65\x73\164\126\x61\x6c\x69\x64\141\x74\145" && $Z0 !== "\x74\x65\163\164\x4e\x65\167\x43\145\x72\164\151\x66\x69\x63\x61\164\145")) {
        goto qY;
    }
    $vf = parse_url($Z0, PHP_URL_HOST);
    $Dj = parse_url($ia, PHP_URL_HOST);
    $Ml = parse_url(get_current_base_url(), PHP_URL_HOST);
    if (!empty($Z0)) {
        goto ln;
    }
    $Z0 = "\x2f";
    goto P6;
    ln:
    $Z0 = mo_saml_parse_url($Z0);
    P6:
    if (!(!empty($vf) && $vf != $Ml)) {
        goto G8;
    }
    Utilities::postSAMLResponse($Z0, $_REQUEST["\x53\101\x4d\x4c\122\145\x73\160\157\156\163\145"], mo_saml_relaystate_url($Z0));
    G8:
    qY:
    $B4 = maybe_unserialize(get_site_option("\163\141\155\x6c\137\170\x35\x30\x39\137\x63\145\x72\x74\151\146\151\x63\x61\x74\145"));
    update_site_option("\155\157\137\163\x61\x6d\154\x5f\x72\x65\163\160\x6f\x6e\163\145", base64_encode($sn));
    foreach ($B4 as $XC => $wE) {
        if (@openssl_x509_read($wE)) {
            goto Co;
        }
        unset($B4[$XC]);
        Co:
        Ft:
    }
    rp:
    $lZ = $ia . "\57";
    if ($Z0 == "\164\x65\163\164\116\x65\x77\x43\145\162\164\x69\x66\151\x63\x61\x74\x65") {
        goto pX;
    }
    $sn = new SAML2_Response($gj, get_site_option("\155\157\x5f\x73\141\x6d\154\x5f\x63\165\x72\162\x65\156\x74\x5f\143\x65\162\x74\x5f\160\x72\x69\x76\141\164\x65\137\x6b\x65\171"));
    goto vF;
    pX:
    $xG = file_get_contents(plugin_dir_path(__FILE__) . "\162\x65\x73\x6f\x75\x72\x63\x65\163" . DIRECTORY_SEPARATOR . mo_options_enum_default_sp_certificate::SP_Private_Key);
    $sn = new SAML2_Response($gj, $xG);
    vF:
    $qS = $sn->getSignatureData();
    $bQ = current($sn->getAssertions())->getSignatureData();
    if (!(empty($bQ) && empty($qS))) {
        goto ss;
    }
    if ($Z0 == "\164\x65\x73\x74\126\141\x6c\151\x64\141\x74\145" or $Z0 == "\x74\x65\x73\x74\x4e\x65\167\103\145\x72\x74\151\146\151\x63\x61\x74\145") {
        goto LD;
    }
    wp_die("\x57\x65\x20\x63\157\x75\x6c\144\x20\x6e\x6f\x74\x20\163\151\147\156\x20\x79\x6f\x75\x20\151\x6e\x2e\40\x50\x6c\x65\x61\163\x65\40\143\157\156\x74\141\143\164\40\x61\144\155\151\156\x69\163\164\162\141\164\x6f\x72", "\105\162\x72\x6f\162\72\40\111\x6e\166\141\154\x69\x64\40\x53\x41\115\x4c\x20\x52\x65\163\x70\x6f\156\x73\x65");
    goto MD;
    LD:
    $Tf = mo_options_error_constants::Error_no_certificate;
    $b3 = mo_options_error_constants::Cause_no_certificate;
    echo "\x3c\144\151\166\40\163\x74\x79\154\145\x3d\42\x66\x6f\156\x74\55\146\141\155\x69\x6c\171\x3a\103\141\154\151\x62\162\x69\x3b\x70\x61\144\x64\x69\156\147\x3a\60\40\63\x25\x3b\42\x3e\15\12\11\x9\11\11\x9\11\x3c\x64\151\166\40\163\x74\171\x6c\x65\75\x22\x63\157\x6c\x6f\x72\x3a\40\x23\141\71\64\x34\x34\62\73\142\x61\143\153\147\x72\x6f\x75\156\x64\55\143\157\x6c\157\162\x3a\40\43\x66\x32\x64\145\x64\x65\73\x70\x61\144\144\151\156\147\72\40\x31\x35\160\x78\x3b\155\x61\162\x67\x69\x6e\55\142\157\164\164\157\x6d\72\x20\x32\60\160\x78\x3b\164\145\x78\x74\x2d\141\x6c\151\x67\156\72\x63\x65\156\164\145\x72\73\142\x6f\x72\144\x65\x72\x3a\61\x70\x78\40\163\157\x6c\151\144\x20\x23\x45\x36\x42\63\102\x32\x3b\x66\x6f\156\164\x2d\x73\151\x7a\x65\72\61\x38\x70\x74\x3b\42\x3e\40\105\122\x52\117\122\74\57\144\x69\166\76\xd\12\11\x9\11\x9\11\11\74\x64\x69\x76\x20\163\x74\171\154\x65\75\x22\x63\157\154\157\x72\72\40\43\x61\x39\x34\64\64\x32\73\146\157\156\x74\x2d\163\151\x7a\x65\72\61\x34\x70\164\x3b\x20\x6d\141\162\147\151\x6e\x2d\x62\157\164\x74\157\155\x3a\x32\x30\x70\170\x3b\42\76\74\160\x3e\x3c\x73\164\x72\157\156\x67\x3e\105\x72\x72\x6f\x72\40\40\72" . $Tf . "\x20\74\x2f\163\164\162\x6f\x6e\x67\76\74\x2f\x70\76\xd\12\11\x9\x9\x9\x9\x9\xd\xa\x9\x9\x9\11\x9\x9\x3c\x70\76\74\163\164\162\x6f\156\x67\76\x50\x6f\x73\x73\x69\x62\154\x65\x20\103\141\x75\163\145\72\x20" . $b3 . "\74\57\x73\x74\162\x6f\x6e\147\76\74\x2f\x70\76\xd\12\11\x9\11\x9\x9\x9\xd\12\11\11\x9\x9\11\x9\74\x2f\144\x69\x76\x3e\x3c\57\x64\x69\x76\x3e";
    mo_saml_download_logs($Tf, $b3);
    exit;
    MD:
    ss:
    $d2 = '';
    if (is_array($B4)) {
        goto hL;
    }
    $Qs = XMLSecurityKey::getRawThumbprint($B4);
    $Qs = mo_saml_convert_to_windows_iconv($Qs);
    $Qs = preg_replace("\x2f\134\163\53\57", '', $Qs);
    if (empty($qS)) {
        goto YL;
    }
    $d2 = Utilities::processResponse($lZ, $Qs, $qS, $sn, 0, $Z0);
    YL:
    if (empty($bQ)) {
        goto mE;
    }
    $d2 = Utilities::processResponse($lZ, $Qs, $bQ, $sn, 0, $Z0);
    mE:
    goto e9;
    hL:
    foreach ($B4 as $XC => $wE) {
        $Qs = XMLSecurityKey::getRawThumbprint($wE);
        $Qs = mo_saml_convert_to_windows_iconv($Qs);
        $Qs = preg_replace("\57\134\x73\x2b\x2f", '', $Qs);
        if (empty($qS)) {
            goto Zh;
        }
        $d2 = Utilities::processResponse($lZ, $Qs, $qS, $sn, $XC, $Z0);
        Zh:
        if (empty($bQ)) {
            goto tA;
        }
        $d2 = Utilities::processResponse($lZ, $Qs, $bQ, $sn, $XC, $Z0);
        tA:
        if (!$d2) {
            goto ml;
        }
        goto oG;
        ml:
        zR:
    }
    oG:
    e9:
    if (empty($qS)) {
        goto R_;
    }
    $XW = $qS["\x43\x65\x72\x74\x69\x66\151\143\141\x74\x65\163"][0];
    goto LL;
    R_:
    $XW = $bQ["\x43\x65\162\x74\x69\146\151\x63\x61\164\145\x73"][0];
    LL:
    if ($d2) {
        goto uI;
    }
    if ($Z0 == "\164\145\x73\x74\126\141\x6c\151\x64\141\x74\145" or $Z0 == "\x74\145\163\x74\116\x65\x77\x43\145\x72\164\x69\x66\x69\143\x61\x74\145") {
        goto Vx;
    }
    wp_die("\127\145\40\x63\x6f\x75\x6c\x64\x20\x6e\x6f\164\x20\x73\151\147\156\x20\171\157\x75\40\151\x6e\56\40\120\154\x65\141\163\145\x20\143\157\156\164\141\x63\164\40\x79\x6f\x75\162\40\101\x64\x6d\x69\x6e\151\x73\x74\x72\141\x74\157\x72", "\105\162\x72\x6f\162\x20\x3a\103\x65\x72\164\x69\x66\x69\143\141\x74\x65\x20\x6e\x6f\x74\x20\146\157\x75\x6e\144");
    goto xI;
    Vx:
    $Tf = mo_options_error_constants::Error_wrong_certificate;
    $b3 = mo_options_error_constants::Cause_wrong_certificate;
    $HE = "\x2d\55\55\55\55\102\x45\x47\x49\x4e\x20\103\x45\x52\124\x49\106\111\103\x41\x54\x45\x2d\x2d\x2d\x2d\x2d\74\x62\162\76" . chunk_split($XW, 64) . "\x3c\x62\x72\76\55\x2d\x2d\x2d\55\105\116\x44\x20\103\x45\122\x54\111\106\111\x43\101\x54\105\55\55\x2d\x2d\55";
    echo "\x3c\x64\x69\166\40\x73\164\171\154\145\75\42\x66\157\156\164\x2d\146\141\155\151\x6c\x79\72\x43\141\154\151\x62\x72\151\x3b\x70\x61\144\x64\151\156\147\72\60\x20\63\45\73\x22\x3e";
    echo "\x3c\144\151\x76\x20\x73\164\x79\154\x65\x3d\42\x63\157\x6c\x6f\162\x3a\x20\43\141\x39\x34\x34\x34\x32\x3b\x62\141\x63\x6b\x67\x72\157\165\x6e\144\x2d\x63\157\x6c\x6f\x72\x3a\x20\43\146\62\144\x65\x64\145\73\x70\x61\144\x64\151\x6e\147\72\x20\61\x35\160\x78\73\155\141\162\x67\151\156\55\x62\x6f\164\164\x6f\x6d\72\40\x32\x30\x70\170\73\x74\x65\x78\164\55\x61\x6c\151\147\x6e\x3a\x63\145\156\164\x65\162\73\142\157\x72\x64\x65\162\x3a\61\160\170\x20\163\157\154\x69\144\40\x23\x45\x36\x42\63\x42\62\73\x66\157\156\164\55\163\x69\172\x65\x3a\x31\70\160\164\73\42\x3e\40\x45\x52\x52\x4f\x52\x3c\57\x64\x69\x76\x3e\15\12\40\40\40\x20\40\40\40\x20\x20\x20\x20\x20\x20\x20\x20\x20\40\x20\40\x20\40\40\40\40\74\144\x69\x76\40\163\164\x79\154\x65\75\42\143\x6f\x6c\157\x72\72\x20\43\x61\x39\64\64\64\x32\x3b\146\x6f\156\164\x2d\x73\151\172\145\x3a\61\64\160\x74\x3b\x20\155\141\162\x67\151\x6e\x2d\142\x6f\x74\x74\x6f\x6d\x3a\x32\x30\x70\x78\73\x22\x3e\x3c\160\76\74\163\164\x72\x6f\x6e\x67\76\105\162\162\157\x72\x3a\40\74\x2f\x73\164\x72\x6f\x6e\x67\x3e\x55\156\x61\x62\x6c\145\x20\164\157\x20\x66\x69\x6e\x64\40\141\x20\x63\x65\162\x74\151\x66\151\x63\141\164\145\x20\x6d\x61\164\x63\150\x69\156\147\x20\x74\x68\x65\x20\x63\x6f\x6e\x66\151\x67\165\162\x65\x64\x20\146\151\x6e\x67\145\162\x70\x72\151\156\164\56\74\57\x70\x3e\15\xa\x20\40\x20\x20\40\x20\x20\40\x20\40\40\40\40\x20\40\x20\x20\40\40\40\x20\40\40\x20\40\40\x20\40\74\x70\76\120\154\145\141\163\145\x20\x63\x6f\156\x74\141\x63\164\x20\x79\x6f\x75\162\40\x61\x64\x6d\x69\156\151\163\x74\162\141\x74\x6f\x72\40\141\156\144\40\162\x65\160\157\x72\164\40\164\150\145\x20\146\x6f\x6c\154\157\167\x69\156\147\x20\145\x72\x72\157\x72\x3a\74\57\x70\x3e\xd\12\x20\40\40\40\x20\x20\x20\x20\40\40\40\x20\40\x20\40\40\40\x20\40\40\x20\40\x20\x20\x20\40\x20\40\74\160\x3e\74\163\x74\162\157\x6e\x67\x3e\x50\157\x73\163\151\142\154\145\40\103\141\x75\163\145\72\40\x3c\x2f\x73\164\x72\157\156\x67\76\x27\x58\56\x35\60\x39\40\x43\145\162\x74\x69\x66\151\x63\x61\x74\145\47\x20\x66\x69\145\154\x64\40\x69\156\x20\x70\x6c\165\x67\x69\156\40\144\x6f\x65\x73\x20\x6e\157\164\40\155\x61\x74\x63\x68\x20\164\150\145\40\143\x65\x72\164\x69\x66\151\x63\141\x74\145\40\x66\x6f\x75\156\x64\40\x69\156\x20\x53\x41\115\x4c\40\122\x65\163\160\x6f\156\x73\x65\x2e\74\57\160\76\xd\xa\40\40\x20\40\40\40\x20\40\x20\x20\x20\x20\x20\40\x20\x20\x20\40\40\x20\x20\x20\40\x20\x20\x20\x20\x20\74\x70\x3e\74\163\164\x72\x6f\x6e\x67\x3e\x43\145\x72\164\151\x66\x69\143\x61\164\145\x20\x66\157\165\156\x64\40\151\156\40\x53\x41\x4d\x4c\40\x52\145\x73\160\x6f\156\163\x65\72\40\x3c\57\x73\164\162\157\156\x67\76\74\x66\x6f\156\164\40\146\x61\143\145\75\x22\x43\x6f\165\162\x69\145\x72\x20\x4e\x65\x77\42\76\x3c\x62\162\x3e\74\x62\162\76" . $HE . "\x3c\57\160\x3e\74\x2f\146\x6f\x6e\164\76\15\xa\40\40\x20\40\40\40\40\40\40\40\40\40\x20\x20\x20\40\40\40\x20\40\x20\x20\40\x20\x20\40\x20\40\x3c\x70\x3e\x3c\163\164\162\x6f\x6e\x67\x3e\123\x6f\x6c\165\164\x69\x6f\x6e\x3a\x20\x3c\x2f\x73\164\x72\x6f\x6e\x67\76\74\x2f\x70\x3e\xd\12\40\40\x20\x20\40\40\40\40\x20\40\40\x20\x20\40\x20\x20\x20\x20\x20\x20\40\40\40\x20\x20\x20\40\40\74\157\x6c\x3e\15\xa\x20\x20\40\40\40\40\x20\x20\x20\40\x20\40\40\40\40\40\40\x20\40\x20\40\40\x20\x20\x20\x20\40\40\40\x20\x20\74\x6c\x69\76\x43\157\x70\171\x20\160\x61\163\164\x65\x20\x74\x68\x65\40\x63\x65\x72\x74\151\x66\151\x63\141\164\x65\40\160\162\x6f\x76\151\144\x65\144\40\x61\142\157\x76\x65\40\151\x6e\40\x58\x35\60\71\x20\103\x65\162\x74\x69\146\151\143\x61\x74\x65\x20\x75\x6e\144\x65\x72\40\123\x65\x72\166\151\x63\145\x20\120\x72\x6f\166\151\x64\145\x72\x20\x53\x65\164\x75\x70\x20\164\x61\142\x2e\74\x2f\x6c\151\76\15\xa\x20\x20\40\x20\x20\x20\40\x20\x20\40\40\x20\x20\40\40\40\40\40\x20\x20\40\x20\40\x20\x20\x20\40\x20\x20\x20\x20\74\x6c\x69\76\111\146\x20\151\163\x73\x75\145\x20\x70\145\162\163\x69\163\164\163\x20\144\151\x73\x61\142\x6c\x65\40\74\x62\x3e\103\150\141\162\141\143\164\x65\162\x20\145\x6e\143\x6f\x64\151\x6e\x67\74\57\x62\76\x20\x75\156\x64\145\162\40\x53\x65\162\166\151\x63\x65\x20\120\162\157\x76\x64\x65\x72\40\123\x65\164\165\160\x20\x74\x61\142\56\x3c\x2f\x6c\x69\x3e\xd\12\40\40\x20\x20\40\40\40\x20\x20\x20\40\x20\40\40\x20\40\x20\x20\40\40\40\40\40\40\x20\x20\x20\40\x3c\57\x6f\154\76\xd\12\x20\40\x20\x20\x20\40\40\x20\x20\x20\x20\x20\x20\40\40\x20\40\x20\x20\40\x20\x20\x20\x20\x20\40\x20\40\74\57\x64\151\166\x3e\15\12\x20\x20\x20\x20\40\x20\40\x20\x20\40\40\x20\40\40\x20\x20\40\40\40\x20\x20\40\x20\40\x3c\144\151\x76\x20\x73\164\x79\154\145\x3d\x22\155\141\x72\x67\x69\x6e\72\x33\45\73\x64\x69\x73\x70\x6c\x61\x79\x3a\x62\154\157\143\153\73\164\145\x78\x74\x2d\x61\154\151\x67\x6e\72\x63\x65\x6e\x74\145\x72\x3b\x22\x3e\15\xa\40\x20\40\40\x20\40\x20\40\x20\40\x20\40\x20\40\40\40\x20\x20\40\40\x20\x20\x20\x20\40\x20\x20\x20\40\x20\40\40\x3c\x64\x69\x76\40\163\164\171\x6c\145\x3d\x22\x6d\x61\162\x67\x69\156\x3a\x33\x25\x3b\x64\151\x73\160\x6c\x61\171\x3a\x62\x6c\x6f\x63\153\x3b\x74\145\170\x74\x2d\x61\154\x69\147\x6e\72\x63\x65\156\164\x65\162\x3b\42\76\74\x69\x6e\x70\165\164\x20\163\164\x79\x6c\145\75\x22\x70\x61\x64\x64\x69\156\x67\72\61\45\x3b\x77\151\x64\x74\x68\x3a\61\60\x30\x70\170\x3b\x62\141\143\x6b\147\x72\x6f\165\x6e\x64\x3a\40\43\x30\60\x39\61\103\104\x20\156\157\x6e\x65\x20\162\145\x70\x65\141\164\x20\x73\143\x72\x6f\154\154\x20\x30\45\x20\x30\x25\x3b\143\x75\x72\x73\157\162\72\x20\x70\x6f\x69\x6e\164\145\162\x3b\x66\x6f\156\164\55\163\x69\172\145\72\61\x35\x70\170\x3b\142\x6f\162\x64\145\x72\x2d\167\x69\x64\x74\x68\72\40\x31\x70\170\x3b\x62\x6f\x72\144\145\x72\x2d\163\x74\x79\x6c\x65\x3a\40\x73\x6f\x6c\151\144\x3b\142\157\162\x64\145\162\x2d\162\x61\144\151\165\x73\x3a\40\x33\x70\x78\73\x77\150\151\164\145\55\163\160\141\x63\145\72\x20\x6e\x6f\x77\162\x61\x70\x3b\x62\x6f\x78\x2d\x73\x69\172\151\x6e\x67\72\40\x62\157\162\144\x65\x72\x2d\142\157\x78\x3b\x62\x6f\x72\144\145\x72\55\143\157\154\x6f\162\x3a\40\43\60\60\x37\63\x41\101\73\x62\157\170\x2d\x73\150\141\144\157\167\x3a\40\60\x70\170\40\x31\x70\x78\x20\60\x70\x78\40\x72\147\142\x61\50\x31\x32\x30\x2c\40\62\60\x30\x2c\40\62\x33\x30\54\40\60\x2e\66\51\40\151\156\163\x65\x74\x3b\x63\157\154\x6f\162\x3a\x20\43\106\x46\x46\73\42\164\x79\x70\x65\x3d\x22\142\165\x74\x74\157\x6e\x22\x20\166\141\x6c\165\145\75\42\x44\157\x6e\x65\x22\x20\157\x6e\x43\154\x69\143\153\x3d\42\163\x65\x6c\x66\x2e\143\154\157\163\x65\50\x29\x3b\42\x3e\x3c\57\x64\151\x76\76";
    mo_saml_download_logs($Tf, $b3);
    exit;
    xI:
    uI:
    $Jm = get_site_option("\x73\141\155\x6c\137\x69\163\163\165\145\162");
    $P6 = get_site_option("\155\157\x5f\163\x61\x6d\154\x5f\x73\160\137\x65\x6e\x74\x69\164\171\x5f\151\x64");
    if (!empty($P6)) {
        goto S3;
    }
    $P6 = $ia . "\57\167\x70\x2d\x63\x6f\x6e\x74\x65\x6e\x74\x2f\160\154\165\x67\x69\x6e\163\x2f\x6d\151\156\x69\x6f\162\141\x6e\147\145\x2d\163\x61\x6d\x6c\55\62\x30\55\x73\151\x6e\147\154\145\55\x73\x69\147\156\x2d\157\x6e\57";
    S3:
    Utilities::validateIssuerAndAudience($sn, $P6, $Jm, $Z0);
    $xd = current(current($sn->getAssertions())->getNameId());
    $pS = current($sn->getAssertions())->getAttributes();
    $pS["\116\x61\155\x65\111\x44"] = array("\x30" => $xd);
    $L_ = current($sn->getAssertions())->getSessionIndex();
    mo_saml_checkMapping($pS, $Z0, $L_);
    goto yy;
    i5:
    if (!isset($_REQUEST["\122\145\x6c\x61\x79\x53\164\x61\164\x65"])) {
        goto iq;
    }
    $tR = $_REQUEST["\x52\145\x6c\141\x79\x53\164\141\x74\x65"];
    iq:
    if (!is_user_logged_in()) {
        goto Lm;
    }
    wp_logout();
    Lm:
    if (empty($tR)) {
        goto oq;
    }
    $tR = mo_saml_parse_url($tR);
    goto dn;
    oq:
    $tR = $ia;
    dn:
    header("\x4c\157\143\x61\164\151\x6f\156\x3a" . $tR);
    exit;
    yy:
    H8:
    if (!(array_key_exists("\123\x41\115\x4c\122\145\x71\165\145\163\x74", $_REQUEST) && !empty($_REQUEST["\123\x41\115\x4c\122\145\161\165\145\x73\x74"]))) {
        goto mt;
    }
    $Da = $_REQUEST["\x53\x41\115\114\x52\x65\x71\x75\x65\x73\x74"];
    $Z0 = "\x2f";
    if (!array_key_exists("\x52\x65\154\x61\x79\x53\x74\x61\x74\145", $_REQUEST)) {
        goto fV;
    }
    $Z0 = $_REQUEST["\x52\x65\x6c\x61\171\x53\164\141\164\x65"];
    fV:
    $Da = base64_decode($Da);
    if (!(array_key_exists("\123\x41\115\114\x52\145\x71\165\145\163\164", $_GET) && !empty($_GET["\123\101\115\x4c\122\145\161\165\x65\x73\x74"]))) {
        goto g2;
    }
    $Da = gzinflate($Da);
    g2:
    $Lx = new DOMDocument();
    $Lx->loadXML($Da);
    $Ew = $Lx->firstChild;
    if (!($Ew->localName == "\114\157\x67\157\165\164\x52\x65\161\165\x65\x73\x74")) {
        goto kc;
    }
    $DD = new SAML2_LogoutRequest($Ew);
    if (!(!session_id() || session_id() == '' || !isset($_SESSION))) {
        goto u9;
    }
    session_start();
    u9:
    $_SESSION["\x6d\x6f\x5f\x73\x61\155\154\x5f\154\157\147\x6f\165\x74\137\162\145\161\x75\145\163\x74"] = $Da;
    $_SESSION["\155\157\137\x73\x61\x6d\154\137\154\x6f\x67\157\165\x74\x5f\x72\145\x6c\x61\x79\x5f\163\164\x61\164\x65"] = $Z0;
    wp_redirect(htmlspecialchars_decode(wp_logout_url()));
    exit;
    kc:
    mt:
    if (!(isset($_REQUEST["\x6f\160\164\151\157\156"]) and !is_array($_REQUEST["\x6f\160\x74\x69\x6f\156"]) and strpos($_REQUEST["\x6f\160\164\151\157\x6e"], "\162\145\x61\x64\163\141\x6d\154\154\x6f\x67\x69\x6e") !== false)) {
        goto rW;
    }
    require_once dirname(__FILE__) . "\x2f\151\156\x63\154\x75\144\145\x73\x2f\154\x69\x62\x2f\145\x6e\143\162\171\x70\164\x69\x6f\x6e\x2e\160\150\160";
    if (isset($_POST["\x53\x54\101\x54\x55\123"]) && $_POST["\123\x54\101\x54\x55\123"] == "\105\122\122\x4f\122") {
        goto iU;
    }
    if (!(isset($_POST["\x53\x54\x41\x54\x55\123"]) && $_POST["\x53\x54\x41\x54\125\123"] == "\x53\x55\103\103\105\123\x53")) {
        goto hI;
    }
    $dh = '';
    if (!(isset($_REQUEST["\162\145\x64\x69\x72\145\143\x74\x5f\x74\157"]) && !empty($_REQUEST["\162\x65\x64\x69\162\145\143\x74\x5f\x74\x6f"]) && $_REQUEST["\162\x65\144\x69\162\145\143\x74\137\164\157"] != "\57")) {
        goto sO;
    }
    $dh = $_REQUEST["\162\x65\144\151\162\x65\x63\164\137\x74\157"];
    sO:
    delete_site_option("\x6d\x6f\x5f\163\141\x6d\x6c\137\162\x65\x64\x69\x72\x65\143\164\137\x65\162\x72\x6f\x72\137\x63\x6f\144\145");
    delete_site_option("\x6d\157\137\x73\141\155\x6c\x5f\162\145\x64\151\162\145\x63\164\x5f\145\x72\x72\157\162\x5f\162\x65\x61\163\157\x6e");
    try {
        $y2 = get_site_option("\x73\x61\x6d\x6c\x5f\141\x6d\x5f\145\x6d\x61\x69\x6c");
        $Ag = get_site_option("\x73\141\155\x6c\137\x61\155\x5f\x75\x73\145\x72\x6e\x61\155\145");
        $jw = get_site_option("\163\141\155\x6c\137\141\155\137\x66\x69\162\x73\164\137\x6e\141\155\145");
        $bg = get_site_option("\163\x61\155\x6c\137\141\155\x5f\154\x61\163\x74\x5f\156\x61\x6d\145");
        $fn = get_site_option("\163\141\x6d\154\137\x61\x6d\x5f\147\x72\157\x75\160\x5f\x6e\x61\x6d\145");
        $C6 = get_site_option("\x73\141\155\154\137\141\x6d\137\144\x65\146\141\x75\154\164\x5f\165\163\145\162\x5f\x72\157\154\x65");
        $fQ = get_site_option("\163\x61\x6d\x6c\137\141\x6d\x5f\x64\157\156\164\137\141\x6c\154\x6f\167\x5f\x75\x6e\x6c\x69\x73\x74\145\144\137\x75\x73\x65\162\137\162\x6f\x6c\x65");
        $SL = get_site_option("\x73\x61\x6d\154\x5f\141\155\x5f\x61\143\x63\157\165\x6e\164\137\x6d\141\x74\x63\x68\145\162");
        $SJ = '';
        $T0 = '';
        $jw = str_replace("\56", "\x5f", $jw);
        $jw = str_replace("\x20", "\x5f", $jw);
        if (!(!empty($jw) && array_key_exists($jw, $_POST))) {
            goto Lj;
        }
        $jw = $_POST[$jw];
        Lj:
        $bg = str_replace("\56", "\137", $bg);
        $bg = str_replace("\x20", "\x5f", $bg);
        if (!(!empty($bg) && array_key_exists($bg, $_POST))) {
            goto hz;
        }
        $bg = $_POST[$bg];
        hz:
        $Ag = str_replace("\56", "\137", $Ag);
        $Ag = str_replace("\x20", "\137", $Ag);
        if (!empty($Ag) && array_key_exists($Ag, $_POST)) {
            goto wv;
        }
        $T0 = $_POST["\116\141\x6d\145\111\x44"];
        goto vI;
        wv:
        $T0 = $_POST[$Ag];
        vI:
        $SJ = str_replace("\56", "\x5f", $y2);
        $SJ = str_replace("\40", "\x5f", $y2);
        if (!empty($y2) && array_key_exists($y2, $_POST)) {
            goto PR;
        }
        $SJ = $_POST["\x4e\141\x6d\x65\111\x44"];
        goto wU;
        PR:
        $SJ = $_POST[$y2];
        wU:
        $fn = str_replace("\x2e", "\x5f", $fn);
        $fn = str_replace("\40", "\x5f", $fn);
        if (!(!empty($fn) && array_key_exists($fn, $_POST))) {
            goto Dd;
        }
        $fn = $_POST[$fn];
        Dd:
        if (!empty($SL)) {
            goto M5;
        }
        $SL = "\145\155\x61\x69\x6c";
        M5:
        $XC = get_site_option("\x6d\157\137\163\141\155\x6c\x5f\x63\165\x73\x74\x6f\x6d\145\162\x5f\x74\x6f\x6b\x65\x6e");
        if (!(isset($XC) || trim($XC) != '')) {
            goto oi;
        }
        $br = AESEncryption::decrypt_data($SJ, $XC);
        $SJ = $br;
        oi:
        if (!(!empty($jw) && !empty($XC))) {
            goto kB;
        }
        $J0 = AESEncryption::decrypt_data($jw, $XC);
        $jw = $J0;
        kB:
        if (!(!empty($bg) && !empty($XC))) {
            goto su;
        }
        $Hu = AESEncryption::decrypt_data($bg, $XC);
        $bg = $Hu;
        su:
        if (!(!empty($T0) && !empty($XC))) {
            goto t5;
        }
        $VJ = AESEncryption::decrypt_data($T0, $XC);
        $T0 = $VJ;
        t5:
        if (!(!empty($fn) && !empty($XC))) {
            goto aq;
        }
        $NQ = AESEncryption::decrypt_data($fn, $XC);
        $fn = $NQ;
        aq:
    } catch (Exception $wD) {
        echo sprintf("\101\156\40\145\162\x72\157\x72\40\157\143\143\165\x72\162\x65\x64\x20\167\x68\151\x6c\x65\x20\160\162\157\x63\145\163\x73\x69\x6e\x67\x20\x74\x68\x65\x20\x53\x41\x4d\114\40\122\145\x73\160\157\156\163\145\x2e");
        exit;
    }
    $R3 = array($fn);
    mo_saml_login_user($SJ, $jw, $bg, $T0, $R3, $fQ, $C6, $dh, $SL);
    hI:
    goto vz;
    iU:
    update_site_option("\155\157\137\x73\x61\155\154\x5f\162\145\144\151\x72\x65\143\x74\x5f\x65\162\x72\157\162\x5f\143\157\144\145", $_POST["\x45\122\122\117\x52\x5f\x52\105\x41\123\x4f\116"]);
    update_site_option("\x6d\157\137\163\141\x6d\154\x5f\162\x65\x64\x69\162\145\143\164\x5f\x65\162\162\x6f\x72\x5f\162\x65\x61\x73\157\156", $_POST["\105\122\x52\117\x52\137\115\105\123\x53\x41\107\105"]);
    vz:
    rW:
    Km:
}
function mo_saml_relaystate_url($Z0)
{
    $fg = parse_url($Z0, PHP_URL_SCHEME);
    $Z0 = str_replace($fg . "\72\57\57", '', $Z0);
    return $Z0;
}
function mo_saml_hash_relaystate($Z0)
{
    $fg = parse_url($Z0, PHP_URL_SCHEME);
    $Z0 = str_replace($fg . "\x3a\57\x2f", '', $Z0);
    $Z0 = base64_encode($Z0);
    $GU = cdjsurkhh($Z0);
    $Z0 = $Z0 . "\56" . $GU;
    return $Z0;
}
function mo_saml_get_relaystate($Z0)
{
    if (!filter_var($Z0, FILTER_VALIDATE_URL)) {
        goto fr;
    }
    return $Z0;
    fr:
    $it = strpos($Z0, "\56");
    if ($it) {
        goto ou;
    }
    wp_die("\101\x6e\40\x65\x72\x72\x6f\162\x20\157\x63\143\x75\162\145\144\56\x20\x50\x6c\x65\141\163\145\x20\143\x6f\x6e\164\x61\x63\x74\40\171\157\x75\x72\x20\x61\144\x6d\151\156\151\163\164\x72\x61\x74\157\162\x2e", "\105\x72\162\x6f\x72\x20\72\40\x4e\157\x74\x20\141\x20\x74\162\x75\x73\x74\145\x64\x20\163\x6f\x75\162\x63\x65\x20\x6f\146\x20\164\x68\x65\x20\x53\x41\x4d\114\40\162\x65\163\160\157\x6e\163\145");
    exit;
    ou:
    $tR = substr($Z0, 0, $it);
    $mH = substr($Z0, $it + 1);
    $a7 = cdjsurkhh($tR);
    if (!($mH !== $a7)) {
        goto xU;
    }
    wp_die("\x41\156\40\x65\162\162\157\162\40\x6f\143\143\x75\x72\145\144\56\x20\x50\x6c\x65\x61\x73\x65\40\143\x6f\156\164\141\143\x74\x20\x79\x6f\x75\162\x20\141\x64\155\151\156\151\163\x74\162\141\x74\x6f\x72\x2e", "\x45\x72\x72\x6f\162\40\x3a\40\116\x6f\164\40\141\40\x74\x72\x75\163\x74\x65\144\40\163\157\x75\x72\143\145\x20\157\146\40\x74\150\145\x20\x53\x41\115\114\40\x72\145\x73\160\157\156\x73\x65");
    exit;
    xU:
    $tR = base64_decode($tR);
    return $tR;
}
function cdjsurkhh($tb)
{
    $GU = hash("\163\150\x61\65\x31\62", $tb);
    $K7 = substr($GU, 7, 14);
    return $K7;
}
function mo_saml_parse_url($Z0)
{
    if (!($Z0 != "\x74\x65\163\x74\x56\141\154\x69\144\141\x74\x65" && $Z0 != "\x74\x65\163\x74\x4e\x65\167\x43\145\x72\x74\x69\146\151\143\141\164\145")) {
        goto Ak;
    }
    $ia = get_site_option("\x6d\x6f\x5f\x73\141\155\x6c\137\x73\160\137\x62\141\163\x65\x5f\165\x72\154");
    if (!empty($ia)) {
        goto Wu;
    }
    $ia = get_network_site_url();
    Wu:
    $fg = parse_url($ia, PHP_URL_SCHEME);
    if (filter_var($Z0, FILTER_VALIDATE_URL)) {
        goto Ia;
    }
    $Z0 = $fg . "\72\57\x2f" . $Z0;
    Ia:
    Ak:
    return $Z0;
}
function mo_saml_is_subsite($Z0)
{
    $AL = parse_url($Z0, PHP_URL_HOST);
    $OZ = parse_url($Z0, PHP_URL_PATH);
    if (is_subdomain_install()) {
        goto Ol;
    }
    $Mc = strpos($OZ, "\57", 1) != false ? strpos($OZ, "\x2f", 1) : strlen($OZ) - 1;
    $OZ = substr($OZ, 0, $Mc + 1);
    $blog_id = get_blog_id_from_url($AL, $OZ);
    goto yV;
    Ol:
    $blog_id = get_blog_id_from_url($AL);
    yV:
    if ($blog_id !== 0) {
        goto tQ;
    }
    return false;
    goto Hz;
    tQ:
    return true;
    Hz:
}
function mo_saml_show_SAML_log($Ew, $Ts)
{
    header("\x43\157\156\164\x65\156\x74\x2d\124\x79\x70\145\72\40\164\145\170\164\x2f\150\164\x6d\154");
    $ZR = new DOMDocument();
    $ZR->preserveWhiteSpace = false;
    $ZR->formatOutput = true;
    $ZR->loadXML($Ew);
    if ($Ts == "\144\x69\163\x70\154\x61\x79\123\101\115\x4c\122\x65\161\165\145\x73\164") {
        goto sm;
    }
    $hL = "\x53\x41\115\114\x20\x52\145\163\160\157\x6e\163\145";
    goto SF;
    sm:
    $hL = "\x53\x41\x4d\114\40\x52\145\x71\165\145\163\x74";
    SF:
    $L2 = $ZR->saveXML();
    $YH = htmlentities($L2);
    $YH = rtrim($YH);
    $mf = simplexml_load_string($L2);
    $yj = json_encode($mf);
    $Ad = json_decode($yj);
    $Tz = plugins_url("\x69\x6e\x63\x6c\165\x64\145\163\57\x63\x73\x73\57\163\x74\x79\x6c\x65\137\163\x65\x74\x74\151\156\x67\x73\56\143\163\x73\77\166\145\x72\x3d\64\x2e\70\56\64\60", __FILE__);
    echo "\x3c\154\x69\x6e\153\40\x72\x65\154\75\x27\x73\x74\x79\x6c\x65\x73\x68\x65\x65\164\x27\40\x69\x64\75\x27\x6d\157\137\x73\x61\x6d\154\137\x61\144\x6d\x69\x6e\x5f\163\145\164\x74\x69\x6e\147\x73\137\x73\x74\171\x6c\x65\x2d\x63\x73\163\x27\x20\x20\x68\x72\x65\146\x3d\x27" . $Tz . "\47\x20\x74\171\160\x65\75\x27\x74\x65\170\x74\x2f\x63\163\x73\47\x20\x6d\x65\x64\151\141\x3d\47\141\154\154\x27\40\x2f\76\xd\12\15\xa\74\144\151\x76\40\x63\x6c\x61\x73\x73\x3d\x22\155\x6f\55\144\151\163\x70\154\x61\171\x2d\154\x6f\147\x73\x22\40\x3e\x3c\160\x20\164\171\x70\x65\75\x22\164\145\170\x74\42\x20\x20\40\151\x64\75\x22\x53\x41\115\114\x5f\x74\171\160\145\42\76" . $hL . "\x3c\x2f\x70\76\74\x2f\144\x69\166\x3e\15\xa\15\12\x3c\x64\151\166\40\164\x79\x70\x65\75\42\164\145\x78\164\x22\x20\151\144\75\42\123\101\115\x4c\137\144\151\x73\160\154\x61\x79\42\x20\x63\154\141\x73\163\x3d\x22\155\x6f\x2d\144\151\163\x70\154\x61\x79\55\x62\154\157\x63\153\x22\76\x3c\160\162\x65\x20\143\x6c\x61\163\163\75\47\x62\162\x75\x73\x68\x3a\40\x78\x6d\154\73\x27\76" . $YH . "\74\57\160\162\x65\x3e\74\x2f\x64\x69\166\76\15\xa\x3c\142\162\x3e\15\12\x3c\144\x69\x76\x9\40\x73\x74\171\154\x65\x3d\x22\155\141\162\147\151\x6e\72\63\x25\73\144\x69\163\160\154\x61\x79\x3a\x62\154\x6f\x63\153\73\164\x65\170\x74\x2d\141\154\151\x67\x6e\72\x63\145\156\x74\x65\162\x3b\x22\x3e\15\12\15\12\74\144\151\166\x20\163\164\171\x6c\x65\75\42\x6d\141\x72\147\x69\x6e\72\x33\45\73\144\151\163\x70\x6c\x61\171\72\x62\154\157\x63\153\x3b\164\145\x78\164\x2d\x61\154\151\147\x6e\72\x63\145\156\164\145\162\73\x22\40\76\xd\xa\xd\xa\x3c\x2f\144\151\166\76\15\xa\x3c\142\165\164\x74\x6f\x6e\40\151\144\75\42\x63\157\x70\171\x22\x20\x6f\156\x63\154\x69\143\153\x3d\x22\143\157\x70\x79\104\x69\x76\x54\157\103\x6c\x69\x70\x62\157\141\162\x64\x28\x29\x22\40\x20\x73\x74\171\x6c\x65\x3d\42\x70\x61\144\144\151\156\147\72\x31\45\x3b\x77\151\x64\x74\x68\x3a\x31\x30\x30\160\170\x3b\142\141\x63\153\x67\162\157\x75\x6e\144\x3a\40\43\60\x30\x39\61\103\x44\40\x6e\x6f\156\145\40\x72\145\x70\145\x61\164\40\x73\143\x72\157\x6c\154\x20\60\x25\x20\x30\45\x3b\x63\x75\162\163\157\x72\x3a\40\x70\157\151\156\164\x65\162\x3b\146\x6f\156\x74\x2d\163\x69\x7a\x65\72\61\x35\x70\170\73\142\x6f\162\144\x65\162\55\167\x69\144\164\150\72\x20\x31\160\x78\73\142\157\162\144\x65\162\55\163\x74\x79\154\x65\x3a\40\163\x6f\154\x69\x64\x3b\x62\157\162\144\x65\162\55\162\141\144\x69\x75\x73\72\x20\63\160\170\x3b\x77\x68\x69\x74\145\x2d\x73\x70\x61\143\145\x3a\x20\x6e\157\x77\x72\x61\x70\x3b\142\157\170\x2d\163\x69\x7a\x69\156\x67\72\40\x62\x6f\x72\x64\x65\x72\55\x62\157\170\x3b\142\157\162\x64\x65\162\55\143\x6f\x6c\x6f\x72\72\40\43\x30\60\x37\63\x41\x41\x3b\142\157\x78\55\x73\x68\x61\x64\x6f\167\x3a\40\60\x70\170\x20\x31\160\170\x20\x30\160\170\x20\162\147\142\x61\50\x31\x32\x30\x2c\40\62\x30\60\x2c\x20\62\63\60\x2c\x20\60\56\x36\x29\x20\151\156\163\x65\x74\x3b\143\157\154\157\162\x3a\x20\43\x46\x46\x46\73\42\x20\x3e\103\x6f\160\x79\x3c\57\x62\165\x74\164\x6f\x6e\76\xd\xa\46\156\142\163\160\x3b\xd\12\x3c\x69\x6e\160\165\x74\40\x69\144\x3d\42\x64\167\x6e\x2d\x62\164\156\x22\x20\x73\x74\171\x6c\x65\75\x22\160\141\x64\144\x69\156\x67\72\x31\x25\x3b\x77\x69\x64\x74\x68\x3a\61\x30\60\160\x78\x3b\142\141\x63\153\x67\x72\x6f\165\x6e\144\72\40\43\x30\x30\x39\x31\103\104\x20\156\157\156\145\40\x72\x65\160\x65\141\x74\40\163\x63\162\x6f\154\x6c\40\x30\x25\x20\x30\45\x3b\x63\165\162\163\157\x72\x3a\x20\x70\x6f\151\156\x74\145\x72\73\146\x6f\x6e\x74\55\x73\x69\x7a\x65\72\x31\x35\160\x78\x3b\x62\157\162\x64\x65\162\55\x77\x69\144\164\150\x3a\x20\x31\160\x78\73\x62\157\x72\144\x65\x72\55\x73\x74\171\154\145\72\x20\163\157\x6c\x69\x64\73\x62\x6f\162\x64\x65\162\55\162\x61\x64\151\165\x73\72\40\x33\x70\x78\x3b\x77\x68\151\x74\x65\55\163\160\x61\x63\x65\x3a\x20\x6e\157\167\x72\141\160\x3b\142\x6f\x78\55\163\151\x7a\151\156\147\x3a\x20\x62\157\x72\144\145\162\x2d\x62\157\170\73\142\x6f\162\x64\x65\162\x2d\x63\157\154\157\162\72\40\43\x30\60\x37\63\101\101\x3b\x62\x6f\x78\55\x73\x68\141\x64\x6f\167\x3a\x20\x30\160\x78\40\x31\x70\170\40\60\x70\x78\40\162\147\x62\141\x28\61\x32\60\x2c\x20\62\60\60\x2c\40\x32\63\60\x2c\x20\x30\x2e\66\51\40\151\156\163\x65\164\73\x63\x6f\154\x6f\162\72\x20\x23\106\106\x46\x3b\42\x74\171\160\x65\x3d\x22\x62\x75\164\x74\x6f\x6e\42\x20\x76\141\x6c\165\145\75\42\104\157\167\156\154\157\141\144\42\40\xd\12\x22\x3e\xd\xa\74\57\x64\x69\x76\x3e\15\12\74\57\x64\151\166\x3e\xd\xa\15\12\15\12";
    ob_end_flush();
    echo "\15\12\x3c\163\x63\162\151\160\x74\x3e\xd\12\15\xa\146\x75\156\x63\x74\x69\157\156\40\x63\157\160\171\x44\151\x76\124\x6f\103\154\151\x70\142\x6f\x61\x72\x64\x28\51\x20\x7b\15\xa\166\x61\162\x20\x61\x75\170\40\x3d\x20\144\157\143\x75\x6d\145\156\164\x2e\143\x72\x65\x61\164\145\x45\x6c\x65\x6d\x65\x6e\164\50\x22\x69\156\160\165\164\42\51\73\xd\xa\141\165\x78\56\163\x65\164\x41\x74\x74\x72\x69\x62\165\164\145\x28\x22\166\x61\x6c\165\145\x22\54\40\x64\x6f\143\165\155\x65\x6e\x74\x2e\x67\145\164\x45\154\x65\x6d\x65\156\164\x42\x79\111\x64\x28\x22\x53\x41\115\114\x5f\x64\151\x73\160\x6c\x61\171\42\51\x2e\x74\x65\170\164\103\x6f\x6e\x74\x65\156\x74\51\x3b\15\12\144\x6f\143\165\x6d\145\156\x74\x2e\x62\x6f\144\171\56\x61\x70\x70\x65\x6e\144\x43\150\x69\154\144\x28\x61\165\x78\51\73\xd\xa\x61\165\x78\56\163\145\x6c\145\143\x74\x28\51\x3b\15\xa\x64\x6f\143\165\155\145\x6e\x74\x2e\x65\x78\x65\143\x43\x6f\155\155\141\x6e\144\x28\x22\143\x6f\x70\x79\42\51\73\15\12\144\x6f\x63\165\155\x65\x6e\x74\56\x62\157\144\x79\56\162\x65\x6d\157\x76\145\x43\150\151\154\144\50\x61\165\x78\x29\73\xd\xa\144\x6f\143\x75\155\x65\x6e\164\56\x67\145\x74\105\x6c\145\155\x65\x6e\x74\x42\171\x49\144\x28\x27\143\x6f\x70\171\47\51\56\164\x65\x78\x74\103\x6f\x6e\164\145\156\164\x20\x3d\40\x22\103\157\160\x69\145\x64\x22\73\15\xa\144\157\143\165\155\145\156\164\x2e\x67\145\x74\105\154\145\x6d\145\156\164\x42\x79\x49\x64\x28\x27\x63\x6f\x70\x79\x27\x29\x2e\163\164\x79\x6c\x65\x2e\142\141\143\x6b\147\x72\157\165\x6e\144\40\75\40\x22\147\x72\x65\x79\x22\73\15\12\167\x69\x6e\x64\157\x77\x2e\147\x65\164\123\145\x6c\145\143\164\x69\x6f\x6e\x28\x29\x2e\163\145\x6c\x65\143\164\x41\154\154\103\x68\151\x6c\144\x72\x65\156\50\x20\144\x6f\143\165\x6d\145\156\x74\56\147\145\x74\105\x6c\x65\x6d\x65\156\164\x42\171\x49\x64\50\40\x22\x53\x41\115\x4c\x5f\144\x69\163\x70\x6c\141\x79\x22\40\x29\40\51\73\15\xa\15\12\175\xd\12\xd\12\146\x75\156\x63\164\151\157\156\40\x64\157\x77\x6e\154\x6f\x61\144\50\x66\151\x6c\x65\x6e\141\x6d\x65\54\x20\164\145\x78\x74\x29\40\x7b\15\xa\166\141\x72\40\x65\154\145\x6d\x65\x6e\x74\40\x3d\40\x64\x6f\x63\165\x6d\145\x6e\164\56\143\162\145\141\164\145\x45\154\145\x6d\x65\156\164\x28\x27\x61\x27\x29\73\15\12\145\x6c\x65\155\145\156\164\x2e\163\145\164\101\x74\x74\162\x69\142\x75\164\145\x28\47\x68\162\x65\146\x27\x2c\40\x27\x64\x61\x74\x61\72\101\x70\160\154\x69\x63\141\x74\x69\157\156\x2f\x6f\143\164\145\x74\55\163\164\162\145\141\x6d\x3b\x63\150\141\162\x73\145\164\75\x75\x74\146\x2d\x38\x2c\47\40\x2b\x20\x65\156\143\157\144\145\125\x52\x49\x43\x6f\x6d\160\157\156\145\156\164\50\x74\145\170\x74\51\x29\x3b\xd\12\145\154\145\155\145\156\164\x2e\163\x65\164\x41\164\x74\162\x69\x62\165\164\145\x28\47\x64\x6f\x77\x6e\154\157\x61\x64\x27\x2c\x20\146\151\154\x65\156\x61\x6d\145\x29\73\xd\12\xd\xa\145\x6c\145\155\145\156\x74\x2e\163\164\x79\x6c\145\x2e\144\x69\x73\x70\x6c\141\171\40\75\x20\47\156\157\x6e\145\x27\73\15\12\144\157\x63\165\155\145\156\164\56\x62\x6f\x64\x79\56\x61\160\160\145\x6e\144\x43\x68\151\x6c\x64\x28\x65\x6c\x65\155\x65\156\164\51\x3b\15\12\xd\xa\145\x6c\145\x6d\145\x6e\164\56\x63\x6c\x69\x63\x6b\50\x29\x3b\15\12\xd\12\144\x6f\143\165\155\x65\x6e\x74\x2e\142\x6f\144\x79\x2e\x72\145\x6d\157\x76\145\103\x68\x69\154\x64\x28\x65\154\x65\155\x65\x6e\x74\x29\73\15\12\x7d\15\12\15\xa\x64\x6f\x63\x75\155\145\156\164\56\x67\145\164\105\x6c\145\155\x65\156\164\102\171\x49\144\50\42\144\x77\x6e\x2d\142\x74\156\42\51\56\x61\144\144\105\166\x65\x6e\x74\114\151\163\164\x65\x6e\145\x72\x28\42\143\x6c\151\143\x6b\42\54\40\146\165\156\x63\x74\x69\x6f\x6e\x20\50\51\x20\x7b\15\xa\xd\12\166\141\x72\40\146\151\154\145\x6e\141\x6d\145\x20\x3d\x20\144\x6f\143\165\155\x65\156\x74\56\147\x65\164\x45\x6c\145\x6d\x65\156\x74\102\171\x49\144\50\x22\123\101\115\x4c\x5f\x74\x79\x70\145\42\x29\x2e\164\x65\170\x74\x43\x6f\156\164\x65\x6e\x74\53\42\x2e\170\155\154\x22\x3b\xd\12\166\x61\x72\x20\156\x6f\144\145\x20\75\x20\x64\157\x63\165\x6d\145\x6e\x74\x2e\x67\x65\x74\105\x6c\x65\155\145\156\164\102\171\111\x64\x28\42\123\x41\x4d\x4c\137\144\x69\x73\x70\154\141\x79\x22\x29\73\15\xa\150\x74\x6d\154\103\157\x6e\x74\x65\x6e\164\x20\x3d\x20\156\157\x64\x65\56\x69\156\156\x65\162\110\x54\x4d\114\x3b\15\xa\164\145\x78\x74\40\75\x20\x6e\157\x64\145\x2e\x74\145\170\x74\103\157\156\x74\145\156\x74\73\xd\12\144\157\167\x6e\x6c\x6f\141\x64\x28\146\151\154\x65\x6e\x61\x6d\x65\54\x20\164\145\170\x74\51\73\xd\12\x7d\54\x20\146\141\154\x73\x65\x29\73\15\12\15\12\15\xa\xd\xa\xd\xa\15\xa\74\x2f\163\x63\x72\151\160\164\x3e\15\xa";
    exit;
}
function mo_saml_checkMapping($pS, $Z0, $L_)
{
    try {
        $y2 = get_site_option("\x73\x61\155\154\137\x61\155\x5f\x65\155\141\151\154");
        $Ag = get_site_option("\x73\x61\x6d\154\137\x61\155\137\165\x73\x65\x72\156\x61\x6d\145");
        $jw = get_site_option("\163\x61\155\154\137\141\x6d\x5f\x66\151\162\163\164\x5f\156\x61\x6d\x65");
        $bg = get_site_option("\163\x61\155\154\137\141\155\137\x6c\141\x73\x74\x5f\156\x61\x6d\145");
        $fn = get_site_option("\163\x61\x6d\154\137\x61\155\x5f\x67\162\x6f\165\x70\137\156\x61\x6d\x65");
        $wW = array();
        $wW = maybe_unserialize(get_site_option("\163\141\155\x6c\137\x61\x6d\137\x72\x6f\x6c\x65\137\x6d\x61\160\160\x69\x6e\147"));
        $SL = get_site_option("\x73\x61\155\x6c\x5f\141\x6d\137\x61\143\x63\157\165\x6e\x74\x5f\x6d\141\x74\143\x68\x65\162");
        $SJ = '';
        $T0 = '';
        if (empty($pS)) {
            goto rm;
        }
        if (!empty($jw) && array_key_exists($jw, $pS)) {
            goto u7;
        }
        $jw = '';
        goto qQ;
        u7:
        $jw = $pS[$jw][0];
        qQ:
        if (!empty($bg) && array_key_exists($bg, $pS)) {
            goto u_;
        }
        $bg = '';
        goto sQ;
        u_:
        $bg = $pS[$bg][0];
        sQ:
        if (!empty($Ag) && array_key_exists($Ag, $pS)) {
            goto H3;
        }
        $T0 = $pS["\116\141\155\145\111\104"][0];
        goto EF;
        H3:
        $T0 = $pS[$Ag][0];
        EF:
        if (!empty($y2) && array_key_exists($y2, $pS)) {
            goto eg;
        }
        $SJ = $pS["\x4e\141\155\145\111\x44"][0];
        goto Qb;
        eg:
        $SJ = $pS[$y2][0];
        Qb:
        if (!empty($fn) && array_key_exists($fn, $pS)) {
            goto n9;
        }
        $fn = array();
        goto na;
        n9:
        $fn = $pS[$fn];
        na:
        if (!empty($SL)) {
            goto E9;
        }
        $SL = "\145\155\x61\x69\x6c";
        E9:
        rm:
        if ($Z0 == "\x74\x65\x73\164\x56\x61\154\151\x64\x61\x74\145") {
            goto n4;
        }
        if ($Z0 == "\x74\x65\x73\164\x4e\145\167\x43\x65\x72\x74\x69\x66\x69\143\x61\x74\x65") {
            goto Ya;
        }
        mo_saml_login_user($SJ, $jw, $bg, $T0, $fn, $wW, $Z0, $SL, $L_, $pS["\x4e\x61\x6d\x65\111\x44"][0], $pS);
        goto UA;
        n4:
        update_site_option("\x6d\157\137\x73\141\155\154\137\x74\145\x73\164", "\124\145\163\x74\x20\123\x75\143\x63\145\163\163\x66\165\154");
        mo_saml_show_test_result($jw, $bg, $SJ, $fn, $pS, $Z0);
        goto UA;
        Ya:
        update_site_option("\155\157\137\163\141\155\154\137\164\x65\163\x74\137\156\145\167\137\x63\145\162\164", "\124\145\x73\164\x20\163\165\143\143\145\x73\x73\x66\x75\x6c");
        mo_saml_show_test_result($jw, $bg, $SJ, $fn, $pS, $Z0);
        UA:
    } catch (Exception $wD) {
        echo sprintf("\101\x6e\40\x65\x72\x72\x6f\x72\x20\x6f\143\143\165\162\x72\x65\144\40\167\x68\151\154\145\40\160\162\x6f\x63\x65\163\x73\x69\x6e\x67\40\164\150\145\x20\x53\101\115\x4c\x20\x52\145\x73\x70\x6f\x6e\163\145\x2e");
        exit;
    }
}
function mo_saml_show_test_result($jw, $bg, $SJ, $fn, $pS, $Z0)
{
    echo "\x3c\144\151\x76\x20\x73\164\x79\x6c\145\75\x22\x66\x6f\156\x74\55\146\141\155\x69\x6c\171\x3a\x43\141\154\x69\142\x72\x69\x3b\160\x61\x64\x64\151\x6e\147\72\x30\x20\63\45\x3b\42\x3e";
    if (!empty($SJ)) {
        goto Hx;
    }
    echo "\74\x64\x69\166\x20\x73\164\171\154\x65\75\42\143\x6f\x6c\157\162\72\40\x23\x61\x39\64\64\x34\x32\x3b\142\x61\x63\153\x67\162\157\165\x6e\144\55\143\x6f\154\157\x72\72\40\x23\146\62\144\145\x64\x65\x3b\x70\141\144\144\151\x6e\147\72\x20\61\65\160\170\73\x6d\141\162\147\151\x6e\55\x62\157\x74\164\x6f\x6d\72\x20\x32\x30\x70\170\x3b\164\145\x78\164\x2d\x61\154\x69\x67\x6e\72\143\x65\x6e\164\145\x72\73\142\x6f\162\x64\x65\x72\x3a\61\x70\170\x20\x73\157\x6c\151\144\x20\43\105\66\102\x33\x42\x32\73\x66\x6f\x6e\164\55\x73\151\x7a\x65\x3a\61\70\x70\x74\x3b\x22\x3e\x54\105\x53\x54\x20\x46\101\111\x4c\105\x44\x3c\57\144\x69\x76\76\15\12\x20\40\x20\40\40\x20\40\40\x3c\144\x69\166\x20\x73\164\x79\x6c\x65\75\42\x63\x6f\x6c\157\x72\x3a\40\x23\x61\71\x34\64\x34\62\x3b\x66\157\x6e\x74\x2d\x73\x69\172\145\72\61\64\160\164\73\x20\155\141\x72\x67\x69\156\55\x62\157\x74\164\x6f\x6d\x3a\x32\60\160\170\x3b\42\x3e\127\x41\122\116\111\x4e\107\72\x20\x53\157\x6d\145\x20\x41\x74\x74\x72\x69\142\x75\164\x65\x73\x20\x44\x69\144\x20\x4e\157\x74\40\115\141\164\143\x68\56\74\57\x64\151\x76\76\15\xa\40\x20\x20\40\40\40\x20\40\x3c\x64\151\x76\40\163\x74\171\x6c\145\75\42\144\151\x73\x70\154\x61\x79\x3a\142\154\x6f\143\x6b\x3b\x74\145\170\x74\x2d\141\x6c\151\147\x6e\72\x63\x65\156\164\145\x72\73\x6d\141\162\147\151\x6e\x2d\142\x6f\x74\x74\157\x6d\72\64\45\73\42\x3e\x3c\x69\155\147\40\x73\164\x79\x6c\145\x3d\42\x77\151\144\x74\150\72\x31\65\x25\x3b\x22\x73\162\x63\75\x22" . plugin_dir_url(__FILE__) . "\x69\155\x61\147\x65\163\x2f\167\162\157\x6e\x67\56\x70\x6e\x67\x22\x3e\74\57\x64\151\166\x3e";
    goto Gl;
    Hx:
    update_site_option("\155\x6f\x5f\163\x61\x6d\x6c\x5f\164\x65\x73\164\137\x63\157\x6e\146\x69\147\137\x61\x74\x74\x72\x73", $pS);
    echo "\74\x64\151\166\40\163\164\171\154\x65\x3d\42\x63\157\154\157\x72\x3a\40\43\63\x63\x37\66\x33\x64\73\xd\xa\x20\40\40\x20\x20\40\40\40\x62\141\x63\x6b\x67\x72\x6f\165\x6e\144\x2d\143\x6f\154\x6f\x72\x3a\40\43\x64\x66\146\60\144\x38\73\x20\160\141\x64\x64\151\x6e\147\72\x32\x25\73\155\x61\x72\147\x69\x6e\x2d\x62\157\164\x74\x6f\155\72\62\60\x70\x78\73\x74\x65\170\164\55\141\x6c\x69\x67\156\x3a\x63\145\x6e\x74\145\162\x3b\40\x62\x6f\x72\x64\145\x72\x3a\61\160\170\40\163\x6f\154\151\x64\x20\43\101\x45\x44\102\71\101\73\x20\146\x6f\x6e\x74\x2d\x73\x69\x7a\x65\72\61\70\x70\x74\73\42\76\x54\x45\x53\x54\x20\x53\125\x43\x43\x45\x53\123\106\125\114\x3c\57\144\151\166\x3e\xd\xa\x20\40\40\x20\40\40\x20\40\74\x64\x69\x76\x20\x73\x74\171\154\x65\x3d\42\x64\x69\x73\160\154\x61\x79\72\x62\154\x6f\x63\153\x3b\x74\x65\170\x74\x2d\x61\154\x69\147\x6e\72\x63\x65\156\x74\x65\x72\73\155\x61\x72\147\x69\156\55\x62\x6f\164\164\x6f\x6d\72\64\45\x3b\x22\x3e\x3c\151\x6d\x67\40\x73\164\171\x6c\145\75\x22\167\151\144\164\150\72\61\x35\45\73\x22\163\162\x63\75\42" . plugin_dir_url(__FILE__) . "\x69\155\141\x67\x65\163\x2f\147\162\x65\x65\156\x5f\x63\150\145\143\153\x2e\160\156\x67\42\76\74\x2f\144\x69\x76\x3e";
    Gl:
    $rO = $Z0 == "\164\x65\163\164\116\x65\x77\103\x65\162\x74\151\x66\151\143\141\x74\145" ? "\144\x69\x73\x70\154\141\x79\x3a\156\x6f\x6e\x65" : '';
    $RR = get_site_option("\163\x61\155\x6c\137\141\x6d\x5f\141\x63\x63\x6f\x75\x6e\x74\x5f\155\x61\x74\143\150\x65\x72") ? get_site_option("\163\141\155\x6c\137\x61\x6d\x5f\x61\x63\x63\x6f\x75\156\x74\137\155\141\164\x63\x68\145\162") : "\x65\155\141\x69\154";
    if (!($RR == "\x65\155\141\x69\154" && !filter_var($pS["\x4e\141\x6d\145\x49\104"][0], FILTER_VALIDATE_EMAIL))) {
        goto wJ;
    }
    echo "\x3c\160\x3e\74\146\157\x6e\x74\x20\143\x6f\x6c\x6f\x72\75\x22\43\x46\x46\x30\x30\x30\x30\42\x20\163\x74\x79\x6c\145\x3d\x22\x66\157\x6e\164\x2d\x73\151\x7a\x65\72\61\x34\160\x74\42\x3e\x28\x57\141\162\x6e\151\x6e\147\72\40\124\150\145\40\116\x61\x6d\145\x49\104\x20\x76\141\154\x75\x65\x20\151\163\x20\x6e\157\x74\40\x61\x20\x76\141\154\151\x64\40\x45\155\141\x69\154\x20\111\x44\51\x3c\x2f\146\157\156\x74\x3e\74\57\x70\76";
    wJ:
    echo "\x3c\163\x70\141\x6e\x20\163\x74\171\x6c\145\75\x22\146\157\156\164\x2d\163\151\172\145\72\61\64\x70\164\73\x22\x3e\74\x62\76\110\x65\x6c\154\157\74\57\142\76\54\x20" . $SJ . "\74\57\x73\x70\x61\156\76\74\x62\162\x2f\x3e\x3c\160\40\163\164\x79\x6c\145\75\x22\x66\x6f\x6e\x74\55\x77\x65\x69\147\150\164\x3a\x62\157\154\144\x3b\146\157\x6e\x74\55\163\151\x7a\145\x3a\61\64\160\164\73\155\141\162\x67\x69\x6e\x2d\x6c\145\146\x74\x3a\x31\x25\x3b\x22\76\x41\x54\x54\122\111\102\125\x54\105\123\40\x52\105\103\x45\x49\126\105\104\72\x3c\57\160\76\15\xa\x20\x20\x20\x20\74\164\141\142\x6c\145\40\163\x74\171\154\145\75\42\x62\x6f\162\144\x65\162\x2d\x63\157\x6c\x6c\x61\x70\163\x65\x3a\143\157\154\x6c\x61\160\163\x65\x3b\x62\x6f\x72\x64\145\x72\x2d\x73\x70\141\x63\x69\156\147\x3a\x30\73\40\x64\151\163\x70\x6c\x61\x79\x3a\164\141\142\x6c\x65\73\x77\151\x64\x74\150\x3a\x31\60\x30\x25\73\x20\146\x6f\156\164\x2d\x73\151\x7a\145\x3a\61\x34\160\164\73\142\x61\143\153\x67\162\157\165\x6e\144\55\x63\x6f\x6c\157\x72\72\x23\x45\104\x45\104\105\104\73\x22\x3e\xd\xa\x20\40\x20\x20\40\40\40\40\x3c\164\162\x20\163\164\x79\154\x65\75\42\164\145\x78\x74\55\x61\154\x69\x67\x6e\x3a\x63\x65\x6e\164\x65\162\73\42\x3e\74\x74\144\40\x73\x74\171\154\145\x3d\42\146\x6f\156\164\x2d\167\x65\x69\147\150\164\72\x62\x6f\x6c\x64\x3b\142\157\x72\x64\x65\162\x3a\62\160\170\40\163\157\154\x69\x64\40\43\x39\64\71\60\x39\60\x3b\x70\141\144\144\151\156\147\x3a\62\x25\73\x22\76\x41\x54\x54\x52\x49\x42\x55\124\105\x20\116\x41\x4d\105\x3c\57\x74\144\76\74\x74\144\40\163\164\171\154\x65\75\42\x66\x6f\156\x74\55\x77\x65\151\147\x68\x74\72\142\x6f\x6c\x64\x3b\x70\141\144\x64\x69\x6e\147\x3a\x32\x25\73\142\157\162\x64\145\x72\72\x32\160\x78\x20\163\157\x6c\151\x64\x20\x23\71\64\x39\x30\x39\x30\73\x20\x77\157\162\x64\55\167\x72\x61\160\x3a\x62\162\145\141\x6b\55\167\157\162\144\x3b\x22\x3e\101\124\x54\122\x49\x42\x55\124\105\40\x56\101\114\x55\x45\x3c\57\x74\144\x3e\x3c\57\x74\162\76";
    if (!empty($pS)) {
        goto Os;
    }
    echo "\x4e\x6f\x20\101\164\x74\162\151\142\x75\x74\x65\x73\40\122\x65\x63\x65\x69\x76\x65\144\56";
    goto Bn;
    Os:
    foreach ($pS as $XC => $wE) {
        echo "\x3c\x74\162\76\74\164\x64\40\x73\x74\171\154\x65\75\x27\x66\x6f\156\164\55\167\145\x69\x67\150\164\x3a\142\157\x6c\144\73\142\x6f\162\144\145\162\x3a\x32\160\x78\x20\x73\x6f\x6c\x69\x64\40\43\71\x34\x39\60\71\x30\x3b\x70\x61\x64\144\x69\156\147\72\62\x25\73\47\76" . $XC . "\74\x2f\x74\144\x3e\x3c\x74\x64\40\163\164\x79\154\145\75\x27\160\141\x64\x64\151\x6e\147\x3a\62\x25\73\142\157\162\x64\145\162\72\62\x70\x78\40\x73\157\154\151\144\x20\43\x39\64\71\60\71\60\73\x20\167\x6f\x72\144\x2d\x77\x72\141\x70\72\142\x72\145\141\x6b\x2d\x77\x6f\162\x64\x3b\x27\76" . implode("\x3c\150\x72\57\76", $wE) . "\74\x2f\164\x64\x3e\74\57\164\x72\76";
        wB:
    }
    qX:
    Bn:
    echo "\x3c\57\x74\141\142\x6c\x65\76\x3c\57\x64\151\x76\x3e";
    echo "\74\144\x69\x76\x20\x73\x74\171\x6c\x65\x3d\x22\x6d\x61\162\x67\x69\x6e\x3a\x33\x25\73\x64\151\163\160\154\x61\x79\x3a\142\x6c\157\x63\x6b\73\164\x65\x78\164\x2d\x61\x6c\151\147\x6e\x3a\143\145\156\164\x65\162\73\42\x3e\xd\xa\40\40\x20\40\x20\40\40\x20\40\x20\x20\x20\x3c\x69\x6e\160\165\x74\x20\x73\164\171\154\x65\75\x22\160\141\144\x64\151\x6e\147\x3a\x31\x25\73\x77\151\x64\x74\x68\72\62\x35\60\x70\170\73\142\141\x63\x6b\x67\162\157\x75\156\144\72\40\43\60\60\71\x31\x43\x44\x20\x6e\x6f\156\x65\x20\162\x65\x70\145\x61\164\40\163\x63\162\x6f\154\x6c\x20\60\x25\40\60\x25\73\xd\xa\x20\x20\x20\40\x20\x20\x20\x20\40\40\40\x20\143\x75\x72\163\157\162\72\x20\x70\157\x69\156\x74\x65\x72\73\146\x6f\x6e\164\55\163\x69\x7a\145\72\61\x35\160\170\73\x62\x6f\x72\144\145\x72\55\167\x69\144\164\x68\x3a\x20\x31\160\170\x3b\x62\x6f\x72\144\145\162\55\163\x74\171\x6c\145\x3a\x20\163\x6f\x6c\x69\x64\73\142\x6f\x72\x64\x65\162\x2d\x72\141\x64\151\165\x73\x3a\x20\x33\160\170\x3b\x77\x68\x69\164\145\x2d\x73\x70\x61\143\x65\72\xd\12\40\x20\40\x20\x20\40\x20\40\40\x20\40\x20\156\x6f\x77\x72\x61\160\73\142\157\x78\55\x73\151\172\x69\x6e\147\72\40\142\157\162\x64\145\x72\x2d\x62\157\170\73\142\157\x72\x64\x65\162\x2d\x63\157\x6c\157\162\x3a\x20\43\60\60\x37\63\101\x41\x3b\x62\157\170\55\x73\x68\141\x64\x6f\x77\72\40\x30\x70\x78\x20\61\160\x78\x20\x30\x70\170\40\162\147\142\141\x28\x31\62\x30\x2c\x20\62\60\60\54\40\62\63\60\54\x20\x30\56\66\51\x20\151\156\163\145\x74\x3b\x63\157\x6c\157\162\72\x20\x23\106\x46\x46\x3b" . $rO . "\42\xd\xa\40\40\x20\40\40\40\x20\40\40\40\x20\x20\x20\40\40\x20\x74\171\x70\x65\x3d\42\x62\x75\x74\x74\157\156\x22\40\166\x61\154\165\x65\x3d\42\x43\x6f\156\x66\x69\x67\165\x72\145\40\101\164\164\x72\151\x62\x75\164\x65\x2f\122\157\154\145\x20\x4d\141\160\x70\151\156\147\42\40\x6f\156\103\x6c\151\x63\x6b\75\42\x63\154\x6f\x73\145\x5f\141\x6e\x64\x5f\162\x65\x64\151\162\x65\143\164\50\51\73\42\76\x20\x26\x6e\142\163\x70\73\40\xd\xa\x20\40\40\40\x20\40\40\x20\40\x20\x20\x20\x20\40\40\x20\xd\xa\x20\40\x20\40\x20\40\x20\x20\x20\40\x20\40\x3c\151\x6e\x70\165\x74\x20\x73\x74\x79\x6c\x65\75\42\160\x61\x64\144\x69\x6e\147\x3a\x31\x25\x3b\167\151\x64\x74\150\72\61\60\x30\x70\170\73\142\141\143\153\x67\x72\x6f\165\x6e\144\72\x20\43\x30\x30\71\x31\103\x44\x20\x6e\157\x6e\145\40\162\x65\x70\x65\141\164\40\163\143\x72\157\154\154\x20\60\x25\40\x30\45\x3b\143\165\162\x73\157\x72\x3a\x20\160\157\151\x6e\x74\x65\x72\73\146\x6f\156\x74\55\x73\151\172\145\x3a\61\65\160\170\73\x62\157\x72\144\145\x72\x2d\167\x69\x64\x74\x68\x3a\40\x31\160\x78\73\142\157\x72\144\x65\x72\x2d\x73\164\171\x6c\145\x3a\40\x73\157\154\x69\x64\x3b\142\157\x72\144\x65\162\55\x72\x61\x64\151\165\163\x3a\x20\63\x70\x78\x3b\167\150\x69\164\x65\55\163\x70\x61\143\x65\x3a\x20\x6e\157\167\162\141\x70\x3b\142\157\x78\x2d\163\x69\172\151\156\147\72\40\142\157\x72\144\x65\162\x2d\x62\x6f\170\x3b\142\157\x72\144\145\162\x2d\143\157\154\157\162\x3a\x20\43\60\x30\x37\63\101\101\x3b\142\x6f\170\x2d\x73\x68\x61\x64\157\x77\x3a\x20\60\160\170\40\61\160\x78\x20\x30\x70\170\x20\162\x67\x62\141\x28\x31\x32\x30\x2c\40\62\60\x30\54\40\62\63\x30\54\40\x30\x2e\66\x29\40\151\156\x73\x65\x74\73\143\x6f\x6c\x6f\162\x3a\x20\x23\x46\106\x46\73\x22\x74\171\x70\x65\75\x22\142\x75\164\164\x6f\x6e\42\40\166\x61\154\x75\145\x3d\x22\104\157\x6e\x65\x22\40\157\156\x43\154\151\143\x6b\75\x22\163\145\x6c\146\x2e\x63\154\157\163\145\x28\51\73\x22\76\74\57\x64\151\166\x3e\15\12\40\40\40\40\40\40\40\x20\40\40\x20\x20\x20\x20\40\x20\40\x20\x20\40\x20\x20\40\x20\40\40\x20\40\40\x20\40\x20\x3c\163\143\162\x69\160\164\x3e\xd\12\15\12\40\40\40\x20\40\40\x20\x20\x20\40\40\x20\146\165\156\143\164\151\x6f\156\x20\x63\154\x6f\x73\x65\137\x61\x6e\144\x5f\162\x65\x64\151\x72\145\143\164\x28\x29\x7b\xd\xa\x20\40\40\40\40\x20\x20\40\x20\x20\40\x20\x20\x20\40\x20\167\151\x6e\144\157\x77\x2e\x6f\160\145\x6e\x65\162\56\x72\x65\x64\151\162\x65\x63\x74\x5f\164\157\x5f\x61\164\x74\162\x69\x62\x75\164\145\x5f\155\141\x70\160\x69\156\147\x28\51\73\15\xa\x20\x20\40\x20\40\x20\40\x20\x20\40\40\x20\40\x20\40\40\163\145\154\146\56\x63\x6c\157\163\x65\x28\51\x3b\15\xa\40\x20\x20\x20\40\40\40\40\40\40\x20\x20\175\xd\xa\x20\40\40\x20\40\40\x20\x20\x20\x20\x20\x20\15\12\x20\x20\x20\40\x20\x20\40\40\x20\x20\x20\40\146\165\x6e\143\x74\151\157\x6e\x20\x72\145\x66\162\x65\163\150\x50\141\162\x65\156\x74\x28\51\40\x7b\xd\xa\x20\40\40\x20\x20\x20\x20\x20\x20\40\40\40\x20\x20\40\x20\167\151\156\144\x6f\167\56\157\x70\x65\156\145\x72\x2e\154\157\x63\x61\164\x69\x6f\156\x2e\162\x65\x6c\x6f\x61\144\x28\51\73\15\12\x20\x20\x20\40\x20\40\x20\40\x20\40\x20\x20\x7d\xd\12\x20\x20\40\x20\40\40\x20\40\40\40\x20\40\x3c\57\163\143\x72\x69\x70\x74\76";
    exit;
}
function mo_saml_convert_to_windows_iconv($Qs)
{
    $l7 = get_site_option("\x6d\157\137\x73\x61\x6d\154\x5f\x65\x6e\143\157\144\x69\156\147\x5f\145\156\141\x62\x6c\145\x64");
    if (!($l7 !== "\x63\150\x65\x63\153\x65\x64")) {
        goto si;
    }
    return $Qs;
    si:
    return iconv("\125\124\x46\x2d\x38", "\x43\x50\x31\62\65\x32\x2f\57\111\107\x4e\x4f\122\105", $Qs);
}
function mo_saml_login_user($SJ, $jw, $bg, $T0, $fn, $wW, $Z0, $SL, $L_ = '', $Ix = '', $pS = null)
{
    do_action("\155\157\x5f\x61\142\x72\x5f\x66\x69\154\164\x65\162\137\154\157\147\x69\156", $pS);
    $T0 = mo_saml_sanitize_username($T0);
    if (get_site_option("\x6d\157\137\x73\141\155\x6c\x5f\144\x69\x73\x61\142\x6c\x65\x5f\162\x6f\154\145\137\155\x61\160\160\x69\x6e\x67")) {
        goto M9;
    }
    check_if_user_allowed_to_login_due_to_role_restriction($fn);
    M9:
    $ia = get_site_option("\x6d\x6f\137\x73\141\155\154\x5f\163\160\x5f\142\141\x73\145\137\x75\162\x6c");
    mo_saml_restrict_users_based_on_domain($SJ);
    if (!empty($wW)) {
        goto aT;
    }
    $wW["\104\105\106\x41\125\x4c\x54"]["\x64\145\146\x61\165\154\164\137\162\157\154\x65"] = "\x73\x75\142\163\143\x72\x69\142\x65\162";
    $wW["\104\105\106\x41\125\114\124"]["\x64\x6f\x6e\164\137\141\154\x6c\x6f\x77\137\165\156\154\x69\x73\x74\145\x64\x5f\x75\163\145\x72"] = '';
    $wW["\x44\x45\x46\101\x55\x4c\124"]["\144\x6f\156\x74\x5f\x63\162\x65\x61\164\145\137\x75\163\x65\162"] = '';
    $wW["\x44\x45\x46\x41\x55\x4c\x54"]["\x6b\145\x65\160\x5f\145\170\151\x73\164\151\x6e\x67\137\x75\163\x65\x72\163\x5f\162\157\x6c\x65"] = '';
    $wW["\104\x45\106\101\x55\x4c\x54"]["\x6d\157\x5f\163\141\155\154\137\144\x6f\x6e\164\x5f\x61\x6c\x6c\157\x77\x5f\165\x73\x65\x72\x5f\x74\157\154\x6f\147\151\x6e\x5f\x63\x72\x65\141\x74\x65\x5f\167\151\x74\x68\137\x67\x69\166\145\156\x5f\x67\162\157\165\160\x73"] = '';
    $wW["\x44\105\106\101\125\x4c\124"]["\x6d\x6f\x5f\x73\141\155\154\x5f\162\145\163\164\x72\151\x63\x74\137\165\163\x65\162\x73\x5f\x77\x69\164\x68\x5f\147\x72\x6f\x75\160\x73"] = '';
    aT:
    global $wpdb;
    $il = get_current_blog_id();
    $Fz = "\165\x6e\x63\x68\145\143\x6b\145\x64";
    if (!empty($ia)) {
        goto tU;
    }
    $ia = get_network_site_url();
    tU:
    if (email_exists($SJ) || username_exists($T0)) {
        goto dR;
    }
    $C4 = Utilities::get_active_sites();
    $og = get_site_option("\155\157\137\141\x70\160\x6c\171\137\x72\x6f\154\x65\137\x6d\x61\160\160\151\156\x67\x5f\x66\157\162\x5f\x73\151\x74\145\x73");
    if (!get_site_option("\155\157\x5f\163\141\155\154\137\144\151\x73\141\x62\x6c\x65\x5f\162\x6f\154\145\137\x6d\x61\160\x70\151\x6e\x67")) {
        goto rN;
    }
    $Nz = wp_generate_password(12, false);
    $vE = wpmu_create_user($T0, $Nz, $SJ);
    goto Cz;
    rN:
    $vE = mo_saml_assign_roles_to_new_user($C4, $og, $wW, $fn, $T0, $SJ);
    Cz:
    switch_to_blog($il);
    if (!empty($vE)) {
        goto S6;
    }
    if (!get_site_option("\x6d\157\x5f\163\141\155\154\137\x64\x69\163\x61\142\x6c\x65\x5f\162\x6f\154\145\137\x6d\x61\160\160\x69\156\147")) {
        goto I8;
    }
    wp_die("\127\x65\40\x63\157\x75\154\144\x20\x6e\x6f\x74\x20\163\x69\x67\x6e\x20\171\157\165\40\151\x6e\56\40\x50\x6c\x65\141\163\145\x20\x63\x6f\x6e\164\x61\x63\164\x20\x61\x64\x6d\x69\x6e\151\x73\x74\x72\141\x74\157\162", "\114\157\x67\x69\156\x20\106\x61\x69\x6c\145\144\x21");
    goto LF;
    I8:
    $n2 = get_site_option("\155\x6f\137\x73\x61\155\154\137\x61\143\x63\157\165\156\164\x5f\143\x72\x65\141\164\x69\157\156\137\x64\151\x73\141\x62\x6c\x65\x64\137\x6d\163\147");
    if (!empty($n2)) {
        goto Yc;
    }
    $n2 = "\127\145\x20\143\157\165\x6c\144\x20\156\x6f\x74\40\163\151\x67\x6e\40\x79\x6f\165\40\x69\x6e\x2e\x20\120\154\x65\141\163\145\40\x63\x6f\x6e\164\x61\143\x74\x20\171\157\x75\162\40\x41\x64\155\x69\x6e\x69\x73\x74\162\x61\x74\x6f\x72\x2e";
    Yc:
    wp_die($n2, "\x45\162\x72\x6f\x72\x3a\x20\116\157\x74\x20\141\x20\x57\157\x72\144\120\x72\x65\163\163\40\115\x65\155\x62\x65\x72");
    LF:
    S6:
    $user = get_user_by("\x69\x64", $vE);
    mo_saml_map_basic_attributes($user, $jw, $bg, $pS);
    mo_saml_map_custom_attributes($vE, $pS);
    $K3 = mo_saml_get_redirect_url($ia, $Z0);
    do_action("\x6d\151\x6e\x69\x6f\x72\x61\156\147\145\137\160\157\163\164\137\141\x75\x74\x68\x65\x6e\164\x69\143\x61\164\145\137\x75\x73\x65\x72\x5f\154\157\147\x69\x6e", $user, null, $K3, true);
    mo_saml_set_auth_cookie($user, $L_, $Ix, true);
    do_action("\155\x6f\137\163\141\155\x6c\137\141\x74\x74\162\151\142\x75\x74\145\x73", $T0, $SJ, $jw, $bg, $fn, null, true);
    goto kb;
    dR:
    if (email_exists($SJ)) {
        goto jc;
    }
    $user = get_user_by("\154\x6f\147\x69\x6e", $T0);
    goto xR;
    jc:
    $user = get_user_by("\x65\155\141\x69\x6c", $SJ);
    xR:
    $vE = $user->ID;
    if (!(!empty($SJ) and strcasecmp($SJ, $user->user_email) != 0)) {
        goto kS;
    }
    $vE = wp_update_user(array("\x49\104" => $vE, "\x75\x73\145\x72\137\x65\155\141\151\x6c" => $SJ));
    kS:
    mo_saml_map_basic_attributes($user, $jw, $bg, $pS);
    mo_saml_map_custom_attributes($vE, $pS);
    $C4 = Utilities::get_active_sites();
    $og = get_site_option("\x6d\x6f\137\141\160\160\x6c\x79\137\x72\x6f\154\145\x5f\x6d\141\x70\x70\x69\156\x67\137\x66\157\162\x5f\163\x69\x74\145\x73");
    if (get_site_option("\x6d\157\x5f\163\x61\x6d\154\137\x64\x69\x73\141\142\x6c\145\x5f\162\x6f\154\145\x5f\x6d\x61\160\x70\151\x6e\x67")) {
        goto gv;
    }
    foreach ($C4 as $blog_id) {
        switch_to_blog($blog_id);
        $user = get_user_by("\x69\144", $vE);
        $NA = '';
        if ($og) {
            goto d4;
        }
        $NA = $blog_id;
        goto Bb;
        d4:
        $NA = 0;
        Bb:
        if (empty($wW)) {
            goto ZV;
        }
        if (!empty($wW[$NA])) {
            goto wV;
        }
        if (!empty($wW["\104\105\x46\101\x55\114\x54"])) {
            goto oU;
        }
        $C6 = "\163\165\x62\x73\x63\x72\x69\142\145\162";
        $fQ = '';
        $Fz = '';
        $zj = '';
        goto aB;
        oU:
        $C6 = isset($wW["\x44\105\x46\x41\x55\x4c\124"]["\144\x65\x66\x61\165\x6c\x74\137\162\x6f\154\145"]) ? $wW["\x44\105\106\x41\125\114\x54"]["\144\145\146\141\165\154\x74\137\x72\x6f\154\x65"] : "\x73\x75\x62\163\x63\x72\x69\x62\x65\x72";
        $fQ = isset($wW["\x44\105\106\101\x55\114\x54"]["\x64\x6f\x6e\164\x5f\141\154\154\157\167\137\x75\x6e\154\x69\163\164\x65\x64\x5f\165\x73\145\162"]) ? $wW["\104\105\x46\101\x55\x4c\124"]["\144\x6f\156\164\137\x61\x6c\154\157\167\137\x75\156\154\x69\x73\164\x65\144\137\x75\x73\145\162"] : '';
        $Fz = isset($wW["\x44\105\106\x41\125\x4c\124"]["\x64\157\x6e\164\x5f\x63\162\x65\141\x74\x65\137\165\163\x65\x72"]) ? $wW["\x44\105\x46\101\x55\x4c\124"]["\144\x6f\156\164\x5f\x63\162\x65\x61\164\x65\x5f\x75\x73\145\162"] : '';
        $zj = isset($wW["\x44\105\x46\101\x55\x4c\x54"]["\153\x65\x65\160\x5f\x65\170\151\x73\x74\151\156\147\x5f\165\x73\x65\162\163\137\x72\157\x6c\145"]) ? $wW["\104\105\x46\101\x55\x4c\124"]["\x6b\145\x65\160\137\145\x78\151\x73\164\151\156\x67\x5f\x75\x73\x65\162\163\x5f\162\x6f\154\x65"] : '';
        aB:
        goto HY;
        wV:
        $C6 = isset($wW[$NA]["\144\145\x66\x61\x75\154\164\137\162\x6f\x6c\145"]) ? $wW[$NA]["\x64\x65\x66\141\x75\x6c\164\137\162\x6f\x6c\145"] : '';
        $fQ = isset($wW[$NA]["\144\157\156\x74\x5f\x61\x6c\x6c\157\167\x5f\165\x6e\x6c\x69\x73\164\145\144\x5f\165\x73\145\162"]) ? $wW[$NA]["\x64\x6f\x6e\x74\137\x61\x6c\154\157\167\137\165\x6e\154\151\163\x74\x65\144\x5f\165\163\x65\x72"] : '';
        $Fz = isset($wW[$NA]["\144\x6f\156\x74\x5f\x63\x72\x65\x61\164\x65\137\x75\163\x65\162"]) ? $wW[$NA]["\x64\157\x6e\164\x5f\143\162\145\x61\x74\x65\x5f\165\x73\145\x72"] : '';
        $zj = isset($wW[$NA]["\153\x65\x65\160\x5f\x65\170\x69\x73\164\x69\x6e\x67\x5f\165\x73\x65\x72\163\137\x72\x6f\x6c\x65"]) ? $wW[$NA]["\153\145\x65\160\137\145\170\x69\x73\x74\x69\x6e\147\137\x75\163\x65\x72\x73\x5f\162\157\x6c\x65"] : '';
        HY:
        ZV:
        if (!is_user_member_of_blog($vE, $blog_id)) {
            goto B8;
        }
        if (isset($zj) && $zj == "\143\150\x65\x63\153\x65\144") {
            goto cA;
        }
        $Le = assign_roles_to_user($user, $wW, $blog_id, $fn, $NA);
        goto G5;
        cA:
        $Le = false;
        G5:
        if (is_administrator_user($user)) {
            goto Wj;
        }
        if (isset($zj) && $zj == "\x63\x68\145\x63\153\x65\x64") {
            goto KZ;
        }
        if ($Le !== true && !empty($fQ) && $fQ == "\x63\x68\145\x63\x6b\145\x64") {
            goto aF;
        }
        if ($Le !== true && !empty($C6) && $C6 !== "\146\x61\154\x73\x65") {
            goto j6;
        }
        if ($Le !== true && is_user_member_of_blog($vE, $blog_id)) {
            goto Ee;
        }
        goto zy;
        KZ:
        goto zy;
        aF:
        $vE = wp_update_user(array("\x49\x44" => $vE, "\x72\157\154\x65" => false));
        goto zy;
        j6:
        $vE = wp_update_user(array("\111\104" => $vE, "\x72\x6f\x6c\145" => $C6));
        goto zy;
        Ee:
        $fl = get_site_option("\144\145\146\x61\x75\154\x74\137\162\157\154\145");
        $vE = wp_update_user(array("\111\104" => $vE, "\x72\157\x6c\x65" => $fl));
        zy:
        Wj:
        goto YZ;
        B8:
        $MO = TRUE;
        $sT = get_site_option("\163\x61\x6d\x6c\137\163\163\x6f\137\163\145\164\x74\x69\156\147\163");
        if (!empty($sT[$blog_id])) {
            goto Cj;
        }
        $sT[$blog_id] = $sT["\x44\105\106\101\125\x4c\124"];
        Cj:
        if (empty($wW)) {
            goto XI;
        }
        if (array_key_exists($NA, $wW)) {
            goto O3;
        }
        if (!array_key_exists("\x44\105\x46\x41\125\114\124", $wW)) {
            goto Ps;
        }
        $fD = get_saml_roles_to_assign($wW, $NA, $fn);
        if (!(empty($fD) && strcmp($wW["\x44\x45\x46\x41\125\x4c\124"]["\x64\x6f\x6e\x74\137\143\x72\145\x61\164\x65\137\x75\163\x65\162"], "\x63\x68\145\143\x6b\145\x64") == 0)) {
            goto TQ;
        }
        $MO = FALSE;
        TQ:
        Ps:
        goto Wp;
        O3:
        $fD = get_saml_roles_to_assign($wW, $NA, $fn);
        if (!(empty($fD) && strcmp($wW[$NA]["\x64\x6f\156\164\137\x63\x72\x65\141\x74\145\x5f\165\163\145\162"], "\x63\150\145\x63\153\145\144") == 0)) {
            goto Gm;
        }
        $MO = FALSE;
        Gm:
        Wp:
        XI:
        if (!$MO) {
            goto VF;
        }
        add_user_to_blog($blog_id, $vE, false);
        $Le = assign_roles_to_user($user, $wW, $blog_id, $fn, $NA);
        if ($Le !== true && !empty($fQ) && $fQ == "\x63\150\145\x63\153\145\x64") {
            goto i3;
        }
        if ($Le !== true && !empty($C6) && $C6 !== "\146\x61\x6c\x73\145") {
            goto mi;
        }
        if ($Le !== true) {
            goto DM;
        }
        goto F9;
        i3:
        $vE = wp_update_user(array("\x49\104" => $vE, "\162\x6f\154\x65" => false));
        goto F9;
        mi:
        $vE = wp_update_user(array("\x49\x44" => $vE, "\162\157\x6c\x65" => $C6));
        goto F9;
        DM:
        $fl = get_site_option("\144\x65\146\141\x75\154\x74\137\x72\157\154\145");
        $vE = wp_update_user(array("\x49\x44" => $vE, "\162\x6f\x6c\x65" => $fl));
        F9:
        VF:
        YZ:
        ki:
    }
    Vf:
    gv:
    switch_to_blog($il);
    if ($vE) {
        goto In;
    }
    wp_die("\111\156\x76\x61\x6c\x69\144\x20\x75\x73\145\162\56\x20\x50\x6c\145\x61\x73\x65\x20\x74\162\171\x20\141\x67\x61\x69\156\x2e");
    In:
    $user = get_user_by("\151\144", $vE);
    mo_saml_set_auth_cookie($user, $L_, $Ix, true);
    do_action("\155\157\137\x73\141\155\x6c\137\141\164\164\x72\x69\142\165\x74\x65\x73", $T0, $SJ, $jw, $bg, $fn);
    kb:
    mo_saml_post_login_redirection($ia, $Z0);
}
function mo_saml_add_user_to_blog($SJ, $T0, $blog_id = 0)
{
    if (email_exists($SJ)) {
        goto Y4Y;
    }
    if (!empty($T0)) {
        goto xG;
    }
    $vE = mo_saml_create_user($SJ, $SJ, $blog_id);
    goto dz;
    xG:
    $vE = mo_saml_create_user($T0, $SJ, $blog_id);
    dz:
    goto MJu;
    Y4Y:
    $user = get_user_by("\x65\155\x61\x69\x6c", $SJ);
    $vE = $user->ID;
    if (empty($blog_id)) {
        goto GO;
    }
    add_user_to_blog($blog_id, $vE, false);
    GO:
    MJu:
    return $vE;
}
function mo_saml_create_user($T0, $SJ, $blog_id)
{
    $wN = wp_generate_password(10, false);
    if (username_exists($T0)) {
        goto nBP;
    }
    $vE = wp_create_user($T0, $wN, $SJ);
    goto SRX;
    nBP:
    $user = get_user_by("\x6c\157\x67\x69\156", $T0);
    $vE = $user->ID;
    if (!$blog_id) {
        goto Pxc;
    }
    add_user_to_blog($blog_id, $vE, false);
    Pxc:
    SRX:
    if (!is_wp_error($vE)) {
        goto btd;
    }
    echo "\74\x73\x74\162\x6f\x6e\147\x3e\105\122\x52\x4f\x52\x3c\57\x73\x74\162\157\156\x67\x3e\72\40\x45\155\160\x74\171\40\x55\163\145\162\40\116\x61\x6d\145\40\x61\x6e\x64\40\105\x6d\x61\x69\154\x2e\x20\x50\154\145\141\x73\145\x20\143\157\x6e\x74\141\x63\164\40\x79\x6f\165\162\40\141\x64\155\x69\156\x69\x73\x74\x72\x61\164\x6f\x72\56";
    exit;
    btd:
    return $vE;
}
function mo_saml_assign_roles_to_new_user($C4, $og, $wW, $fn, $T0, $SJ)
{
    global $wpdb;
    $user = NULL;
    $KQ = false;
    foreach ($C4 as $blog_id) {
        $pF = TRUE;
        $NA = '';
        if ($og) {
            goto bRr;
        }
        $NA = $blog_id;
        goto jmS;
        bRr:
        $NA = 0;
        jmS:
        $sT = get_site_option("\163\x61\155\x6c\x5f\163\x73\157\x5f\163\145\x74\164\x69\156\x67\x73");
        if (!empty($sT[$blog_id])) {
            goto zG8;
        }
        $sT[$blog_id] = $sT["\x44\x45\106\101\x55\x4c\124"];
        zG8:
        if (empty($wW)) {
            goto Tn2;
        }
        if (!empty($wW[$NA])) {
            goto ukq;
        }
        if (!empty($wW["\104\105\x46\101\125\x4c\x54"])) {
            goto pKN;
        }
        $C6 = "\x73\165\142\x73\143\x72\x69\x62\145\162";
        $fQ = '';
        $zj = '';
        $fD = '';
        goto qyS;
        pKN:
        $C6 = isset($wW["\x44\105\x46\101\x55\114\x54"]["\x64\x65\146\x61\x75\x6c\x74\x5f\x72\157\154\x65"]) ? $wW["\104\105\106\x41\125\114\x54"]["\144\145\x66\141\x75\154\164\137\162\157\x6c\145"] : '';
        $fQ = isset($wW["\x44\105\106\x41\125\x4c\x54"]["\144\x6f\156\164\137\x61\x6c\154\157\167\137\x75\156\x6c\x69\163\x74\x65\x64\x5f\x75\163\x65\x72"]) ? $wW["\104\105\106\x41\x55\114\124"]["\144\157\156\x74\x5f\141\x6c\154\157\167\x5f\165\x6e\154\x69\x73\x74\x65\144\x5f\165\x73\x65\162"] : '';
        $zj = array_key_exists("\153\145\145\160\x5f\x65\170\x69\x73\164\x69\x6e\147\137\165\163\145\x72\x73\x5f\162\x6f\x6c\x65", $wW["\104\x45\x46\101\125\x4c\x54"]) ? $wW["\104\105\x46\101\x55\114\x54"]["\153\145\145\x70\137\145\x78\151\x73\164\151\156\x67\x5f\x75\x73\145\x72\163\137\162\x6f\154\x65"] : '';
        $fD = get_saml_roles_to_assign($wW, $NA, $fn);
        if (!(empty($fD) && strcmp($wW["\x44\x45\x46\101\125\114\124"]["\144\x6f\x6e\x74\137\x63\162\145\x61\x74\x65\137\x75\163\145\162"], "\143\150\145\x63\153\x65\144") == 0)) {
            goto FcA;
        }
        $pF = FALSE;
        FcA:
        qyS:
        goto Hs8;
        ukq:
        $C6 = isset($wW[$NA]["\144\x65\x66\x61\x75\154\x74\137\x72\157\x6c\x65"]) ? $wW[$NA]["\x64\x65\146\141\165\154\x74\137\162\157\154\x65"] : '';
        $fQ = isset($wW[$NA]["\144\x6f\156\x74\x5f\141\x6c\154\x6f\167\137\x75\156\154\x69\163\x74\145\144\137\x75\x73\x65\x72"]) ? $wW[$NA]["\144\x6f\x6e\164\137\x61\154\x6c\157\x77\x5f\165\x6e\154\151\163\164\x65\144\x5f\165\x73\145\x72"] : '';
        $zj = array_key_exists("\153\x65\x65\x70\x5f\145\170\x69\x73\x74\151\x6e\147\x5f\x75\x73\x65\x72\x73\137\x72\x6f\154\145", $wW[$NA]) ? $wW[$NA]["\x6b\145\x65\x70\x5f\x65\170\151\163\x74\x69\156\147\137\x75\x73\145\x72\x73\137\162\157\154\145"] : '';
        $fD = get_saml_roles_to_assign($wW, $NA, $fn);
        if (!(empty($fD) && strcmp($wW[$NA]["\144\x6f\156\x74\137\x63\162\x65\x61\x74\145\137\165\163\x65\x72"], "\143\x68\x65\x63\x6b\145\x64") == 0)) {
            goto Yi_;
        }
        $pF = FALSE;
        Yi_:
        Hs8:
        Tn2:
        if (!$pF) {
            goto vVc;
        }
        $vE = NULL;
        switch_to_blog($blog_id);
        $vE = mo_saml_add_user_to_blog($SJ, $T0, $blog_id);
        $user = get_user_by("\151\x64", $vE);
        $Le = assign_roles_to_user($user, $wW, $blog_id, $fn, $NA);
        if ($Le !== true && !empty($fQ) && $fQ == "\143\x68\x65\143\153\x65\x64") {
            goto W78;
        }
        if ($Le !== true && !empty($C6) && $C6 !== "\146\141\x6c\x73\x65") {
            goto bnf;
        }
        if ($Le !== true) {
            goto NVU;
        }
        goto nOH;
        W78:
        $vE = wp_update_user(array("\x49\x44" => $vE, "\x72\x6f\154\x65" => false));
        goto nOH;
        bnf:
        $vE = wp_update_user(array("\111\104" => $vE, "\162\x6f\154\145" => $C6));
        goto nOH;
        NVU:
        $fl = get_site_option("\x64\145\x66\x61\165\x6c\164\137\162\157\154\x65");
        $vE = wp_update_user(array("\111\104" => $vE, "\x72\157\154\145" => $fl));
        nOH:
        $W0 = $user->{$wpdb->prefix . "\143\141\160\141\142\x69\x6c\151\164\151\x65\163"};
        if (isset($wp_roles)) {
            goto W9G;
        }
        $wp_roles = new WP_Roles($NA);
        W9G:
        vVc:
        n2E:
    }
    Rvn:
    if (!empty($user)) {
        goto v6Q;
    }
    return;
    goto mAf;
    v6Q:
    return $user->ID;
    mAf:
}
function mo_saml_sanitize_username($T0)
{
    $d_ = sanitize_user($T0, true);
    $kc = apply_filters("\160\162\145\137\165\163\x65\162\x5f\154\x6f\x67\x69\156", $d_);
    $T0 = trim($kc);
    return $T0;
}
function mo_saml_map_basic_attributes($user, $jw, $bg, $pS)
{
    $vE = $user->ID;
    if (empty($jw)) {
        goto Bo3;
    }
    $vE = wp_update_user(array("\111\104" => $vE, "\146\151\x72\x73\x74\x5f\156\x61\155\x65" => $jw));
    Bo3:
    if (empty($bg)) {
        goto CyB;
    }
    $vE = wp_update_user(array("\111\x44" => $vE, "\154\x61\163\x74\137\x6e\x61\155\x65" => $bg));
    CyB:
    if (is_null($pS)) {
        goto qva;
    }
    update_user_meta($vE, "\x6d\x6f\x5f\x73\x61\x6d\154\x5f\165\163\x65\x72\x5f\141\164\164\162\x69\x62\x75\x74\x65\x73", $pS);
    $wu = get_site_option("\x73\x61\x6d\154\137\x61\155\x5f\x64\x69\163\160\154\x61\x79\137\156\x61\155\x65");
    if (empty($wu)) {
        goto tC8;
    }
    if (strcmp($wu, "\x55\123\105\x52\x4e\101\115\x45") == 0) {
        goto k5C;
    }
    if (strcmp($wu, "\106\x4e\101\x4d\x45") == 0 && !empty($jw)) {
        goto jbr;
    }
    if (strcmp($wu, "\x4c\x4e\x41\x4d\105") == 0 && !empty($bg)) {
        goto ZlR;
    }
    if (strcmp($wu, "\x46\x4e\x41\115\105\x5f\114\x4e\x41\x4d\x45") == 0 && !empty($bg) && !empty($jw)) {
        goto IGb;
    }
    if (!(strcmp($wu, "\114\116\101\115\105\137\x46\116\101\x4d\x45") == 0 && !empty($bg) && !empty($jw))) {
        goto X4u;
    }
    $vE = wp_update_user(array("\x49\104" => $vE, "\x64\x69\163\160\154\141\171\x5f\156\x61\x6d\145" => $bg . "\x20" . $jw));
    X4u:
    goto oeH;
    IGb:
    $vE = wp_update_user(array("\111\x44" => $vE, "\x64\151\163\160\x6c\141\171\x5f\x6e\x61\x6d\145" => $jw . "\40" . $bg));
    oeH:
    goto kvz;
    ZlR:
    $vE = wp_update_user(array("\x49\x44" => $vE, "\x64\x69\x73\x70\154\141\x79\x5f\x6e\141\155\x65" => $bg));
    kvz:
    goto JNf;
    jbr:
    $vE = wp_update_user(array("\111\104" => $vE, "\x64\x69\x73\160\154\x61\171\137\156\x61\x6d\x65" => $jw));
    JNf:
    goto INE;
    k5C:
    $vE = wp_update_user(array("\x49\x44" => $vE, "\x64\151\x73\160\x6c\x61\x79\137\156\x61\155\145" => $user->user_login));
    INE:
    tC8:
    qva:
}
function mo_saml_map_custom_attributes($vE, $pS)
{
    if (!get_site_option("\155\157\x5f\163\141\155\x6c\x5f\x63\x75\x73\164\x6f\155\137\x61\164\164\x72\x73\137\155\x61\x70\x70\151\156\x67")) {
        goto OZT;
    }
    $Xn = maybe_unserialize(get_site_option("\155\157\137\163\141\155\x6c\x5f\143\x75\x73\164\x6f\x6d\137\x61\x74\164\x72\x73\137\x6d\x61\160\x70\x69\x6e\x67"));
    foreach ($Xn as $XC => $wE) {
        if (!array_key_exists($wE, $pS)) {
            goto Jpq;
        }
        $Zh = false;
        if (!(count($pS[$wE]) == 1)) {
            goto Dkz;
        }
        $Zh = true;
        Dkz:
        if (!$Zh) {
            goto ytw;
        }
        update_user_meta($vE, $XC, $pS[$wE][0]);
        goto DGa;
        ytw:
        $eg = array();
        foreach ($pS[$wE] as $Bq) {
            array_push($eg, $Bq);
            rwx:
        }
        hj0:
        update_user_meta($vE, $XC, $eg);
        DGa:
        Jpq:
        S5w:
    }
    KW4:
    OZT:
}
function mo_saml_restrict_users_based_on_domain($SJ)
{
    $iD = get_site_option("\x6d\157\x5f\163\141\x6d\154\x5f\145\x6e\x61\x62\x6c\x65\x5f\144\157\155\x61\x69\x6e\137\x72\x65\163\164\x72\151\x63\x74\x69\x6f\156\x5f\x6c\x6f\147\x69\x6e");
    if (!$iD) {
        goto fS_;
    }
    $BX = get_site_option("\x73\x61\155\x6c\x5f\x61\x6d\x5f\x65\x6d\141\x69\154\x5f\144\x6f\x6d\x61\151\x6e\x73");
    $Aw = explode("\73", $BX);
    $x9 = explode("\x40", $SJ);
    $Ra = array_key_exists("\x31", $x9) ? $x9[1] : '';
    $mG = get_site_option("\155\157\137\163\x61\155\x6c\x5f\x61\154\x6c\x6f\x77\x5f\x64\145\x6e\x79\x5f\x75\x73\x65\x72\137\167\151\164\150\137\x64\x6f\155\141\151\x6e");
    $n2 = get_site_option("\x6d\x6f\137\x73\x61\x6d\x6c\137\162\x65\163\164\x72\x69\x63\x74\145\x64\137\144\x6f\155\x61\x69\x6e\137\x65\x72\x72\x6f\x72\137\155\x73\x67");
    if (!empty($n2)) {
        goto bqh;
    }
    $n2 = "\x59\x6f\165\x20\141\x72\x65\40\x6e\x6f\x74\x20\141\x6c\154\x6f\167\145\144\x20\x74\x6f\40\x6c\x6f\x67\151\x6e\x2e\x20\x50\154\x65\141\163\145\x20\x63\157\x6e\x74\141\x63\164\40\x79\x6f\165\x72\x20\x41\x64\155\151\156\151\163\x74\x72\141\164\157\162\56";
    bqh:
    if (!empty($mG) && $mG == "\x64\145\x6e\171") {
        goto SCK;
    }
    if (in_array($Ra, $Aw)) {
        goto S6o;
    }
    wp_die($n2, "\120\145\x72\155\x69\163\x73\151\157\x6e\x20\104\145\156\151\x65\x64\40\105\x72\x72\157\162\x20\x2d\40\x32");
    S6o:
    goto fbQ;
    SCK:
    if (!in_array($Ra, $Aw)) {
        goto p5m;
    }
    wp_die($n2, "\x50\x65\162\x6d\151\163\163\x69\157\x6e\40\104\x65\x6e\x69\145\x64\x20\105\162\x72\x6f\x72\40\55\40\x31");
    p5m:
    fbQ:
    fS_:
}
function mo_saml_set_auth_cookie($user, $L_, $Ix, $uj)
{
    $vE = $user->ID;
    do_action("\x77\160\x5f\x6c\157\147\151\156", $user->user_login, $user);
    if (empty($L_)) {
        goto RUd;
    }
    update_user_meta($vE, "\155\157\137\163\141\x6d\154\137\x73\x65\x73\x73\151\x6f\156\x5f\x69\156\x64\145\170", $L_);
    RUd:
    if (empty($Ix)) {
        goto qK4;
    }
    update_user_meta($vE, "\155\x6f\x5f\x73\141\155\x6c\137\x6e\141\155\145\x5f\x69\144", $Ix);
    qK4:
    if (!(!session_id() || session_id() == '' || !isset($_SESSION))) {
        goto BUU;
    }
    session_start();
    BUU:
    $_SESSION["\155\157\x5f\163\141\155\154"]["\x6c\x6f\147\147\145\144\x5f\x69\x6e\137\167\x69\x74\150\137\x69\144\160"] = TRUE;
    update_user_meta($vE, "\x6d\157\x5f\163\141\155\154\x5f\151\x64\160\137\154\157\x67\x69\x6e", "\x74\162\165\145");
    wp_set_current_user($vE);
    $EE = false;
    $EE = apply_filters("\x6d\157\x5f\162\145\155\x65\x6d\x62\x65\162\137\x6d\x65", $EE);
    wp_set_auth_cookie($vE, $EE);
    if (!$uj) {
        goto bR0;
    }
    do_action("\165\x73\145\x72\x5f\x72\145\x67\x69\163\164\145\162", $vE);
    bR0:
}
function mo_saml_post_login_redirection($ia, $Z0)
{
    $p5 = mo_saml_get_redirect_url($ia, $Z0);
    wp_redirect($p5);
    exit;
}
function mo_saml_get_redirect_url($ia, $Z0)
{
    $K3 = '';
    $sT = get_site_option("\x73\x61\x6d\x6c\x5f\x73\163\157\137\x73\145\x74\x74\x69\x6e\x67\163");
    $LQ = get_current_blog_id();
    if (!(empty($sT[$LQ]) && !empty($sT["\x44\105\x46\101\x55\114\124"]))) {
        goto YAR;
    }
    $sT[$LQ] = $sT["\104\x45\x46\101\125\x4c\x54"];
    YAR:
    $HW = isset($sT[$LQ]["\155\x6f\x5f\163\x61\x6d\x6c\137\162\x65\x6c\141\171\137\x73\164\x61\164\145"]) ? $sT[$LQ]["\x6d\x6f\137\x73\141\155\x6c\x5f\x72\x65\154\141\x79\137\x73\x74\x61\164\x65"] : '';
    if (!empty($HW)) {
        goto g6_;
    }
    if (!empty($Z0)) {
        goto ZU9;
    }
    $K3 = $ia;
    goto ui6;
    ZU9:
    $K3 = $Z0;
    ui6:
    goto lLz;
    g6_:
    $K3 = $HW;
    lLz:
    return $K3;
}
function check_if_user_allowed_to_login($user, $ia)
{
    $vE = $user->ID;
    global $wpdb;
    if (get_user_meta($vE, "\155\x6f\137\163\141\x6d\154\137\x75\x73\x65\x72\137\x74\x79\160\145", true)) {
        goto Cn1;
    }
    if (get_site_option("\x6d\x6f\137\163\141\155\154\137\165\x73\162\x5f\154\x6d\x74")) {
        goto cFy;
    }
    update_user_meta($vE, "\x6d\x6f\x5f\163\x61\155\x6c\x5f\165\163\145\162\x5f\164\x79\160\x65", "\163\163\157\137\165\x73\x65\x72");
    goto Wch;
    cFy:
    $XC = get_site_option("\155\x6f\x5f\163\x61\155\154\137\x63\x75\x73\164\157\155\x65\x72\137\x74\x6f\153\x65\x6e");
    $ri = AESEncryption::decrypt_data(get_site_option("\x6d\x6f\x5f\x73\x61\155\154\137\165\163\162\137\154\x6d\164"), $XC);
    $wO = "\123\105\114\105\x43\x54\40\x43\x4f\125\116\124\50\52\x29\x20\x46\122\117\115\40" . $wpdb->prefix . "\165\x73\145\162\x6d\x65\x74\141\x20\x57\110\x45\x52\105\40\x6d\145\164\x61\x5f\153\145\x79\x3d\x27\155\157\137\163\x61\x6d\154\137\x75\163\x65\x72\x5f\x74\171\x70\x65\x27";
    $EW = $wpdb->get_var($wO);
    if ($EW >= $ri) {
        goto BVT;
    }
    update_user_meta($vE, "\x6d\157\137\x73\x61\155\154\x5f\x75\163\145\x72\x5f\x74\x79\160\x65", "\x73\163\157\x5f\165\x73\145\162");
    goto gKX;
    BVT:
    if (get_site_option("\x75\163\x65\162\137\141\x6c\x65\x72\x74\137\x65\155\x61\x69\x6c\137\163\x65\156\164")) {
        goto Hvy;
    }
    $ZX = new Customersaml();
    $ZX->mo_saml_send_user_exceeded_alert_email($ri, $this);
    Hvy:
    if (is_administrator_user($user)) {
        goto en2;
    }
    wp_redirect($ia);
    exit;
    goto u6X;
    en2:
    update_user_meta($vE, "\155\x6f\x5f\x73\x61\155\x6c\137\165\x73\x65\x72\x5f\164\x79\x70\x65", "\x73\163\157\x5f\165\163\145\162");
    u6X:
    gKX:
    Wch:
    Cn1:
}
function check_if_user_allowed_to_login_due_to_role_restriction($fn)
{
    $wW = maybe_unserialize(get_site_option("\x73\141\x6d\x6c\x5f\x61\155\137\162\157\154\145\137\155\141\x70\x70\151\156\x67"));
    $C4 = Utilities::get_active_sites();
    $og = get_site_option("\x6d\x6f\137\x61\160\x70\x6c\171\x5f\162\x6f\154\145\x5f\x6d\141\x70\x70\151\x6e\x67\x5f\x66\157\162\137\163\151\x74\145\x73");
    if ($wW) {
        goto yCV;
    }
    $wW = array();
    yCV:
    if (array_key_exists("\x44\x45\106\101\x55\x4c\124", $wW)) {
        goto dTh;
    }
    $wW["\104\x45\x46\101\x55\114\124"] = array();
    dTh:
    foreach ($C4 as $blog_id) {
        if ($og) {
            goto thx;
        }
        $NA = $blog_id;
        goto Pex;
        thx:
        $NA = 0;
        Pex:
        if (isset($wW[$NA])) {
            goto Kga;
        }
        $j5 = $wW["\x44\105\x46\101\x55\114\x54"];
        goto XdF;
        Kga:
        $j5 = $wW[$NA];
        XdF:
        if (empty($j5)) {
            goto rjG;
        }
        $io = isset($j5["\x6d\x6f\137\163\x61\155\x6c\137\x64\x6f\156\x74\x5f\x61\x6c\x6c\x6f\x77\137\x75\x73\x65\x72\x5f\164\x6f\x6c\157\147\x69\156\x5f\x63\162\145\x61\164\145\x5f\x77\x69\164\x68\x5f\x67\151\x76\145\156\x5f\x67\x72\x6f\x75\160\163"]) ? $j5["\x6d\x6f\137\163\141\155\154\137\144\x6f\x6e\x74\137\x61\x6c\154\x6f\167\x5f\x75\x73\145\x72\x5f\164\157\x6c\x6f\x67\x69\x6e\x5f\x63\162\145\141\164\145\x5f\x77\x69\164\150\137\x67\151\166\145\156\x5f\x67\162\x6f\x75\x70\163"] : '';
        if (!($io == "\143\150\145\143\x6b\x65\144")) {
            goto KaP;
        }
        if (empty($fn)) {
            goto iYV;
        }
        $BJ = $j5["\x6d\x6f\x5f\x73\141\x6d\x6c\x5f\162\x65\163\x74\162\x69\143\x74\137\165\163\x65\162\163\137\167\151\164\150\137\147\x72\x6f\x75\x70\163"];
        $FU = explode("\73", $BJ);
        foreach ($FU as $rr) {
            foreach ($fn as $hX) {
                $hX = trim($hX);
                if (!(!empty($hX) && $hX == $rr)) {
                    goto Iwz;
                }
                wp_die("\x59\157\165\40\x61\162\x65\x20\156\157\164\40\x61\165\x74\150\157\x72\151\x7a\x65\144\40\x74\157\40\x6c\157\x67\151\156\x2e\x20\120\154\x65\x61\x73\145\x20\x63\157\x6e\164\141\143\164\x20\x79\x6f\165\x72\40\141\x64\x6d\x69\x6e\x69\x73\164\162\x61\x74\x6f\162\56", "\x45\x72\x72\x6f\162");
                Iwz:
                GlM:
            }
            FkM:
            fN3:
        }
        Sbb:
        iYV:
        KaP:
        rjG:
        xgl:
    }
    zL7:
}
function assign_roles_to_user($user, $wW, $blog_id, $fn, $NA)
{
    $Le = false;
    if (!(!empty($fn) && !empty($wW) && !is_administrator_user($user) && is_user_member_of_blog($user->ID, $blog_id))) {
        goto CCB;
    }
    if (!empty($wW[$NA])) {
        goto Z1P;
    }
    if (empty($wW["\104\105\x46\101\125\x4c\x54"])) {
        goto QgH;
    }
    $j5 = $wW["\x44\105\x46\101\x55\x4c\124"];
    QgH:
    goto KcY;
    Z1P:
    $j5 = $wW[$NA];
    KcY:
    if (empty($j5)) {
        goto I0t;
    }
    $user->set_role(false);
    $eO = '';
    $vH = false;
    unset($j5["\x64\145\146\x61\165\154\x74\137\x72\x6f\154\145"]);
    unset($j5["\144\x6f\x6e\164\x5f\x63\162\x65\141\x74\145\137\165\163\x65\x72"]);
    unset($j5["\x64\157\156\x74\137\141\154\x6c\157\167\x5f\165\x6e\x6c\151\163\x74\x65\x64\x5f\x75\163\145\162"]);
    unset($j5["\153\145\145\x70\x5f\145\170\151\163\x74\x69\x6e\147\x5f\x75\x73\x65\x72\x73\x5f\x72\x6f\x6c\145"]);
    unset($j5["\x6d\x6f\x5f\163\x61\155\x6c\137\x64\x6f\x6e\x74\137\141\x6c\x6c\157\167\137\x75\163\145\x72\x5f\164\x6f\x6c\x6f\147\151\156\x5f\x63\x72\x65\141\164\x65\137\167\151\164\x68\x5f\147\151\x76\145\156\x5f\x67\x72\157\165\x70\x73"]);
    unset($j5["\155\157\137\163\141\155\x6c\x5f\162\x65\163\x74\x72\x69\x63\x74\x5f\x75\x73\145\x72\x73\137\x77\151\x74\x68\x5f\147\162\x6f\165\x70\x73"]);
    foreach ($j5 as $bC => $rH) {
        $FU = explode("\73", $rH);
        foreach ($FU as $rr) {
            if (!(!empty($rr) && in_array($rr, $fn))) {
                goto CwX;
            }
            $Le = true;
            $user->add_role($bC);
            CwX:
            N0_:
        }
        zEQ:
        xGR:
    }
    z13:
    I0t:
    CCB:
    $xK = get_site_option("\x6d\157\137\x73\x61\x6d\x6c\137\163\165\160\x65\162\x5f\x61\144\x6d\x69\x6e\x5f\x72\x6f\x6c\145\x5f\x6d\141\160\x70\151\x6e\147");
    $gK = array();
    if (empty($xK)) {
        goto jb4;
    }
    $gK = explode("\73", $xK);
    jb4:
    if (!(!empty($fn) && !empty($gK))) {
        goto nX5;
    }
    foreach ($gK as $rr) {
        if (!in_array($rr, $fn)) {
            goto KRZ;
        }
        grant_super_admin($user->ID);
        KRZ:
        dzi:
    }
    NbG:
    nX5:
    return $Le;
}
function get_saml_roles_to_assign($wW, $blog_id, $fn)
{
    $fD = array();
    if (!(!empty($fn) && !empty($wW))) {
        goto SpV;
    }
    if (!empty($wW[$blog_id])) {
        goto LfP;
    }
    if (empty($wW["\104\x45\x46\x41\x55\x4c\x54"])) {
        goto awQ;
    }
    $j5 = $wW["\x44\x45\106\x41\x55\x4c\124"];
    awQ:
    goto pjc;
    LfP:
    $j5 = $wW[$blog_id];
    pjc:
    if (empty($j5)) {
        goto to8;
    }
    unset($j5["\144\145\x66\x61\x75\154\164\x5f\x72\157\x6c\x65"]);
    unset($j5["\144\x6f\x6e\x74\137\143\x72\145\x61\x74\145\x5f\165\163\145\x72"]);
    unset($j5["\x64\x6f\x6e\x74\x5f\x61\x6c\154\x6f\x77\137\x75\156\154\x69\163\x74\x65\x64\x5f\165\163\145\x72"]);
    unset($j5["\153\145\145\160\137\145\x78\151\x73\x74\151\156\x67\x5f\165\x73\x65\162\x73\x5f\x72\157\154\x65"]);
    unset($j5["\155\x6f\x5f\x73\141\x6d\x6c\x5f\144\157\156\164\137\x61\154\154\x6f\167\x5f\x75\x73\x65\162\137\x74\x6f\154\157\x67\151\x6e\137\143\x72\x65\141\x74\x65\x5f\167\x69\x74\x68\137\147\x69\x76\x65\x6e\137\x67\162\x6f\x75\160\x73"]);
    unset($j5["\x6d\157\x5f\x73\x61\x6d\154\137\162\x65\163\x74\x72\x69\x63\x74\x5f\165\163\145\162\163\137\x77\x69\x74\x68\x5f\147\x72\x6f\x75\160\163"]);
    foreach ($j5 as $bC => $rH) {
        $FU = explode("\73", $rH);
        foreach ($FU as $rr) {
            if (!(!empty($rr) and in_array($rr, $fn))) {
                goto SiG;
            }
            array_push($fD, $bC);
            SiG:
            Xbg:
        }
        hiQ:
        lAn:
    }
    r1M:
    to8:
    SpV:
    return $fD;
}
function is_administrator_user($user)
{
    $Vn = $user->roles;
    if (!is_null($Vn) && in_array("\141\144\x6d\151\x6e\x69\163\x74\162\x61\x74\x6f\162", $Vn)) {
        goto AOo;
    }
    return false;
    goto G5V;
    AOo:
    return true;
    G5V:
}
function mo_saml_is_customer_registered()
{
    $QC = get_site_option("\155\157\x5f\x73\141\155\154\x5f\141\x64\x6d\x69\x6e\137\145\x6d\141\x69\154");
    $aS = get_site_option("\155\157\137\x73\x61\x6d\154\137\x61\144\155\151\156\x5f\x63\x75\163\164\x6f\155\x65\162\137\153\145\171");
    if (!$QC || !$aS || !is_numeric(trim($aS))) {
        goto kl4;
    }
    return 1;
    goto Wi4;
    kl4:
    return 0;
    Wi4:
}
function mo_saml_is_customer_license_verified()
{
    $XC = get_site_option("\x6d\157\x5f\x73\141\155\154\137\143\x75\163\164\157\x6d\145\162\137\x74\x6f\x6b\145\156");
    $HF = AESEncryption::decrypt_data(get_site_option("\x74\137\x73\x69\164\x65\x5f\163\x74\141\164\165\163"), $XC);
    $ZK = get_site_option("\x73\155\154\137\x6c\x6b");
    $QC = get_site_option("\x6d\157\137\x73\x61\x6d\154\x5f\x61\x64\x6d\151\x6e\x5f\145\x6d\141\x69\154");
    $aS = get_site_option("\x6d\x6f\137\163\x61\x6d\x6c\137\x61\144\x6d\151\156\x5f\143\165\x73\x74\x6f\x6d\x65\162\x5f\153\145\x79");
    $Zy = AESEncryption::decrypt_data(get_site_option("\156\157\x5f\x73\x62\x73"), $XC);
    $ly = false;
    if (!get_site_option("\156\157\x5f\163\142\x73")) {
        goto KkX;
    }
    $lf = Utilities::get_sites();
    $ly = $Zy < count($lf);
    KkX:
    if ($HF != "\164\162\165\x65" && !$ZK || !$QC || !$aS || !is_numeric(trim($aS)) || $ly) {
        goto xCw;
    }
    return 1;
    goto BOR;
    xCw:
    return 0;
    BOR:
}
function show_status_error($GI, $Z0)
{
    if ($Z0 == "\164\145\163\164\x56\x61\x6c\151\144\x61\x74\x65" or $Z0 == "\164\x65\163\164\x4e\x65\167\x43\145\162\x74\151\x66\151\x63\141\164\x65") {
        goto Nr5;
    }
    wp_die("\127\x65\40\x63\x6f\165\x6c\144\x20\x6e\157\164\x20\x73\151\x67\x6e\x20\171\x6f\165\40\151\156\56\x20\120\x6c\145\141\x73\x65\40\143\x6f\x6e\x74\141\143\164\x20\171\x6f\165\162\x20\x41\x64\x6d\151\x6e\151\x73\164\x72\141\x74\157\162\56", "\x45\162\162\157\x72\x3a\x20\x49\x6e\x76\x61\x6c\151\144\x20\x53\x41\115\x4c\x20\122\x65\163\x70\157\x6e\x73\x65\40\123\x74\x61\x74\x75\x73");
    goto K9a;
    Nr5:
    echo "\x3c\144\x69\x76\40\163\x74\171\x6c\x65\75\42\146\x6f\156\x74\55\146\x61\x6d\151\154\171\72\x43\141\154\x69\142\162\151\x3b\x70\x61\x64\x64\151\x6e\x67\x3a\60\x20\x33\45\x3b\x22\76";
    echo "\x3c\x64\x69\x76\x20\163\164\171\154\x65\75\x22\x63\157\154\157\162\x3a\40\43\141\x39\64\x34\x34\62\x3b\x62\141\143\153\147\162\x6f\165\156\144\55\x63\x6f\x6c\x6f\162\72\x20\x23\146\x32\x64\145\144\145\73\x70\x61\144\144\151\156\x67\x3a\40\61\65\x70\170\x3b\155\x61\x72\147\151\x6e\55\x62\157\164\x74\157\155\x3a\x20\62\x30\x70\170\73\164\145\x78\x74\x2d\x61\x6c\x69\x67\156\x3a\143\x65\156\164\145\162\x3b\x62\x6f\162\x64\145\x72\x3a\x31\x70\170\x20\163\157\x6c\151\144\40\x23\105\x36\102\x33\x42\x32\x3b\x66\x6f\x6e\x74\x2d\x73\x69\172\145\x3a\x31\x38\x70\x74\x3b\x22\x3e\40\x45\122\122\117\122\74\x2f\144\151\x76\x3e\15\12\x20\x20\x20\40\x20\40\x20\40\74\x64\x69\x76\40\163\164\x79\x6c\x65\75\x22\x63\157\154\x6f\x72\x3a\40\43\141\71\64\x34\x34\x32\73\x66\157\156\164\x2d\163\x69\x7a\145\72\x31\x34\x70\x74\x3b\x20\155\x61\162\x67\x69\156\55\x62\157\164\164\157\x6d\72\x32\x30\160\170\x3b\42\x3e\x3c\x70\x3e\74\163\164\x72\157\156\x67\76\105\x72\x72\157\x72\72\40\74\57\163\164\162\157\156\x67\x3e\40\x49\x6e\166\x61\x6c\x69\x64\x20\x53\101\x4d\114\40\122\x65\x73\x70\157\156\x73\x65\x20\x53\x74\x61\x74\165\x73\x2e\x3c\57\x70\x3e\xd\12\40\x20\x20\40\x20\40\40\x20\40\40\40\x20\x3c\x70\76\74\163\164\162\157\x6e\x67\x3e\x43\141\165\163\145\x73\74\x2f\x73\x74\x72\x6f\x6e\147\x3e\72\40\x49\144\x65\x6e\x74\x69\164\x79\40\120\162\157\x76\x69\144\x65\x72\x20\150\141\163\40\163\145\156\x74\40\47" . $GI . "\47\x20\x73\164\x61\x74\165\x73\40\x63\x6f\144\145\x20\x69\156\x20\x53\101\115\x4c\40\122\x65\163\x70\x6f\x6e\163\145\56\x20\74\x2f\x70\x3e\15\12\40\40\x20\x20\x20\40\40\40\x20\40\40\40\x3c\160\x3e\x3c\x73\164\162\157\x6e\147\x3e\x52\145\x61\x73\157\156\74\57\163\164\x72\x6f\x6e\147\76\x3a\x20" . get_status_message($GI) . "\x3c\x2f\x70\x3e\x3c\142\x72\x3e";
    if (empty($Do)) {
        goto fMn;
    }
    echo "\x3c\160\x3e\x3c\x73\x74\162\x6f\156\147\x3e\x53\164\x61\x74\x75\163\x20\115\x65\x73\163\x61\147\x65\x20\x69\x6e\x20\164\150\145\40\123\101\115\x4c\x20\x52\x65\163\x70\157\x6e\x73\145\x3a\x3c\57\163\164\x72\157\156\147\76\40\74\142\162\x2f\76" . $Do . "\x3c\57\160\76\x3c\142\162\76";
    fMn:
    echo "\15\12\40\40\x20\40\x20\x20\40\x20\74\57\x64\x69\x76\76\15\12\xd\xa\40\40\x20\x20\x20\x20\x20\x20\74\x64\151\166\x20\163\164\171\154\145\x3d\42\x6d\x61\x72\x67\151\156\72\63\x25\x3b\144\x69\163\x70\x6c\x61\x79\72\x62\154\x6f\x63\x6b\x3b\164\x65\170\164\55\x61\154\x69\147\156\72\143\x65\156\x74\145\x72\73\x22\x3e\xd\12\x20\40\x20\40\x20\x20\40\x20\40\x20\40\x20\x3c\x64\x69\166\x20\x73\164\171\x6c\x65\75\42\x6d\x61\x72\x67\x69\x6e\72\x33\45\x3b\144\151\x73\x70\154\x61\x79\72\142\x6c\x6f\143\153\x3b\x74\x65\x78\x74\55\x61\x6c\151\147\x6e\72\143\145\156\x74\x65\162\73\x22\x3e\74\x69\x6e\160\165\x74\x20\163\x74\171\154\145\75\x22\x70\141\x64\144\x69\x6e\147\72\61\x25\73\167\151\144\164\150\72\x31\60\60\160\170\73\x62\141\x63\x6b\x67\162\x6f\x75\156\144\x3a\40\43\x30\60\71\61\103\x44\x20\x6e\157\156\x65\40\162\145\x70\145\x61\x74\40\163\143\x72\157\x6c\x6c\x20\60\x25\x20\60\x25\73\143\165\162\x73\157\x72\72\x20\x70\x6f\151\156\x74\x65\162\73\x66\x6f\x6e\164\55\x73\x69\x7a\x65\72\x31\65\x70\x78\73\x62\x6f\x72\144\145\x72\x2d\x77\x69\x64\x74\x68\x3a\x20\61\160\170\73\142\157\162\144\x65\x72\55\x73\164\x79\154\x65\72\x20\x73\x6f\x6c\x69\144\73\x62\157\162\x64\145\162\55\x72\x61\x64\151\165\163\x3a\40\63\x70\x78\73\x77\150\151\x74\x65\x2d\163\160\141\x63\x65\72\x20\x6e\157\167\x72\141\160\x3b\x62\x6f\170\x2d\163\x69\x7a\x69\156\x67\72\40\x62\x6f\x72\144\x65\162\x2d\x62\x6f\170\x3b\x62\x6f\162\x64\145\x72\x2d\x63\157\154\x6f\162\x3a\x20\x23\x30\x30\67\63\x41\x41\73\142\157\x78\55\163\150\141\x64\x6f\x77\72\40\60\160\x78\40\x31\160\170\x20\60\160\170\40\162\147\142\x61\50\61\x32\60\54\x20\x32\60\60\54\x20\x32\63\x30\x2c\40\x30\56\66\x29\x20\x69\156\163\x65\x74\73\x63\157\x6c\x6f\x72\72\40\43\106\x46\x46\x3b\x22\x74\x79\160\145\x3d\x22\142\165\164\x74\x6f\156\42\x20\166\141\x6c\165\x65\75\x22\104\157\156\145\x22\x20\x6f\x6e\103\154\151\143\153\75\x22\163\x65\154\146\x2e\x63\154\x6f\x73\145\50\51\x3b\x22\x3e\74\x2f\x64\151\x76\x3e";
    exit;
    K9a:
}
function addLink($cc, $Ow)
{
    $DV = "\x3c\141\x20\150\x72\x65\x66\75\42" . $Ow . "\x22\76" . $cc . "\x3c\x2f\141\x3e";
    return $DV;
}
function get_status_message($GI)
{
    switch ($GI) {
        case "\x52\x65\161\x75\x65\163\x74\x65\162":
            return "\x54\x68\145\x20\162\x65\x71\165\x65\x73\164\x20\x63\157\x75\x6c\x64\40\156\157\x74\x20\142\145\40\x70\145\x72\146\x6f\162\x6d\145\x64\40\144\x75\x65\x20\x74\157\40\x61\156\x20\145\x72\x72\x6f\x72\x20\x6f\x6e\40\x74\x68\x65\40\160\141\162\164\40\x6f\x66\x20\164\150\145\x20\x72\145\x71\x75\x65\x73\x74\145\x72\56";
            goto qFF;
        case "\x52\x65\x73\160\157\156\144\145\x72":
            return "\124\150\145\40\162\x65\x71\165\145\163\164\x20\143\x6f\x75\154\144\x20\x6e\x6f\x74\x20\142\145\x20\160\x65\x72\x66\157\162\x6d\x65\x64\40\x64\x75\x65\40\164\x6f\40\x61\x6e\40\145\x72\x72\157\x72\40\157\156\x20\164\150\x65\40\x70\141\x72\x74\40\x6f\x66\x20\164\x68\145\x20\123\x41\x4d\114\x20\162\145\x73\x70\x6f\x6e\x64\x65\x72\x20\x6f\x72\40\x53\101\x4d\x4c\x20\141\x75\x74\150\157\x72\x69\x74\x79\x2e";
            goto qFF;
        case "\x56\x65\162\x73\x69\157\x6e\115\151\163\x6d\x61\x74\143\x68":
            return "\124\x68\145\40\123\x41\115\x4c\40\x72\145\163\x70\157\x6e\x64\x65\x72\x20\143\x6f\x75\x6c\144\x20\x6e\x6f\164\x20\x70\x72\x6f\143\x65\163\x73\40\x74\150\145\40\x72\x65\161\x75\x65\163\164\40\142\145\143\x61\x75\x73\x65\40\x74\150\x65\x20\x76\145\162\x73\151\x6f\x6e\x20\157\146\40\164\x68\145\40\x72\x65\161\x75\x65\163\164\40\x6d\145\163\163\141\x67\x65\x20\167\141\x73\x20\x69\156\x63\x6f\162\162\145\x63\164\56";
            goto qFF;
        default:
            return "\125\x6e\x6b\x6e\x6f\x77\x6e";
    }
    HyR:
    qFF:
}
function saml_get_current_page_url()
{
    $uv = $_SERVER["\110\x54\124\x50\x5f\x48\117\x53\124"];
    if (!(substr($uv, -1) == "\57")) {
        goto BZP;
    }
    $uv = substr($uv, 0, -1);
    BZP:
    $M8 = $_SERVER["\122\x45\x51\x55\105\x53\x54\137\x55\122\x49"];
    if (!(substr($M8, 0, 1) == "\57")) {
        goto mZ2;
    }
    $M8 = substr($M8, 1);
    mZ2:
    $OI = isset($_SERVER["\110\124\x54\x50\x53"]) && strcasecmp($_SERVER["\x48\x54\124\x50\x53"], "\x6f\x6e") == 0;
    $tR = "\x68\164\x74\160" . ($OI ? "\x73" : '') . "\x3a\x2f\57" . $uv . "\57" . $M8;
    return $tR;
}
function get_network_site_url()
{
    $Tz = network_site_url();
    if (!(substr($Tz, -1) == "\57")) {
        goto mJ9;
    }
    $Tz = substr($Tz, 0, -1);
    mJ9:
    return $Tz;
}
function get_current_base_url()
{
    return sprintf("\x25\x73\x3a\57\57\x25\x73\x2f", isset($_SERVER["\x48\124\124\x50\x53"]) && $_SERVER["\x48\x54\x54\120\123"] != "\157\146\146" ? "\150\164\x74\160\x73" : "\x68\x74\164\160", $_SERVER["\x48\x54\124\x50\x5f\x48\117\123\x54"]);
}
add_action("\167\x69\144\147\145\164\x73\x5f\151\156\151\164", function () {
    register_widget("\x6d\x6f\x5f\x6c\x6f\147\x69\156\137\167\151\144");
});
add_action("\151\x6e\x69\164", "\155\x6f\x5f\x6c\157\x67\151\x6e\137\x76\x61\154\151\144\x61\x74\x65");
