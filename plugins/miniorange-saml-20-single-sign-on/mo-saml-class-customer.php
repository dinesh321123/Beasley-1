<?php


class Customersaml
{
    public $email;
    public $phone;
    private $defaultCustomerKey = "\61\x36\65\65\65";
    private $defaultApiKey = "\146\106\x64\x32\130\143\x76\124\107\x44\145\155\132\166\142\167\x31\x62\143\125\x65\x73\116\x4a\x57\x45\x71\113\142\142\x55\161";
    function create_customer($oB)
    {
        $Oy = mo_options_plugin_constants::HOSTNAME . "\57\x6d\157\141\x73\57\x72\x65\x73\164\57\x63\x75\x73\164\157\155\145\x72\57\x61\144\144";
        $current_user = wp_get_current_user();
        $this->email = get_site_option("\x6d\x6f\x5f\x73\141\x6d\x6c\x5f\141\144\155\151\x6e\x5f\x65\x6d\x61\x69\x6c");
        $this->phone = get_site_option("\x6d\157\137\163\x61\155\154\137\141\x64\155\151\x6e\x5f\x70\x68\x6f\x6e\145");
        $sG = get_site_option("\x6d\157\x5f\163\141\x6d\154\137\x61\144\x6d\151\x6e\x5f\x70\141\x73\x73\167\157\162\144");
        $Tm = array("\143\x6f\155\x70\x61\156\x79\x4e\x61\x6d\145" => $_SERVER["\x53\105\122\126\x45\x52\137\x4e\x41\x4d\x45"], "\x61\162\x65\x61\117\146\x49\x6e\x74\x65\x72\145\163\164" => "\127\x50\x20\155\x69\x6e\151\117\x72\141\x6e\x67\145\40\123\101\x4d\x4c\x20\62\56\60\x20\123\x53\x4f\40\x50\x6c\x75\x67\151\x6e", "\146\151\162\x73\x74\x6e\141\155\x65" => $current_user->user_firstname, "\154\141\163\164\156\x61\155\145" => $current_user->user_lastname, "\x65\155\x61\151\154" => $this->email, "\160\x68\157\x6e\x65" => $this->phone, "\x70\x61\x73\x73\x77\x6f\x72\x64" => $sG);
        $Qz = json_encode($Tm);
        $l6 = array("\103\157\x6e\x74\x65\156\x74\55\x54\x79\x70\145" => "\x61\x70\x70\x6c\151\143\141\x74\x69\157\156\57\x6a\163\157\156", "\143\x68\x61\x72\163\145\164" => "\125\x54\106\55\70", "\101\165\x74\150\x6f\162\x69\x7a\x61\164\x69\x6f\x6e" => "\x42\141\x73\x69\143");
        $zr = array("\x6d\x65\164\150\x6f\x64" => "\120\x4f\123\124", "\142\157\x64\x79" => $Qz, "\164\x69\155\145\x6f\x75\x74" => "\x35", "\x72\x65\144\x69\162\x65\x63\x74\x69\x6f\x6e" => "\x35", "\x68\164\164\x70\166\x65\162\163\151\157\x6e" => "\61\x2e\60", "\x62\154\x6f\143\x6b\151\x6e\x67" => true, "\150\145\141\x64\145\162\163" => $l6);
        $iW = Utilities::mo_saml_wp_remote_call($Oy, $zr, $oB);
        return $iW;
    }
    function get_customer_key($oB)
    {
        $Oy = mo_options_plugin_constants::HOSTNAME . "\57\x6d\157\x61\163\x2f\162\x65\163\x74\57\143\165\163\164\157\x6d\x65\162\x2f\x6b\145\x79";
        $sf = get_site_option("\x6d\157\137\x73\x61\x6d\154\137\141\x64\x6d\x69\156\x5f\145\x6d\141\151\x6c");
        $sG = get_site_option("\155\157\137\163\x61\155\x6c\x5f\x61\144\155\151\x6e\x5f\160\x61\163\163\167\157\x72\x64");
        $Tm = array("\x65\x6d\141\x69\154" => $sf, "\160\x61\163\x73\x77\x6f\x72\144" => $sG);
        $Qz = json_encode($Tm);
        $l6 = array("\103\x6f\x6e\x74\145\x6e\x74\55\x54\x79\160\x65" => "\x61\160\160\154\x69\x63\x61\x74\151\157\156\x2f\x6a\x73\x6f\156", "\x63\150\x61\x72\x73\x65\164" => "\125\x54\x46\55\70", "\101\165\164\x68\157\x72\x69\172\x61\x74\x69\x6f\156" => "\102\x61\163\151\x63");
        $zr = array("\155\x65\164\150\157\x64" => "\120\x4f\x53\124", "\142\157\144\171" => $Qz, "\164\151\155\x65\157\165\164" => "\x35", "\162\145\144\x69\x72\145\x63\164\151\x6f\x6e" => "\x35", "\x68\x74\164\160\166\x65\162\x73\151\157\x6e" => "\x31\x2e\60", "\142\x6c\x6f\x63\x6b\151\x6e\147" => true, "\150\x65\x61\x64\x65\x72\163" => $l6);
        $iW = Utilities::mo_saml_wp_remote_call($Oy, $zr, $oB);
        return $iW;
    }
    function check_customer($oB)
    {
        $Oy = mo_options_plugin_constants::HOSTNAME . "\57\155\x6f\141\163\x2f\x72\145\163\164\x2f\143\x75\163\164\157\155\145\162\x2f\143\150\145\143\153\x2d\x69\146\55\x65\170\x69\x73\164\163";
        $sf = get_site_option("\155\x6f\137\x73\x61\x6d\x6c\x5f\141\x64\155\151\156\x5f\x65\x6d\141\151\x6c");
        $Tm = array("\x65\155\x61\x69\154" => $sf);
        $Qz = json_encode($Tm);
        $l6 = array("\x43\157\156\x74\145\156\164\55\124\171\160\x65" => "\x61\160\160\154\151\x63\x61\164\x69\157\x6e\57\152\x73\x6f\x6e", "\x63\150\141\x72\163\145\x74" => "\x55\124\x46\x2d\x38", "\x41\x75\x74\x68\157\162\151\x7a\x61\164\x69\157\x6e" => "\102\x61\x73\x69\x63");
        $zr = array("\155\x65\164\x68\157\144" => "\x50\x4f\123\x54", "\142\157\x64\171" => $Qz, "\164\151\155\x65\157\x75\164" => "\x35", "\x72\145\144\151\x72\x65\x63\x74\151\x6f\x6e" => "\65", "\x68\x74\164\x70\x76\x65\162\163\151\157\x6e" => "\x31\x2e\60", "\x62\x6c\x6f\143\x6b\x69\x6e\147" => true, "\x68\145\x61\144\145\162\163" => $l6);
        $iW = Utilities::mo_saml_wp_remote_call($Oy, $zr, $oB);
        return $iW;
    }
    function send_otp_token($sf, $BH, $oB, $Xo = TRUE, $vM = FALSE)
    {
        $Oy = mo_options_plugin_constants::HOSTNAME . "\x2f\x6d\x6f\141\163\x2f\141\160\151\57\x61\165\x74\x68\57\x63\x68\x61\154\154\x65\156\147\x65";
        $Z8 = $this->defaultCustomerKey;
        $qM = $this->defaultApiKey;
        $Vd = round(microtime(true) * 1000);
        $W9 = $Z8 . number_format($Vd, 0, '', '') . $qM;
        $tT = hash("\163\x68\141\65\x31\62", $W9);
        $Vd = number_format($Vd, 0, '', '');
        if ($Xo) {
            goto Llm;
        }
        $Tm = array("\x63\165\163\x74\x6f\155\145\x72\x4b\145\171" => $Z8, "\160\x68\x6f\x6e\145" => $BH, "\x61\165\164\x68\x54\x79\160\145" => "\x53\115\x53", "\164\x72\x61\x6e\163\141\x63\x74\151\157\156\x4e\x61\155\x65" => "\x57\x50\x20\x6d\x69\x6e\151\x4f\x72\x61\x6e\147\145\40\x53\101\x4d\x4c\x20\62\56\x30\40\x53\123\x4f\x20\120\154\x75\x67\x69\156");
        goto TcE;
        Llm:
        $Tm = array("\143\165\x73\164\157\155\x65\162\x4b\x65\171" => $Z8, "\145\x6d\141\151\154" => $sf, "\141\165\x74\150\124\x79\x70\145" => "\x45\115\x41\111\114", "\x74\162\x61\x6e\163\x61\x63\164\x69\x6f\x6e\x4e\141\155\x65" => "\x57\x50\x20\155\x69\x6e\x69\117\x72\x61\156\147\x65\40\x53\101\115\114\x20\62\x2e\60\x20\x53\123\x4f\x20\120\x6c\165\147\151\156");
        TcE:
        $Qz = json_encode($Tm);
        $l6 = array("\x43\x6f\156\x74\145\x6e\164\55\124\x79\x70\145" => "\x61\x70\160\x6c\151\143\141\x74\151\157\x6e\x2f\x6a\x73\x6f\156", "\x43\165\x73\x74\157\x6d\x65\162\x2d\x4b\x65\171" => $Z8, "\124\151\155\145\x73\164\x61\155\x70" => $Vd, "\101\x75\x74\x68\157\x72\151\x7a\141\164\x69\157\156" => $tT);
        $zr = array("\x6d\145\164\x68\157\144" => "\120\117\x53\124", "\142\157\x64\x79" => $Qz, "\x74\151\155\145\157\x75\164" => "\x35", "\162\x65\144\151\162\145\143\164\151\x6f\x6e" => "\65", "\x68\x74\x74\160\x76\145\162\x73\151\x6f\156" => "\61\x2e\60", "\x62\x6c\157\143\153\151\156\x67" => true, "\150\145\x61\x64\x65\162\163" => $l6);
        $iW = Utilities::mo_saml_wp_remote_call($Oy, $zr, $oB);
        return $iW;
    }
    function validate_otp_token($Sf, $R6, $oB)
    {
        $Oy = mo_options_plugin_constants::HOSTNAME . "\x2f\x6d\157\141\x73\57\x61\160\x69\x2f\141\165\164\x68\57\x76\141\154\151\x64\x61\x74\x65";
        $Z8 = $this->defaultCustomerKey;
        $qM = $this->defaultApiKey;
        $MY = get_site_option("\x6d\157\137\x73\141\155\x6c\x5f\x61\x64\155\151\156\137\145\155\x61\x69\154");
        $Vd = round(microtime(true) * 1000);
        $W9 = $Z8 . number_format($Vd, 0, '', '') . $qM;
        $tT = hash("\163\x68\141\65\61\62", $W9);
        $Vd = number_format($Vd, 0, '', '');
        $Tm = '';
        $Tm = array("\164\x78\111\144" => $Sf, "\164\157\153\x65\156" => $R6);
        $Qz = json_encode($Tm);
        $l6 = array("\x43\x6f\156\x74\x65\x6e\164\x2d\124\171\160\x65" => "\141\160\x70\x6c\151\x63\x61\x74\x69\157\x6e\57\152\163\x6f\x6e", "\x43\x75\163\164\x6f\155\145\162\55\x4b\145\171" => $Z8, "\124\151\x6d\145\163\x74\141\x6d\160" => $Vd, "\101\x75\x74\150\x6f\x72\x69\172\141\164\x69\157\156" => $tT);
        $zr = array("\x6d\145\x74\150\x6f\x64" => "\120\x4f\x53\124", "\142\x6f\144\x79" => $Qz, "\x74\151\x6d\145\x6f\165\164" => "\65", "\162\x65\x64\x69\x72\x65\143\x74\151\157\x6e" => "\65", "\x68\x74\x74\160\166\x65\x72\x73\151\157\156" => "\61\56\x30", "\142\154\157\143\153\151\x6e\147" => true, "\x68\145\x61\144\145\162\163" => $l6);
        $iW = Utilities::mo_saml_wp_remote_call($Oy, $zr, $oB);
        return $iW;
    }
    function submit_contact_us($sf, $BH, $wQ, $oB)
    {
        $current_user = wp_get_current_user();
        $wQ = "\133\x57\120\40\123\101\115\114\40\62\56\x30\x20\x53\x65\162\x76\x69\x63\145\40\x50\162\157\166\x69\x64\x65\x72\x20\x53\123\x4f\40\x4c\x6f\x67\151\x6e\40\120\154\165\147\151\156\135\x20" . $wQ;
        $Tm = array("\146\x69\x72\163\164\x4e\141\155\x65" => $current_user->user_firstname, "\154\141\x73\164\x4e\x61\155\145" => $current_user->user_lastname, "\143\x6f\155\160\x61\x6e\x79" => $_SERVER["\123\x45\x52\x56\105\122\137\x4e\x41\x4d\x45"], "\145\155\141\151\154" => $sf, "\160\150\x6f\x6e\145" => $BH, "\161\165\145\x72\171" => $wQ);
        $Qz = json_encode($Tm);
        $Oy = mo_options_plugin_constants::HOSTNAME . "\x2f\155\x6f\x61\163\57\x72\x65\x73\164\x2f\143\x75\x73\x74\x6f\x6d\x65\x72\x2f\143\157\156\164\x61\x63\164\55\165\163";
        $l6 = array("\x43\x6f\x6e\164\145\x6e\164\55\124\x79\x70\145" => "\141\x70\x70\154\x69\143\x61\x74\x69\157\156\57\152\x73\157\x6e", "\143\150\x61\x72\x73\x65\164" => "\125\124\x46\55\70", "\x41\x75\164\150\x6f\x72\x69\172\141\164\x69\157\x6e" => "\102\141\163\151\143");
        $zr = array("\x6d\x65\164\150\157\x64" => "\x50\x4f\x53\124", "\x62\x6f\144\171" => $Qz, "\164\x69\155\145\x6f\165\x74" => "\x35", "\162\145\144\151\x72\145\x63\x74\x69\157\156" => "\x35", "\x68\164\x74\160\x76\145\162\163\x69\157\x6e" => "\x31\56\x30", "\x62\x6c\x6f\143\x6b\151\156\x67" => true, "\150\x65\x61\144\x65\162\x73" => $l6);
        $iW = Utilities::mo_saml_wp_remote_call($Oy, $zr, $oB);
        return $iW;
    }
    function mo_saml_send_alert_email($Jf, $oB)
    {
        $Oy = mo_options_plugin_constants::HOSTNAME . "\57\155\x6f\141\163\x2f\141\160\x69\57\156\157\x74\151\x66\171\x2f\x73\145\156\144";
        $Z8 = get_site_option("\x6d\157\x5f\x73\x61\x6d\154\137\141\144\x6d\x69\x6e\x5f\x63\x75\x73\164\x6f\x6d\145\x72\137\x6b\145\x79");
        $qM = get_site_option("\x6d\157\x5f\x73\141\155\x6c\137\141\x64\x6d\151\x6e\137\x61\160\151\x5f\x6b\x65\171");
        $Vd = round(microtime(true) * 1000);
        $W9 = $Z8 . number_format($Vd, 0, '', '') . $qM;
        $tT = hash("\163\150\141\65\x31\x32", $W9);
        $Vd = number_format($Vd, 0, '', '');
        $gB = get_site_option("\155\157\x5f\163\x61\155\154\137\x61\x64\x6d\x69\156\137\x65\155\x61\x69\154");
        $Zg = "\110\x65\154\154\157\x2c\x3c\142\162\76\74\x62\x72\76\131\x6f\x75\162\40\x3c\x62\x3e\106\x52\x45\105\40\124\x72\x69\141\x6c\74\57\x62\76\x20\x77\x69\x6c\x6c\x20\x65\170\160\151\162\x65\x20\151\x6e\x20" . $Jf . "\40\144\x61\x79\163\40\x66\157\162\x20\x6d\x69\156\x69\117\162\x61\156\147\x65\x20\x53\x41\x4d\114\40\160\154\x75\x67\x69\x6e\40\157\156\x20\x79\x6f\165\162\x20\x77\x65\x62\163\x69\164\x65\40\x3c\142\x3e" . get_bloginfo() . "\x3c\57\142\x3e\x2e\74\x62\x72\76\x3c\x62\162\76\x3c\141\x20\x68\162\x65\x66\x3d\x27\x68\x74\164\160\x73\72\x2f\57\x6c\157\147\x69\156\56\170\145\143\x75\x72\x69\x66\171\56\143\157\x6d\57\x6d\x6f\141\x73\57\154\x6f\x67\x69\156\x3f\162\x65\x64\151\162\x65\143\x74\125\162\x6c\75\x68\x74\164\x70\x73\72\57\57\x6c\157\147\x69\x6e\56\170\x65\143\165\x72\x69\x66\171\56\x63\x6f\155\x2f\155\157\141\x73\57\151\156\x69\164\151\141\154\x69\x7a\x65\x70\141\x79\155\x65\x6e\x74\x26\x72\x65\x71\x75\x65\x73\x74\x4f\x72\x69\x67\x69\156\x3d\x77\x70\x5f\x73\x61\155\154\x5f\x73\x73\x6f\137\142\x61\x73\x69\143\137\160\x6c\141\156\x27\76\103\x6c\151\x63\x6b\40\150\x65\x72\x65\74\57\141\x3e\x20\x74\157\x20\165\160\x67\162\x61\x64\x65\40\x74\157\x20\157\165\x72\40\160\x72\145\x6d\x69\x75\x6d\x20\160\154\141\156\x20\x73\x6f\157\156\x20\x69\146\x20\x79\x6f\x75\40\x77\x61\156\x74\40\x74\x6f\40\x63\x6f\156\164\151\x6e\x75\x65\x20\165\x73\151\x6e\x67\x20\x6f\x75\x72\40\x70\x6c\x75\x67\151\x6e\x2e\40\131\x6f\165\40\143\141\x6e\40\162\x65\146\145\162\40\114\x69\x63\x65\x6e\163\151\x6e\x67\x20\164\141\x62\40\146\x6f\162\40\x6f\x75\x72\x20\160\x72\145\x6d\151\x75\x6d\x20\x70\154\x61\156\163\56\x3c\142\x72\76\74\x62\162\76\x54\x68\x61\156\153\163\x2c\74\142\x72\76\x6d\x69\156\151\x4f\162\x61\x6e\147\145";
        $kP = "\124\x72\x69\x61\x6c\x20\166\x65\x72\x73\151\157\x6e\x20\145\170\x70\151\x72\x69\156\147\40\x69\x6e\x20" . $Jf . "\40\x64\141\171\x73\x20\x66\157\x72\40\155\151\x6e\151\117\162\141\x6e\147\x65\40\123\x41\x4d\x4c\x20\x70\154\x75\x67\151\156\x20\174\40" . get_bloginfo();
        if (!($Jf == 1)) {
            goto gQp;
        }
        $Zg = str_replace("\144\141\x79\x73", "\144\x61\171", $Zg);
        $kP = str_replace("\144\141\171\163", "\144\141\171", $kP);
        gQp:
        $Tm = array("\x63\165\163\164\157\155\x65\x72\x4b\x65\x79" => $Z8, "\163\145\x6e\x64\x45\x6d\x61\x69\154" => true, "\x65\155\141\151\154" => array("\x63\165\x73\x74\157\x6d\x65\x72\x4b\x65\x79" => $Z8, "\146\162\x6f\155\105\155\141\x69\154" => "\x69\156\146\x6f\x40\x78\x65\143\165\162\151\146\171\x2e\x63\157\x6d", "\142\x63\143\105\155\x61\151\x6c" => "\141\x6e\x69\x72\x62\141\x6e\x40\x78\x65\x63\x75\162\151\x66\171\x2e\143\x6f\x6d", "\x66\x72\157\155\116\141\x6d\x65" => "\155\x69\156\x69\x4f\x72\141\x6e\x67\145", "\x74\157\x45\x6d\141\x69\154" => $gB, "\x74\x6f\116\x61\155\145" => $gB, "\163\165\x62\152\145\x63\164" => $kP, "\x63\x6f\x6e\x74\145\x6e\164" => $Zg));
        $Qz = json_encode($Tm);
        $l6 = array("\103\157\x6e\x74\x65\x6e\x74\x2d\124\171\x70\x65" => "\141\160\x70\154\151\143\141\x74\x69\x6f\156\x2f\152\x73\x6f\x6e", "\x43\165\163\164\x6f\155\x65\162\x2d\113\145\171" => $Z8, "\x54\x69\155\145\163\x74\x61\155\x70" => $Vd, "\x41\x75\164\x68\x6f\162\x69\172\141\x74\151\157\x6e" => $tT);
        $zr = array("\155\145\164\x68\x6f\144" => "\x50\117\123\124", "\142\x6f\144\x79" => $Qz, "\x74\151\155\145\x6f\x75\164" => "\x35", "\162\x65\x64\151\x72\145\143\164\x69\157\156" => "\x35", "\x68\x74\x74\x70\x76\x65\162\163\151\x6f\x6e" => "\61\56\60", "\142\154\157\x63\x6b\151\x6e\x67" => true, "\x68\x65\x61\144\145\x72\163" => $l6);
        $iW = Utilities::mo_saml_wp_remote_call($Oy, $zr, $oB);
        return $iW;
    }
    function mo_saml_forgot_password($sf, $oB)
    {
        $Oy = mo_options_plugin_constants::HOSTNAME . "\x2f\x6d\157\141\x73\57\x72\145\163\164\x2f\143\165\163\x74\157\155\145\162\x2f\x70\x61\x73\163\x77\x6f\x72\x64\x2d\162\145\x73\145\x74";
        $Z8 = get_site_option("\155\157\x5f\163\x61\155\x6c\137\x61\x64\155\x69\156\137\x63\x75\163\164\x6f\x6d\145\162\x5f\153\145\171");
        $qM = get_site_option("\155\157\x5f\x73\x61\155\154\137\x61\144\155\151\156\x5f\141\x70\151\137\x6b\x65\x79");
        $Vd = round(microtime(true) * 1000);
        $W9 = $Z8 . number_format($Vd, 0, '', '') . $qM;
        $tT = hash("\x73\150\x61\65\x31\62", $W9);
        $Vd = number_format($Vd, 0, '', '');
        $Tm = '';
        $Tm = array("\x65\x6d\x61\x69\x6c" => $sf);
        $Qz = json_encode($Tm);
        $l6 = array("\x43\157\156\164\145\x6e\x74\55\x54\x79\160\x65" => "\x61\x70\160\154\151\143\x61\x74\151\157\156\x2f\152\x73\157\x6e", "\103\x75\163\x74\157\155\x65\x72\x2d\113\145\x79" => $Z8, "\x54\x69\x6d\145\x73\x74\141\x6d\x70" => $Vd, "\x41\165\x74\x68\x6f\x72\151\x7a\141\164\151\157\156" => $tT);
        $zr = array("\155\x65\164\x68\157\x64" => "\120\117\123\x54", "\142\x6f\x64\171" => $Qz, "\x74\x69\155\x65\157\165\164" => "\x35", "\x72\x65\144\151\x72\x65\x63\164\x69\157\156" => "\65", "\x68\164\164\x70\166\x65\162\163\151\157\x6e" => "\61\x2e\60", "\x62\154\x6f\x63\153\151\156\x67" => true, "\150\145\141\x64\x65\162\163" => $l6);
        $iW = Utilities::mo_saml_wp_remote_call($Oy, $zr, $oB);
        return $iW;
    }
    function mo_saml_verify_license($wC, $oB)
    {
        $Oy = mo_options_plugin_constants::HOSTNAME . "\x2f\155\157\141\163\57\x61\160\x69\x2f\142\141\x63\153\x75\x70\x63\157\144\x65\57\x76\145\x72\x69\146\x79";
        $Z8 = get_site_option("\x6d\157\x5f\163\141\x6d\x6c\137\141\144\x6d\x69\x6e\x5f\143\x75\x73\x74\157\155\145\162\137\x6b\145\171");
        $qM = get_site_option("\x6d\157\x5f\163\141\x6d\154\137\x61\x64\155\151\x6e\x5f\141\160\x69\137\x6b\145\x79");
        $Vd = round(microtime(true) * 1000);
        $W9 = $Z8 . number_format($Vd, 0, '', '') . $qM;
        $tT = hash("\x73\150\x61\65\x31\x32", $W9);
        $Vd = number_format($Vd, 0, '', '');
        $Tm = '';
        $Tm = array("\x63\157\144\145" => $wC, "\x63\165\x73\x74\157\x6d\145\x72\113\145\x79" => $Z8, "\141\144\x64\151\164\151\157\156\141\154\106\151\145\x6c\x64\163" => array("\146\151\145\x6c\x64\61" => site_url()));
        $Qz = json_encode($Tm);
        $l6 = array("\103\x6f\x6e\x74\x65\x6e\x74\55\124\x79\x70\x65" => "\x61\160\x70\154\151\143\141\164\x69\x6f\x6e\57\152\x73\x6f\x6e", "\x43\x75\x73\x74\x6f\x6d\x65\x72\55\113\x65\x79" => $Z8, "\x54\x69\x6d\145\163\x74\x61\155\x70" => $Vd, "\101\x75\x74\x68\x6f\x72\151\172\141\164\151\x6f\x6e" => $tT);
        $zr = array("\x6d\x65\164\x68\157\x64" => "\x50\x4f\x53\x54", "\142\157\144\171" => $Qz, "\x74\151\x6d\145\x6f\x75\x74" => "\65", "\162\145\144\151\162\145\x63\x74\151\x6f\156" => "\x35", "\150\164\164\x70\x76\145\162\x73\x69\x6f\156" => "\x31\56\x30", "\x62\x6c\x6f\143\x6b\151\156\147" => true, "\x68\145\141\144\x65\162\x73" => $l6);
        $iW = Utilities::mo_saml_wp_remote_call($Oy, $zr, $oB);
        return $iW;
    }
    function check_customer_ln($oB)
    {
        $Oy = mo_options_plugin_constants::HOSTNAME . "\57\x6d\x6f\141\163\x2f\x72\145\163\x74\57\143\165\x73\164\157\155\x65\x72\57\154\x69\x63\x65\x6e\x73\145";
        $Z8 = get_site_option("\155\157\x5f\x73\141\155\x6c\137\141\144\x6d\x69\x6e\137\x63\x75\x73\x74\x6f\155\x65\162\137\153\x65\x79");
        $qM = get_site_option("\x6d\157\x5f\x73\141\155\x6c\137\x61\x64\x6d\x69\x6e\137\141\160\151\137\153\x65\171");
        $Vd = round(microtime(true) * 1000);
        $W9 = $Z8 . number_format($Vd, 0, '', '') . $qM;
        $tT = hash("\163\150\141\x35\x31\62", $W9);
        $Vd = number_format($Vd, 0, '', '');
        $Tm = '';
        $Tm = array("\143\165\163\164\x6f\x6d\145\162\111\x64" => $Z8, "\141\160\x70\154\151\x63\x61\x74\151\x6f\156\x4e\x61\x6d\x65" => "\167\x70\x5f\x73\141\x6d\x6c\137\x73\x73\x6f\x5f\155\x75\154\164\151\163\151\164\x65\137\142\141\x73\x69\143\137\x70\x6c\141\156");
        $Qz = json_encode($Tm);
        $l6 = array("\103\157\156\164\x65\x6e\x74\55\x54\x79\160\145" => "\x61\160\160\x6c\x69\143\141\164\x69\x6f\x6e\x2f\x6a\x73\157\156", "\x43\x75\x73\x74\157\x6d\x65\x72\55\x4b\145\x79" => $Z8, "\124\x69\x6d\x65\x73\x74\x61\x6d\160" => $Vd, "\x41\165\164\150\x6f\x72\151\x7a\141\x74\151\157\x6e" => $tT);
        $zr = array("\155\x65\164\150\x6f\144" => "\x50\x4f\x53\124", "\142\x6f\x64\x79" => $Qz, "\164\x69\155\x65\x6f\x75\x74" => "\65", "\162\145\144\151\162\145\x63\x74\151\157\x6e" => "\65", "\x68\164\164\160\166\x65\162\163\151\x6f\x6e" => "\61\x2e\60", "\x62\154\157\143\153\151\156\147" => true, "\x68\x65\x61\x64\145\x72\163" => $l6);
        $iW = Utilities::mo_saml_wp_remote_call($Oy, $zr, $oB);
        return $iW;
    }
    function mo_saml_update_status($oB)
    {
        $Oy = mo_options_plugin_constants::HOSTNAME . "\57\x6d\x6f\x61\163\x2f\141\x70\x69\x2f\x62\141\143\153\165\160\x63\x6f\144\145\x2f\x75\160\144\x61\164\x65\163\x74\141\x74\x75\163";
        $Z8 = get_site_option("\155\157\x5f\163\x61\155\154\137\141\144\x6d\151\156\137\x63\165\x73\164\x6f\x6d\145\x72\x5f\x6b\x65\x79");
        $qM = get_site_option("\x6d\x6f\137\163\x61\155\x6c\x5f\x61\x64\155\x69\x6e\x5f\x61\160\x69\137\x6b\145\171");
        $Vd = round(microtime(true) * 1000);
        $W9 = $Z8 . number_format($Vd, 0, '', '') . $qM;
        $tT = hash("\163\x68\141\x35\61\x32", $W9);
        $Z1 = get_site_option("\155\157\137\x73\141\x6d\154\x5f\143\x75\163\x74\157\155\145\x72\137\x74\157\x6b\x65\156");
        $wC = AESEncryption::decrypt_data(get_site_option("\x73\x6d\154\x5f\x6c\x6b"), $Z1);
        $Tm = array("\x63\x6f\x64\x65" => $wC, "\143\165\x73\164\x6f\x6d\x65\162\x4b\145\171" => $Z8);
        $Qz = json_encode($Tm);
        $Vd = number_format($Vd, 0, '', '');
        $l6 = array("\103\x6f\156\x74\145\x6e\164\55\x54\x79\x70\145" => "\x61\160\160\154\x69\x63\141\164\151\x6f\156\x2f\152\x73\x6f\x6e", "\103\165\163\x74\157\155\x65\162\x2d\113\145\x79" => $Z8, "\124\151\155\x65\x73\164\141\x6d\160" => $Vd, "\x41\x75\164\150\157\162\151\x7a\141\x74\151\x6f\x6e" => $tT);
        $zr = array("\155\x65\164\x68\x6f\144" => "\120\x4f\123\124", "\142\157\x64\171" => $Qz, "\x74\151\155\x65\157\x75\x74" => "\65", "\x72\145\144\151\162\145\143\164\151\157\156" => "\65", "\150\x74\164\160\x76\x65\162\x73\151\157\x6e" => "\x31\56\60", "\142\x6c\157\x63\153\x69\x6e\x67" => true, "\x68\145\141\x64\x65\x72\163" => $l6);
        $iW = Utilities::mo_saml_wp_remote_call($Oy, $zr, $oB);
        return $iW;
    }
    function mo_saml_send_alert_email_for_license($Kp, $oB)
    {
        $Oy = mo_options_plugin_constants::HOSTNAME . "\57\155\x6f\x61\163\57\x61\160\x69\x2f\156\157\x74\x69\146\x79\57\163\x65\x6e\144";
        $Z8 = get_site_option("\x6d\157\x5f\x73\x61\x6d\154\137\141\144\x6d\x69\156\x5f\143\x75\163\164\x6f\x6d\x65\x72\137\x6b\x65\x79");
        $qM = get_site_option("\155\157\137\163\141\155\154\137\141\144\x6d\x69\156\x5f\141\160\x69\137\x6b\145\x79");
        $Vd = round(microtime(true) * 1000);
        $W9 = $Z8 . number_format($Vd, 0, '', '') . $qM;
        $tT = hash("\163\x68\x61\65\x31\x32", $W9);
        $j5 = "\x43\165\163\x74\157\x6d\145\162\x2d\x4b\145\x79\x3a\40" . $Z8;
        $Gt = "\x54\x69\x6d\x65\163\x74\141\x6d\x70\72\40" . number_format($Vd, 0, '', '');
        $Q6 = "\x41\165\164\150\157\162\151\x7a\141\164\151\157\156\x3a\x20" . $tT;
        $gB = get_site_option("\155\157\x5f\163\x61\x6d\x6c\x5f\141\x64\x6d\151\156\x5f\x65\x6d\141\151\154");
        if (function_exists("\x67\145\164\x5f\163\x69\164\145\x73") && class_exists("\127\x50\x5f\x53\x69\164\x65\137\121\165\x65\x72\171")) {
            goto NfZ;
        }
        $C_ = count(wp_get_sites());
        goto TDW;
        NfZ:
        $C_ = count(get_sites());
        TDW:
        $Zg = "\x48\x65\x6c\154\x6f\x2c\74\142\x72\76\x3c\142\162\76\x59\x6f\x75\x20\150\141\x76\x65\x20\160\x75\x72\143\x68\x61\163\x65\x64\40\154\151\143\x65\156\x73\145\x20\x66\x6f\x72\40\123\101\115\114\40\x53\151\156\147\154\x65\x20\123\151\147\156\55\117\156\40\120\x6c\165\x67\151\x6e\40\x66\x6f\x72\x20\74\x62\76" . $Kp . "\40\x73\151\164\x65\163\x3c\57\x62\76\56\40\x41\163\x20\x6e\x75\155\142\x65\162\40\157\x66\40\x73\x69\164\x65\x73\40\x69\156\x20\x79\x6f\165\162\x20\x6d\x75\154\164\151\x73\x69\164\145\x20\156\145\x74\167\157\162\153\40\150\141\166\145\40\x67\x72\x6f\x77\x6e\x20\164\157\40" . $C_ . "\40\163\x69\x74\x65\163\x20\x6e\157\x77\56\x20\x59\157\165\x20\163\x68\x6f\x75\154\144\40\165\160\147\162\141\144\145\40\x79\x6f\x75\162\40\154\151\x63\x65\156\x73\145\x20\146\x6f\162\40\x6d\x69\x6e\151\117\162\141\156\147\145\40\123\101\x4d\x4c\40\x70\x6c\x75\147\151\x6e\40\157\156\40\171\x6f\x75\162\x20\167\145\142\163\151\164\x65\x20\74\142\x3e" . get_bloginfo() . "\74\57\142\x3e\x2e\x3c\x62\x72\76\74\142\162\x3e\x3c\141\x20\x68\x72\x65\146\x3d\x27\150\x74\x74\x70\x73\x3a\x2f\57\x6c\157\147\151\156\x2e\170\145\143\x75\x72\x69\x66\171\56\x63\157\155\57\155\x6f\x61\x73\x2f\x6c\157\x67\151\x6e\77\162\145\144\151\x72\145\143\x74\x55\162\154\75\x68\x74\164\160\163\72\57\57\154\x6f\x67\x69\156\x2e\170\145\143\165\162\x69\146\x79\x2e\143\157\155\x2f\155\x6f\x61\x73\57\x69\156\151\x74\x69\x61\x6c\151\172\x65\x70\141\171\x6d\145\156\x74\46\x72\x65\x71\165\x65\x73\164\x4f\x72\x69\147\151\156\x3d\x77\160\x5f\x73\141\x6d\x6c\x5f\x70\162\145\x6d\151\x75\x6d\x5f\155\165\154\x74\151\163\x69\164\145\x5f\163\x73\x6f\137\165\x70\147\x72\141\x64\x65\137\160\x6c\x61\156\47\76\x43\x6c\x69\x63\x6b\x20\150\x65\x72\x65\x3c\57\141\x3e\x20\164\157\x20\142\165\x79\40\164\x68\x65\40\x6c\151\143\x65\x6e\x73\x65\40\x66\157\162\40" . $Kp . "\x20\163\151\164\x65\163\x20\x74\x6f\x20\x63\157\x6e\x74\151\156\x75\x65\x20\x75\163\151\x6e\x67\x20\157\x75\162\40\160\154\x75\x67\151\156\56\74\x62\x72\76\x3c\142\162\76\x54\150\141\156\153\x73\54\x3c\x62\x72\76\155\151\x6e\151\117\162\141\x6e\147\x65";
        $kP = "\x45\x78\143\x65\145\x64\145\x64\40\114\x69\x63\145\156\x73\145\40\114\x69\155\x69\164\40\106\157\162\x20\x4e\157\40\x4f\146\x20\123\x69\164\x65\x73\x20\x2d\x20\127\x6f\x72\x64\x50\162\x65\163\163\40\x53\x41\115\x4c\40\x53\x69\x6e\147\154\x65\x20\123\151\147\x6e\x2d\x4f\156\x20\120\162\x65\155\x69\165\x6d\x20\x50\x6c\165\147\151\x6e\40\x62\171\40\x6d\x69\156\151\x4f\x72\x61\156\147\x65\40\174\x20" . get_bloginfo();
        $Vd = number_format($Vd, 0, '', '');
        update_site_option("\x6c\151\143\x65\156\x73\x65\x5f\x61\x6c\x65\162\x74\137\145\x6d\x61\151\154\137\x73\145\156\164", 1);
        $Tm = array("\x63\165\163\x74\157\x6d\x65\x72\x4b\x65\171" => $Z8, "\x73\x65\156\x64\x45\x6d\x61\x69\154" => true, "\145\x6d\141\151\154" => array("\143\165\x73\164\157\155\145\x72\x4b\145\171" => $Z8, "\x66\162\157\155\105\x6d\141\x69\154" => "\x69\x6e\x66\x6f\x40\x78\145\143\x75\162\151\x66\x79\x2e\143\x6f\x6d", "\142\x63\143\x45\155\x61\151\x6c" => "\x69\x6e\146\x6f\x40\170\145\x63\165\x72\151\146\171\x2e\143\157\x6d", "\146\162\157\155\x4e\x61\x6d\x65" => "\x6d\151\156\151\117\x72\x61\156\x67\x65", "\x74\157\105\155\x61\151\x6c" => $gB, "\164\x6f\116\141\x6d\145" => $gB, "\x73\x75\142\152\x65\143\164" => $kP, "\x63\x6f\156\x74\145\x6e\164" => $Zg));
        $Qz = json_encode($Tm);
        $l6 = array("\103\x6f\156\x74\x65\x6e\164\x2d\124\x79\x70\x65" => "\141\160\160\x6c\x69\143\x61\164\151\x6f\156\x2f\x6a\x73\157\x6e", "\103\165\x73\164\157\x6d\145\x72\x2d\113\x65\x79" => $Z8, "\124\x69\x6d\145\163\164\141\x6d\x70" => $Vd, "\101\x75\x74\x68\157\162\151\x7a\141\164\x69\157\x6e" => $tT);
        $zr = array("\155\145\164\150\x6f\144" => "\x50\x4f\x53\124", "\142\x6f\144\171" => $Qz, "\164\x69\155\x65\157\x75\164" => "\65", "\x72\145\144\151\162\145\143\x74\151\x6f\156" => "\65", "\x68\x74\164\x70\166\x65\x72\163\151\x6f\156" => "\x31\x2e\60", "\142\154\x6f\x63\153\x69\x6e\x67" => true, "\x68\145\x61\x64\145\x72\x73" => $l6);
        $iW = Utilities::mo_saml_wp_remote_call($Oy, $zr, $oB);
        return $iW;
    }
    function mo_saml_send_user_exceeded_alert_email($XO, $oB)
    {
        $Oy = mo_options_plugin_constants::HOSTNAME . "\57\155\157\141\163\57\x61\160\x69\57\x6e\x6f\164\x69\x66\171\x2f\163\x65\x6e\144";
        $Z8 = get_site_option("\x6d\157\137\x73\x61\x6d\154\137\141\x64\155\x69\156\x5f\143\x75\163\164\157\x6d\145\162\x5f\153\145\x79");
        $qM = get_site_option("\x6d\x6f\x5f\163\141\x6d\154\x5f\x61\x64\155\x69\156\x5f\x61\160\151\x5f\x6b\x65\x79");
        $Vd = round(microtime(true) * 1000);
        $W9 = $Z8 . number_format($Vd, 0, '', '') . $qM;
        $tT = hash("\163\x68\141\x35\61\x32", $W9);
        $gB = get_site_option("\155\x6f\x5f\163\x61\155\154\x5f\141\144\x6d\151\x6e\137\x65\x6d\141\151\154");
        $Zg = "\x48\x65\x6c\x6c\157\54\x3c\x62\162\76\x3c\142\162\x3e\x59\x6f\x75\40\150\x61\166\145\x20\x70\165\162\143\x68\141\x73\145\x64\x20\154\x69\143\145\x6e\x73\145\40\x66\x6f\162\x20\123\101\x4d\x4c\40\123\151\156\147\x6c\145\40\123\x69\147\156\x2d\117\x6e\40\x50\x6c\165\147\x69\156\x20\x66\x6f\162\40\x3c\x62\x3e" . $XO . "\40\x75\163\x65\x72\163\x3c\x2f\142\76\x2e\40\101\163\40\156\165\x6d\142\145\162\40\157\x66\40\x75\x73\x65\x72\x73\40\x6f\156\40\x79\157\165\162\x20\x73\x69\164\145\40\x68\x61\166\145\x20\x67\x72\157\167\156\40\164\157\40\x6d\157\x72\x65\40\164\x68\x61\156\40" . $XO . "\x20\165\x73\145\162\163\x20\x6e\x6f\x77\56\x20\x59\x6f\165\40\163\150\x6f\x75\154\x64\40\x75\x70\x67\162\x61\x64\145\40\x79\x6f\x75\x72\x20\x6c\x69\143\145\x6e\x73\145\x20\x66\x6f\162\x20\155\x69\x6e\x69\x4f\x72\141\x6e\x67\145\40\123\x41\x4d\x4c\40\160\154\165\x67\151\x6e\x20\x6f\x6e\x20\171\x6f\x75\x72\x20\x77\x65\142\163\x69\x74\x65\40\74\x62\76" . get_bloginfo() . "\x3c\57\x62\76\x2e\x3c\x62\x72\76\x3c\x62\162\76\74\x61\40\x68\x72\145\x66\x3d\x27" . mo_options_plugin_constants::HOSTNAME . "\x2f\155\x6f\x61\x73\57\154\x6f\147\151\156\77\x72\x65\144\x69\162\145\x63\164\x55\x72\154\75" . mo_options_plugin_constants::HOSTNAME . "\x2f\151\x6e\151\164\151\x61\154\x69\x7a\x65\x70\141\x79\x6d\x65\156\164\46\x72\145\161\x75\145\163\164\x4f\162\151\x67\151\x6e\75\167\x70\x5f\163\141\155\x6c\x5f\160\162\x65\x6d\151\x75\155\137\155\165\154\x74\x69\x73\151\x74\145\x5f\163\163\157\x5f\165\x70\147\162\141\x64\x65\x5f\x70\x6c\141\156\x27\x3e\x43\x6c\151\x63\x6b\x20\x68\x65\x72\x65\x3c\57\x61\76\x20\164\x6f\x20\165\160\147\162\x61\x64\x65\40\164\x68\x65\40\x6c\x69\143\x65\x6e\163\145\40\x74\x6f\40\x63\x6f\x6e\164\x69\x6e\x75\145\40\165\x73\x69\156\147\x20\157\x75\x72\40\x70\154\x75\x67\151\x6e\x2e\x3c\142\x72\76\74\142\x72\x3e\x54\x68\141\x6e\153\x73\54\74\142\162\76\x6d\151\x6e\151\x4f\x72\141\156\x67\x65";
        $kP = "\105\170\x63\145\145\144\145\144\40\114\x69\x63\145\x6e\x73\x65\40\x4c\151\155\151\x74\40\x46\x6f\162\40\x4e\157\x20\117\x66\x20\x55\x73\x65\x72\163\x20\x2d\40\127\x6f\x72\144\x50\162\145\163\163\40\123\x41\115\x4c\x20\123\151\x6e\x67\x6c\145\x20\123\151\x67\156\55\117\x6e\x20\x50\154\x75\x67\x69\156\40\174\40" . get_bloginfo();
        $Vd = number_format($Vd, 0, '', '');
        update_site_option("\165\163\x65\x72\x5f\141\x6c\145\162\164\137\x65\x6d\x61\151\154\x5f\163\145\156\164", 1);
        $Tm = array("\143\x75\x73\x74\157\x6d\145\162\113\x65\x79" => $Z8, "\163\145\x6e\144\105\x6d\141\151\154" => true, "\x65\155\x61\151\x6c" => array("\143\165\x73\164\157\155\145\x72\x4b\145\x79" => $Z8, "\146\x72\x6f\x6d\105\155\x61\151\154" => "\x69\x6e\x66\157\x40\170\x65\143\x75\162\151\x66\x79\x2e\143\157\155", "\x62\143\143\105\x6d\141\151\154" => "\x69\156\146\157\x40\170\145\143\165\x72\151\x66\171\x2e\143\x6f\x6d", "\146\x72\157\155\x4e\x61\155\145" => "\155\x69\x6e\151\117\162\141\156\x67\x65", "\x74\x6f\x45\155\x61\151\x6c" => $gB, "\x74\x6f\x4e\x61\155\x65" => $gB, "\x73\x75\x62\152\x65\x63\164" => $kP, "\143\x6f\x6e\x74\x65\x6e\164" => $Zg));
        $Qz = json_encode($Tm);
        $l6 = array("\103\x6f\156\x74\145\156\164\x2d\124\x79\160\x65" => "\141\x70\160\154\x69\143\x61\x74\x69\157\156\x2f\x6a\x73\157\x6e", "\103\x75\163\164\157\x6d\x65\x72\x2d\x4b\x65\x79" => $Z8, "\124\151\x6d\x65\163\164\141\155\160" => $Vd, "\x41\x75\x74\150\x6f\162\151\x7a\141\x74\x69\157\x6e" => $tT);
        $zr = array("\155\x65\x74\x68\157\144" => "\120\x4f\123\x54", "\142\157\x64\171" => $Qz, "\x74\151\155\x65\157\165\x74" => "\65", "\162\x65\144\x69\162\145\x63\x74\151\157\x6e" => "\x35", "\150\164\164\x70\x76\145\162\163\151\157\156" => "\x31\56\x30", "\142\x6c\157\143\x6b\151\x6e\147" => true, "\x68\x65\141\x64\x65\162\163" => $l6);
        $iW = Utilities::mo_saml_wp_remote_call($Oy, $zr, $oB);
        return $iW;
    }
}
?>
