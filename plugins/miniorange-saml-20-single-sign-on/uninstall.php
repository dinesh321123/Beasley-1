<?php


require_once dirname(__FILE__) . "\57\151\156\x63\154\x75\144\x65\x73\x2f\154\x69\142\57\155\x6f\55\157\160\164\151\x6f\156\163\55\x65\x6e\x75\155\x2e\x70\x68\160";
require_once dirname(__FILE__) . "\57\111\155\x70\x6f\162\164\x2d\x65\x78\160\x6f\162\x74\56\160\150\160";
define("\x55\156\151\x6e\x73\x74\x61\154\x6c\137\103\x6c\x61\163\x73\x5f\116\141\155\145\163", serialize(array("\123\123\117\x5f\x4c\157\147\x69\156" => "\x6d\x6f\137\157\160\x74\x69\x6f\156\x73\137\x65\x6e\165\155\x5f\x73\x73\157\137\x6c\157\x67\x69\156", "\111\144\x65\156\164\x69\x74\171\x5f\x50\162\x6f\x76\x69\144\145\x72" => "\x6d\x6f\137\x6f\x70\164\x69\157\x6e\163\x5f\x65\156\165\155\137\151\144\145\x6e\164\151\164\171\x5f\160\162\x6f\166\x69\x64\145\162", "\x53\145\x72\166\151\143\x65\x5f\x50\x72\157\x76\151\x64\145\162" => "\155\x6f\x5f\157\x70\x74\x69\157\156\x73\137\145\x6e\x75\x6d\x5f\163\145\x72\166\151\143\x65\137\160\162\157\166\151\x64\145\162", "\101\164\164\x72\151\142\x75\x74\145\137\115\x61\x70\x70\151\x6e\147" => "\155\157\x5f\157\160\x74\151\x6f\156\x73\137\x65\156\x75\155\137\x61\x74\164\x72\x69\x62\165\164\145\137\x6d\x61\x70\160\151\156\x67", "\x44\157\x6d\141\151\156\x5f\x52\x65\x73\x74\x72\151\x63\164\x69\157\156" => "\x6d\157\137\x6f\160\164\151\x6f\156\163\137\145\156\165\x6d\x5f\144\157\x6d\141\151\156\x5f\x72\145\163\x74\x72\x69\143\164\151\157\x6e", "\x52\157\154\x65\x5f\115\141\x70\x70\151\156\147" => "\155\157\137\x6f\160\x74\151\x6f\x6e\163\x5f\x65\156\x75\155\x5f\x72\157\154\145\x5f\x6d\141\x70\160\x69\x6e\x67", "\x54\145\163\x74\x5f\103\157\156\146\x69\x67\165\162\x61\164\151\x6f\156" => "\155\x6f\x5f\x6f\x70\164\x69\x6f\156\x73\x5f\145\x6e\165\155\x5f\164\x65\163\x74\x5f\143\157\156\146\x69\x67\165\x72\141\x74\x69\157\x6e", "\103\x75\x73\x74\157\155\x5f\103\x65\x72\x74\151\x66\151\x63\x61\164\145" => "\x6d\x6f\x5f\157\160\164\151\x6f\156\163\x5f\145\156\x75\155\137\143\x75\x73\x74\x6f\155\137\x63\x65\x72\x74\x69\146\151\x63\x61\164\145", "\103\165\x73\164\157\155\x5f\115\145\x73\x73\141\x67\x65" => "\x6d\x6f\x5f\157\x70\x74\x69\x6f\156\x73\137\x65\x6e\165\x6d\137\143\x75\x73\x74\x6f\x6d\x5f\x6d\x65\163\163\x61\147\145\x73")));
if (defined("\127\120\x5f\125\116\x49\116\123\x54\101\x4c\114\x5f\120\x4c\125\107\111\x4e")) {
    goto yQE;
}
exit;
yQE:
if (!(get_site_option("\155\x6f\137\163\141\x6d\154\x5f\x6b\145\x65\160\x5f\163\x65\164\x74\x69\156\147\x73\137\157\156\137\144\x65\154\x65\164\151\x6f\x6e") !== "\164\162\x75\145")) {
    goto tO4;
}
mo_saml_delete_plugin_configuration();
mo_saml_delete_user_meta();
tO4:
function mo_saml_delete_plugin_configuration()
{
    $jK = maybe_unserialize(Uninstall_Class_Names);
    $hH = array();
    foreach ($jK as $XC => $wE) {
        $hH[$XC] = mo_get_configuration_array($wE, true);
        Fzd:
    }
    ZK4:
    foreach ($hH as $vU => $vw) {
        foreach ($vw as $BS => $ZC) {
            delete_site_option($ZC);
            K9x:
        }
        egH:
        hHu:
    }
    MTV:
}
function mo_saml_delete_user_meta()
{
    $Fj = get_users(array());
    foreach ($Fj as $user) {
        delete_user_meta($user->ID, "\155\157\x5f\163\x61\x6d\x6c\137\x75\x73\145\x72\137\x61\x74\x74\x72\151\142\165\164\145\x73");
        delete_user_meta($user->ID, "\155\157\137\163\x61\155\x6c\x5f\x73\x65\x73\x73\x69\x6f\156\137\151\156\x64\x65\170");
        delete_user_meta($user->ID, "\155\157\137\x73\141\x6d\154\x5f\x6e\141\x6d\x65\137\x69\144");
        vyY:
    }
    ooX:
}
