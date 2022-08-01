<?php
/*
Plugin Name: miniOrange SSO using SAML 2.0
Plugin URI: http://miniorange.com/
Description: (Premium Multi-Site)miniOrange SAML 2.0 SSO enables user to perform Single Sign On with any SAML 2.0 enabled Identity Provider.
Version: 20.0.0
Author: miniOrange
Author URI: http://miniorange.com/
*/


include_once dirname(__FILE__) . "\x2f\x6d\x6f\x5f\154\157\147\151\156\x5f\163\x61\x6d\x6c\x5f\x73\x73\x6f\137\x77\x69\144\x67\145\x74\x2e\160\150\x70";
require "\x6d\157\55\163\141\x6d\154\x2d\x63\x6c\x61\163\163\55\x63\x75\x73\164\157\x6d\x65\x72\x2e\160\x68\x70";
require "\155\x6f\137\163\141\155\154\x5f\163\145\x74\164\x69\156\147\163\137\160\141\x67\145\56\x70\150\x70";
require "\x4d\145\x74\141\144\x61\x74\141\x52\x65\141\x64\x65\x72\56\x70\x68\x70";
require "\143\145\162\164\x69\146\x69\x63\x61\164\145\137\x75\x74\x69\154\x69\164\171\56\160\x68\160";
require "\x6d\x6f\55\163\x61\155\154\55\163\x75\142\x73\x69\x74\145\x2d\141\x63\x63\145\x73\x73\55\144\x65\156\x69\x65\144\55\160\x61\x67\x65\56\160\150\160";
require_once "\x6d\157\x2d\x73\x61\155\154\x2d\x70\x6c\165\x67\x69\x6e\x2d\166\145\x72\163\151\x6f\156\55\x75\x70\x64\141\164\x65\x2e\160\150\160";
include_once "\x78\155\x6c\x73\145\143\154\151\x62\163\x2e\x70\150\160";
include_once "\115\x4f\137\x53\151\164\145\163\137\x4c\151\163\x74\56\160\x68\x70";
use RobRichards\XMLSecLibs\XMLSecurityKey;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecEnc;
class saml_mo_login
{
    function __construct()
    {
        add_site_option("\155\x6f\x5f\x61\x70\x70\x6c\x79\x5f\x72\x6f\x6c\145\x5f\x6d\141\160\160\x69\156\147\137\x66\157\x72\x5f\x73\151\164\145\163", 0);
        add_action("\156\145\164\x77\x6f\162\x6b\x5f\141\x64\x6d\x69\x6e\137\x6d\x65\156\x75", array($this, "\155\x69\156\x69\157\x72\141\156\147\145\x5f\163\163\157\x5f\155\x65\156\x75"));
        add_action("\141\x64\155\x69\x6e\137\x69\x6e\x69\164", array($this, "\155\151\156\151\157\x72\x61\156\147\145\137\154\x6f\x67\151\x6e\137\167\x69\144\x67\x65\164\137\163\x61\155\x6c\x5f\x73\x61\x76\145\137\x73\x65\164\164\x69\156\147\x73"));
        add_action("\x61\144\x6d\151\156\137\145\x6e\161\x75\x65\x75\x65\x5f\x73\143\162\x69\x70\164\x73", array($this, "\160\154\x75\147\x69\156\x5f\163\x65\x74\164\x69\156\147\x73\x5f\x73\164\x79\154\145"));
        register_deactivation_hook(__FILE__, array($this, "\x6d\157\x5f\x73\x73\157\x5f\x73\141\x6d\154\137\x64\x65\x61\x63\x74\x69\166\141\x74\x65"));
        register_activation_hook(__FILE__, array($this, "\x6d\157\x5f\163\x73\x6f\x5f\163\141\x6d\154\137\x61\x63\164\151\166\x61\164\x65"));
        add_action("\x61\144\155\151\x6e\x5f\x65\156\x71\x75\145\x75\x65\137\x73\143\162\x69\x70\164\x73", array($this, "\x70\154\165\x67\151\156\137\163\145\x74\x74\x69\156\x67\163\137\x73\143\x72\151\160\x74"));
        add_action("\154\157\147\151\156\x5f\x65\156\161\x75\x65\x75\145\x5f\163\x63\162\151\x70\164\163", array($this, "\x6d\x6f\137\163\141\x6d\154\x5f\x6c\157\147\151\x6e\137\x65\x6e\x71\x75\145\x75\145\x5f\163\143\x72\x69\160\164\x73"));
        remove_action("\x6e\145\164\x77\x6f\162\153\137\141\144\x6d\x69\156\137\x6e\x6f\x74\151\x63\145\x73", array($this, "\x6d\157\x5f\163\x61\155\x6c\x5f\163\x75\143\x63\145\x73\163\137\155\x65\163\163\141\x67\145"));
        remove_action("\x6e\145\164\x77\x6f\x72\153\x5f\x61\x64\155\151\156\137\x6e\157\164\151\143\145\163", array($this, "\x6d\x6f\x5f\163\x61\155\154\x5f\x65\162\x72\157\162\137\x6d\x65\x73\163\141\x67\x65"));
        add_action("\167\160\137\141\x75\164\150\x65\156\x74\151\143\x61\x74\x65", array($this, "\x6d\157\137\163\x61\155\154\x5f\141\x75\164\x68\x65\156\x74\x69\x63\141\164\x65"));
        add_action("\x77\160", array($this, "\x6d\x6f\x5f\x73\x61\x6d\154\x5f\x61\x75\164\157\137\x72\x65\144\151\x72\145\143\x74"));
        $gr = new mo_login_wid();
        add_filter("\154\157\x67\x6f\x75\164\x5f\162\x65\144\x69\x72\145\143\x74", array($gr, "\155\157\137\163\x61\155\154\137\x6c\x6f\147\157\165\164"), 10, 3);
        add_action("\154\x6f\x67\x69\156\137\x66\x6f\x72\x6d", array($this, "\155\x6f\137\x73\x61\155\154\137\155\x6f\144\151\146\171\137\x6c\x6f\x67\151\156\x5f\146\x6f\x72\x6d"));
        add_shortcode("\x4d\117\x5f\x53\x41\x4d\114\137\x46\x4f\122\x4d", array($this, "\155\x6f\x5f\147\x65\164\137\163\141\155\x6c\x5f\x73\150\157\162\x74\143\157\144\x65"));
        add_filter("\x63\x72\x6f\156\137\163\143\150\x65\x64\x75\x6c\x65\x73", array($this, "\x6d\171\x70\x72\x65\x66\x69\170\x5f\141\144\x64\137\x63\162\157\x6e\x5f\x73\x63\150\x65\x64\x75\154\x65"));
        add_action("\155\x65\164\x61\x64\141\164\x61\137\163\x79\x6e\143\x5f\x63\x72\x6f\x6e\x5f\x61\x63\164\151\x6f\156", array($this, "\x6d\x65\x74\x61\144\x61\164\141\x5f\163\171\x6e\143\x5f\x63\162\x6f\x6e\x5f\x61\x63\164\151\157\156"));
        add_action("\141\x64\x6d\151\156\x5f\151\156\151\x74", array($this, "\144\145\146\141\x75\x6c\164\137\x63\145\x72\x74\151\146\151\x63\x61\164\x65"));
        add_action("\x6e\145\x74\x77\x6f\162\x6b\x5f\x61\x64\155\x69\x6e\137\x70\154\165\147\x69\156\x5f\x61\143\x74\x69\x6f\x6e\137\x6c\x69\156\153\163\137" . plugin_basename(__FILE__), array($this, "\155\157\137\x73\141\x6d\154\x5f\160\154\x75\x67\151\156\137\141\x63\164\151\157\156\x5f\x6c\x69\x6e\153\163"));
        add_filter("\x77\160\155\165\137\165\163\x65\162\x73\x5f\x63\x6f\154\x75\155\156\x73", array($this, "\x6d\x6f\x5f\163\141\155\154\x5f\x63\x75\163\164\157\155\x5f\141\164\x74\162\x5f\x63\x6f\x6c\x75\x6d\x6e"));
        add_filter("\155\x61\x6e\x61\x67\145\x5f\165\x73\145\x72\x73\137\143\165\x73\164\157\155\x5f\143\157\x6c\x75\155\156", array($this, "\x6d\157\137\163\x61\x6d\x6c\137\141\164\x74\162\x5f\x63\x6f\x6c\x75\x6d\x6e\137\143\x6f\x6e\x74\145\x6e\x74"), 10, 3);
        add_action("\141\x64\x6d\x69\156\x5f\x69\156\x69\164", "\155\157\x5f\163\141\155\154\137\144\157\167\x6e\x6c\x6f\141\144");
        add_action("\155\x6f\137\163\x61\155\154\137\146\x6c\165\x73\150\x5f\x63\141\143\x68\145", array($this, "\x6d\157\x5f\163\x61\x6d\154\x5f\146\154\x75\x73\x68\x5f\x63\141\x63\x68\145"), 9999);
        add_action("\x64\x65\x6c\x65\x74\x65\137\x75\x73\x65\162\x5f\x66\x6f\x72\x6d", array($this, "\155\x6f\x5f\144\x65\154\x65\x74\145\x5f\x75\x73\145\162\163"), 10, 2);
    }
    function mo_delete_users($current_user, $m1)
    {
        if (count($m1) > 1) {
            goto hi;
        }
        $cS = false;
        goto GG;
        hi:
        $cS = true;
        GG:
        foreach ($m1 as $v2) {
            $user = get_user_by("\151\144", $v2);
            $this->mo_delete_user($user, $m1, $cS);
            jp:
        }
        rd:
    }
    function mo_delete_user($P4, $m1, $cS)
    {
        $G8 = get_users();
        $u6 = $P4->ID;
        $HS = "\74\x6c\x61\142\x65\x6c\40\x66\157\x72\75\x22\x72\145\141\163\163\151\147\x6e\x5f\165\x73\x65\x72\42\40\143\x6c\141\x73\x73\x3d\42\x73\x63\x72\145\x65\156\55\x72\x65\x61\144\x65\162\55\164\x65\170\164\x22\x3e" . __("\x53\x65\154\145\143\x74\x20\141\40\x75\163\x65\162") . "\x3c\x2f\x6c\x61\142\145\x6c\76";
        $HS .= "\x3c\x73\145\x6c\145\x63\x74\40\156\141\155\145\x3d\42\141\154\154\137\x62\x6c\157\147\x73\42\40\x69\x64\75\42\x72\x65\141\x73\163\151\147\x6e\137\165\163\145\162\x5f\141\154\x6c\x22\40\157\156\143\150\x61\156\x67\x65\75\42\162\x65\101\x73\163\x69\147\x6e\124\x6f\x55\163\145\x72\x28\x74\150\x69\163\x2c\x20" . $u6 . "\51\42\76";
        $nN = '';
        foreach ($G8 as $user) {
            if (in_array($user->ID, $m1)) {
                goto T_;
            }
            $nN .= "\74\157\x70\164\x69\x6f\x6e\40\x76\x61\154\x75\x65\x3d\47{$user->ID}\47\x3e{$user->user_login}\x3c\57\157\160\164\x69\157\156\76";
            T_:
            R9:
        }
        ou:
        $HS .= $nN;
        $HS .= "\74\x2f\163\145\154\145\x63\164\x3e\12";
        ?>
        <table class="form-table" role="presentation">

            <tr><th scope="row"><?php
/*
Plugin Name: miniOrange SSO using SAML 2.0
Plugin URI: http://miniorange.com/
Description: (Premium Multi-Site)miniOrange SAML 2.0 SSO enables user to perform Single Sign On with any SAML 2.0 enabled Identity Provider.
Version: 20.0.0
Author: miniOrange
Author URI: http://miniorange.com/
*/ 
        if (!$cS) {
            goto bn;
        }
        echo $P4->user_login;
        bn:
        ?>
 </th><td>
                    <ul style="list-style:none;">
                        <li>
                            <?php
/*
Plugin Name: miniOrange SSO using SAML 2.0
Plugin URI: http://miniorange.com/
Description: (Premium Multi-Site)miniOrange SAML 2.0 SSO enables user to perform Single Sign On with any SAML 2.0 enabled Identity Provider.
Version: 20.0.0
Author: miniOrange
Author URI: http://miniorange.com/
*/ 
        printf(__("\74\x62\76\x53\151\164\x65\x3a\40\x25\x73\x3c\x2f\x62\x3e"), "\101\154\154\x20\123\x69\164\145\163");
        ?>
                        </li>
                        <li><label><input type="radio" id="delete_option0All" name="deleteAll<?php
/*
Plugin Name: miniOrange SSO using SAML 2.0
Plugin URI: http://miniorange.com/
Description: (Premium Multi-Site)miniOrange SAML 2.0 SSO enables user to perform Single Sign On with any SAML 2.0 enabled Identity Provider.
Version: 20.0.0
Author: miniOrange
Author URI: http://miniorange.com/
*/ 
        echo $u6;
        ?>
" value="delete" checked="checked" onchange="deleteAlldata('<?php
/*
Plugin Name: miniOrange SSO using SAML 2.0
Plugin URI: http://miniorange.com/
Description: (Premium Multi-Site)miniOrange SAML 2.0 SSO enables user to perform Single Sign On with any SAML 2.0 enabled Identity Provider.
Version: 20.0.0
Author: miniOrange
Author URI: http://miniorange.com/
*/ 
        echo $u6;
        ?>
')" />
                                <?php
/*
Plugin Name: miniOrange SSO using SAML 2.0
Plugin URI: http://miniorange.com/
Description: (Premium Multi-Site)miniOrange SAML 2.0 SSO enables user to perform Single Sign On with any SAML 2.0 enabled Identity Provider.
Version: 20.0.0
Author: miniOrange
Author URI: http://miniorange.com/
*/ 
        _e("\104\145\x6c\145\164\x65\x20\x61\154\154\40\143\x6f\x6e\164\x65\156\x74\56");
        ?>
</label></li>
                        <li><label><input type="radio" id="delete_option1All" name="deleteAll<?php
/*
Plugin Name: miniOrange SSO using SAML 2.0
Plugin URI: http://miniorange.com/
Description: (Premium Multi-Site)miniOrange SAML 2.0 SSO enables user to perform Single Sign On with any SAML 2.0 enabled Identity Provider.
Version: 20.0.0
Author: miniOrange
Author URI: http://miniorange.com/
*/ 
        echo $u6;
        ?>
" value="reassign" onchange="assignAlldata('<?php
/*
Plugin Name: miniOrange SSO using SAML 2.0
Plugin URI: http://miniorange.com/
Description: (Premium Multi-Site)miniOrange SAML 2.0 SSO enables user to perform Single Sign On with any SAML 2.0 enabled Identity Provider.
Version: 20.0.0
Author: miniOrange
Author URI: http://miniorange.com/
*/ 
        echo $u6;
        ?>
')"/>
                                <?php
/*
Plugin Name: miniOrange SSO using SAML 2.0
Plugin URI: http://miniorange.com/
Description: (Premium Multi-Site)miniOrange SAML 2.0 SSO enables user to perform Single Sign On with any SAML 2.0 enabled Identity Provider.
Version: 20.0.0
Author: miniOrange
Author URI: http://miniorange.com/
*/ 
        _e("\x41\x74\164\162\151\x62\x75\x74\x65\x20\141\154\x6c\40\143\x6f\156\x74\x65\156\x74\40\x74\157\x3a");
        ?>
</label>
                            <?php
/*
Plugin Name: miniOrange SSO using SAML 2.0
Plugin URI: http://miniorange.com/
Description: (Premium Multi-Site)miniOrange SAML 2.0 SSO enables user to perform Single Sign On with any SAML 2.0 enabled Identity Provider.
Version: 20.0.0
Author: miniOrange
Author URI: http://miniorange.com/
*/ 
        echo $HS;
        ?>
</li>
                        <li><span id="mo_delete_user_notice<?php
/*
Plugin Name: miniOrange SSO using SAML 2.0
Plugin URI: http://miniorange.com/
Description: (Premium Multi-Site)miniOrange SAML 2.0 SSO enables user to perform Single Sign On with any SAML 2.0 enabled Identity Provider.
Version: 20.0.0
Author: miniOrange
Author URI: http://miniorange.com/
*/ 
        echo $u6;
        ?>
" style="visibility: hidden"><br/><b>NOTE: </b>The selected user doesn't exist in the following subsites. Kindly attribute the content for the following subsites manually or select a different user.</span></li>
                        <ol id="mo_subsite_list<?php
/*
Plugin Name: miniOrange SSO using SAML 2.0
Plugin URI: http://miniorange.com/
Description: (Premium Multi-Site)miniOrange SAML 2.0 SSO enables user to perform Single Sign On with any SAML 2.0 enabled Identity Provider.
Version: 20.0.0
Author: miniOrange
Author URI: http://miniorange.com/
*/ 
        echo $u6;
        ?>
" style="visibility: hidden">
                        </ol>
                    </ul>
                </td></tr>
        </table>

        <script>

            function deleteAlldata(id){
                var elems = document.querySelectorAll("[name^='delete'][name$='[" + id + "]']");
                for(elem of elems) {
                    if(elem.value == "delete")
                        elem.checked = true;
                }
            }

            function assignAlldata(id){
                var elems = document.querySelectorAll("[name^='delete'][name$='[" + id + "]']");
                for(elem of elems) {
                    if(elem.value == "reassign")
                        elem.checked = true;
                }
            }

            function reAssignToUser(userSelect, userid){
                flushExistingSubsiteList(userid);
                var elems = document.querySelectorAll("[name^='blog[" + userid + "]']");
                var allSites = true;
                for(elem of elems) {
                    if(isOptionExist(elem, userSelect.value))
                        elem.value = userSelect.value;
                    else {
                        showMoDeleteNotice(elem, userid);
                        allSites = false;
                    }

                }

                if(allSites) {
                    document.getElementById("mo_delete_user_notice" + userid).style.visibility = "hidden";
                    document.getElementById("mo_subsite_list" + userid).style.visibility = "hidden";
                }
            }

            function isOptionExist(select, value){

                for(i = 0; i < select.length; i++){
                    if(select.options[i].value == value)
                        return true;
                }
                return false;
            }

            function showMoDeleteNotice(elem, userid){
                document.getElementById("mo_delete_user_notice" + userid).style.visibility = "visible";
                document.getElementById("mo_subsite_list" + userid).style.visibility = "visible";
                url = getSubsiteWhereUserDoesNotExist(elem);
                name = getSubsiteNameWhereUserDoesNotExist(elem);
                addUrlToList(url, name, userid);
            }

            function getSubsiteWhereUserDoesNotExist(element){
                subsiteUrl = element.parentElement.parentElement.firstElementChild.firstElementChild.getAttribute("href");
                return subsiteUrl;
            }

            function getSubsiteNameWhereUserDoesNotExist(element){
                subsiteName = element.parentElement.parentElement.firstElementChild.firstElementChild.innerText;
                return subsiteName;
            }

            function addUrlToList(url, name, userid){
                var olElem = document.getElementById("mo_subsite_list" + userid);
                var li = document.createElement("li");
                li.appendChild(document.createTextNode(url +  "   -   " + name));
                li.setAttribute("id", url);
                olElem.appendChild(li);
            }

            function flushExistingSubsiteList(userid){
                var olElem = document.getElementById("mo_subsite_list" + userid);
                olElem.innerHTML = "";
            }


        </script>

        <?php
/*
Plugin Name: miniOrange SSO using SAML 2.0
Plugin URI: http://miniorange.com/
Description: (Premium Multi-Site)miniOrange SAML 2.0 SSO enables user to perform Single Sign On with any SAML 2.0 enabled Identity Provider.
Version: 20.0.0
Author: miniOrange
Author URI: http://miniorange.com/
*/ 
    }
    function mo_saml_flush_cache()
    {
        if (!(mo_saml_is_customer_registered_saml() && get_site_option("\163\155\x6c\137\154\153"))) {
            goto py;
        }
        $EF = new Customersaml();
        $EF->mo_saml_update_status($this);
        py:
    }
    function default_certificate()
    {
        $Go = file_get_contents(plugin_dir_path(__FILE__) . "\x72\145\x73\x6f\x75\162\143\x65\163" . DIRECTORY_SEPARATOR . "\163\x70\x2d\x63\x65\x72\164\x69\x66\151\x63\141\164\145\x2e\x63\x72\x74");
        $Dh = file_get_contents(plugin_dir_path(__FILE__) . "\x72\145\x73\157\165\x72\x63\145\163" . DIRECTORY_SEPARATOR . "\x73\160\x2d\153\x65\171\x2e\x6b\145\171");
        add_site_option("\155\x6f\x5f\x73\141\x6d\x6c\137\x63\145\x72\x74", $Go);
        add_site_option("\155\x6f\x5f\163\x61\155\x6c\x5f\143\x65\162\164\137\x70\x72\151\166\141\x74\x65\x5f\x6b\x65\171", $Dh);
        if (!(!get_site_option("\x6d\157\137\163\141\155\154\137\143\165\162\x72\x65\156\164\x5f\143\x65\162\164") && !get_site_option("\x6d\157\137\x73\141\x6d\154\x5f\x63\x75\x72\162\145\x6e\x74\x5f\143\x65\x72\164\x5f\160\x72\x69\x76\x61\x74\x65\137\153\x65\x79"))) {
            goto H6;
        }
        update_site_option("\155\157\x5f\x73\x61\x6d\x6c\x5f\143\x75\x72\x72\145\x6e\164\137\x63\x65\162\164", $Go);
        update_site_option("\155\x6f\x5f\x73\141\x6d\154\x5f\143\165\x72\x72\145\x6e\x74\x5f\x63\x65\162\x74\x5f\160\162\x69\x76\141\164\x65\x5f\x6b\145\171", $Dh);
        H6:
    }
    function myprefix_add_cron_schedule($Ty)
    {
        $Ty["\x77\145\x65\153\154\x79"] = array("\151\156\164\145\162\166\x61\154" => 604800, "\x64\x69\x73\160\x6c\141\x79" => __("\117\x6e\143\x65\40\127\x65\145\x6b\x6c\171"));
        $Ty["\x6d\157\x6e\x74\150\x6c\x79"] = array("\x69\x6e\164\145\162\x76\141\x6c" => 2635200, "\144\x69\x73\x70\x6c\141\171" => __("\x4f\x6e\143\x65\x20\x4d\157\156\x74\150\154\171"));
        return $Ty;
    }
    function metadata_sync_cron_action()
    {
        error_log("\x6d\151\156\151\157\x72\x61\x6e\147\x65\x20\72\x20\122\x41\x4e\40\123\131\x4e\103\40\x2d\x20" . time());
        $uh = get_site_option("\163\x61\x6d\154\x5f\x69\x64\145\x6e\x74\x69\164\x79\x5f\156\141\x6d\145");
        $this->upload_metadata(@file_get_contents(get_site_option("\163\x61\155\154\x5f\x6d\145\x74\141\144\141\164\x61\137\165\x72\x6c\137\146\x6f\x72\x5f\163\171\x6e\x63")));
        update_site_option("\163\141\155\154\x5f\151\x64\145\x6e\x74\151\164\171\x5f\156\x61\155\x65", $uh);
    }
    function mo_login_widget_saml_options()
    {
        global $wpdb;
        update_site_option("\x6d\157\x5f\163\141\155\x6c\x5f\x68\157\x73\x74\x5f\x6e\141\155\145", "\x68\164\164\160\x73\x3a\57\x2f\x6c\157\x67\x69\x6e\x2e\x78\145\x63\x75\x72\x69\146\171\x2e\143\157\155");
        mo_register_saml_sso();
    }
    function mo_saml_success_message()
    {
        $Bq = "\x65\x72\162\157\162";
        $bf = get_site_option("\155\157\x5f\163\x61\x6d\154\x5f\155\x65\x73\x73\x61\147\145");
        echo "\74\144\x69\166\40\x63\154\x61\163\x73\x3d\47" . $Bq . "\47\76\40\74\160\x3e" . $bf . "\x3c\57\x70\76\74\57\x64\x69\x76\x3e";
    }
    function mo_saml_error_message()
    {
        $Bq = "\x75\160\144\x61\x74\145\144";
        $bf = get_site_option("\x6d\157\x5f\x73\141\155\x6c\137\155\145\x73\163\x61\147\145");
        echo "\74\x64\151\166\x20\143\x6c\141\163\x73\x3d\47" . $Bq . "\x27\76\40\x3c\160\76" . $bf . "\x3c\57\x70\x3e\x3c\x2f\x64\x69\166\76";
    }
    public function mo_sso_saml_deactivate()
    {
        do_action("\155\x6f\137\163\x61\155\x6c\137\x66\x6c\x75\x73\x68\x5f\x63\141\x63\x68\145");
        delete_site_option("\x6d\157\x5f\x73\141\155\154\x5f\x68\x6f\x73\x74\137\x6e\x61\155\145");
        delete_site_option("\x6d\157\x5f\x73\x61\155\x6c\x5f\156\x65\x77\x5f\x72\x65\x67\151\x73\164\162\141\x74\151\x6f\156");
        delete_site_option("\x6d\157\x5f\163\141\155\x6c\137\141\144\x6d\x69\x6e\137\160\x68\157\x6e\145");
        delete_site_option("\x6d\157\137\x73\x61\x6d\154\137\x61\144\155\x69\x6e\137\x70\141\x73\x73\x77\x6f\162\x64");
        delete_site_option("\155\x6f\137\163\x61\x6d\154\x5f\x76\x65\162\151\146\x79\137\x63\x75\163\164\157\x6d\145\x72");
        delete_site_option("\155\157\137\163\x61\155\154\137\141\x64\155\x69\156\137\143\165\163\164\157\x6d\x65\162\x5f\153\x65\x79");
        delete_site_option("\155\157\x5f\163\x61\155\x6c\137\141\x64\x6d\x69\156\x5f\x61\x70\151\x5f\x6b\x65\171");
        delete_site_option("\x6d\157\x5f\163\x61\155\x6c\x5f\143\x75\x73\x74\157\155\x65\162\x5f\x74\157\153\x65\x6e");
        delete_site_option("\x6d\x6f\137\x73\x61\x6d\154\137\x6d\145\163\x73\141\x67\145");
        delete_site_option("\155\x6f\x5f\163\x61\155\154\137\x72\x65\x67\151\163\x74\x72\x61\x74\151\157\x6e\x5f\x73\x74\141\x74\x75\x73");
        delete_site_option("\155\x6f\137\x73\x61\155\154\x5f\151\144\x70\137\143\x6f\156\146\x69\147\137\143\x6f\155\160\154\145\164\145");
        delete_site_option("\x6d\157\x5f\163\x61\155\154\x5f\164\162\x61\156\x73\141\x63\x74\x69\x6f\x6e\111\144");
        delete_site_option("\x6d\157\x5f\163\x61\x6d\x6c\x5f\143\x65\x72\x74");
        delete_site_option("\x6d\157\137\x73\141\155\x6c\x5f\x63\x65\x72\164\x5f\x70\x72\151\x76\141\x74\x65\x5f\x6b\x65\171");
        delete_site_option("\155\x6f\137\163\x61\155\154\137\x63\165\x72\x72\x65\156\x74\137\x63\x65\162\164");
        delete_site_option("\155\x6f\137\163\141\155\154\x5f\x63\x75\x72\x72\145\x6e\x74\x5f\x63\145\162\x74\x5f\x70\x72\151\166\141\x74\x65\137\153\x65\x79");
        delete_site_option("\x6d\157\137\163\x61\155\x6c\137\x65\x6e\141\x62\x6c\x65\137\143\x6c\x6f\x75\144\x5f\x62\x72\157\x6b\145\x72");
    }
    public function mo_sso_saml_activate()
    {
        if (is_multisite()) {
            goto Ah;
        }
        echo "\125\x6e\141\x62\x6c\x65\x20\x74\x6f\40\141\x63\164\x69\x76\141\164\145\x20\x74\150\x65\x20\160\154\x75\147\151\156\56\40\x49\x74\40\163\x65\x65\155\163\x20\154\151\153\x65\40\171\x6f\165\x20\x61\x72\145\40\164\x72\171\x69\156\147\40\164\x6f\x20\x69\156\163\164\141\154\x6c\40\164\x68\x65\40\x3c\x62\x3e\x6d\x75\154\164\x69\163\x69\164\x65\x20\x70\154\165\147\x69\x6e\74\57\x62\76\40\x69\156\40\164\x68\x65\x20\163\x69\156\x67\154\x65\55\163\x69\164\x65\x20\145\x6e\x76\151\162\157\x6e\x6d\x65\156\164\x2e\40\120\154\x65\x61\163\145\x20\x63\x6f\x6e\x74\x61\x63\x74\40\74\142\x3e\x69\156\146\x6f\x40\x6d\x69\x6e\151\x6f\162\x61\156\x67\x65\56\143\157\x6d\x3c\x2f\x62\x3e\x20\x66\x6f\x72\x20\155\x6f\x72\x65\40\144\x65\164\141\151\x6c\x73";
        die;
        Ah:
        if (mo_saml_is_extension_installed("\x6f\160\x65\156\x73\x73\x6c")) {
            goto bI;
        }
        wp_die("\120\110\x50\40\x6f\160\145\156\x73\163\154\40\x65\x78\x74\145\156\x73\x69\157\x6e\40\x69\x73\40\x6e\157\x74\40\151\x6e\163\x74\141\x6c\154\145\x64\x20\157\162\x20\x64\x69\x73\x61\x62\154\145\x64\x2c\160\154\145\141\163\145\40\145\x6e\x61\142\154\x65\x20\151\164\40\x74\157\40\x61\x63\164\151\x76\141\164\x65\x20\x74\x68\145\40\160\154\x75\147\x69\x6e\x2e");
        bI:
        add_option("\101\x63\164\151\166\x61\x74\145\144\137\120\x6c\165\x67\151\156", "\120\x6c\x75\147\x69\156\x2d\x53\x6c\165\147");
        $this->mo_saml_migrate_configuration();
    }
    function mo_saml_migrate_configuration()
    {
        $pK = get_site_option("\x73\141\155\154\x5f\163\163\x6f\137\x73\x65\x74\164\151\156\147\163");
        if (empty($pK)) {
            goto h0;
        }
        return;
        h0:
        $pK["\x44\x45\106\101\x55\114\124"] = array();
        $pK["\x44\105\106\101\125\114\x54"]["\x6d\x6f\137\x73\141\x6d\154\137\x72\145\154\x61\171\137\x73\164\141\x74\x65"] = !empty(get_site_option("\x6d\157\137\x73\141\x6d\x6c\137\x72\145\x6c\141\x79\x5f\x73\x74\x61\164\145")) ? get_site_option("\155\x6f\137\x73\141\155\154\x5f\x72\x65\x6c\x61\171\137\163\x74\x61\x74\145") : '';
        $pK["\x44\105\x46\x41\x55\x4c\x54"]["\155\x6f\x5f\163\x61\x6d\x6c\x5f\x72\145\147\151\x73\164\145\162\x65\x64\137\157\156\154\x79\x5f\x61\x63\x63\x65\163\x73"] = !empty(get_site_option("\155\x6f\137\x73\x61\x6d\154\137\162\x65\147\x69\x73\x74\x65\162\145\144\x5f\x6f\x6e\154\x79\137\141\x63\x63\x65\x73\163")) ? get_site_option("\x6d\x6f\137\x73\141\x6d\x6c\137\162\145\147\151\163\x74\145\162\145\144\137\x6f\156\154\171\x5f\141\143\143\145\x73\163") : '';
        $pK["\104\105\x46\101\x55\x4c\124"]["\x6d\x6f\x5f\x73\x61\x6d\x6c\x5f\x66\157\162\143\145\137\141\165\x74\x68\145\156\164\x69\x63\141\x74\x69\x6f\x6e"] = !empty(get_site_option("\155\x6f\137\163\141\155\154\x5f\146\x6f\x72\143\x65\137\141\x75\x74\x68\x65\x6e\164\151\x63\141\x74\151\x6f\156")) ? get_site_option("\x6d\x6f\137\163\141\x6d\x6c\x5f\x66\157\x72\143\145\137\x61\165\164\150\145\x6e\x74\151\143\x61\164\151\157\156") : '';
        $pK["\x44\x45\106\x41\x55\x4c\x54"]["\155\157\x5f\163\141\x6d\x6c\137\x65\x6e\x61\142\154\x65\x5f\154\x6f\147\151\x6e\137\x72\x65\144\x69\162\145\143\164"] = !empty(get_site_option("\155\157\x5f\163\141\x6d\154\137\x65\x6e\x61\x62\154\x65\x5f\154\x6f\x67\x69\x6e\x5f\162\x65\x64\x69\x72\x65\x63\x74")) ? get_site_option("\155\157\x5f\x73\x61\x6d\x6c\x5f\x65\x6e\141\x62\154\x65\x5f\x6c\x6f\x67\x69\x6e\137\162\x65\144\151\x72\145\143\164") : '';
        $pK["\104\105\106\x41\125\114\x54"]["\155\x6f\137\x73\141\155\x6c\137\x61\154\154\x6f\x77\137\167\x70\x5f\163\x69\x67\x6e\151\x6e"] = !empty(get_site_option("\155\x6f\x5f\x73\141\x6d\154\137\x61\x6c\154\157\x77\x5f\167\160\137\x73\x69\x67\x6e\x69\x6e")) ? get_site_option("\155\157\x5f\163\x61\x6d\x6c\137\141\154\x6c\157\x77\137\x77\160\x5f\x73\151\x67\x6e\x69\156") : '';
        delete_site_option("\163\x61\x6d\x6c\137\x72\145\161\165\145\163\164\137\163\x69\147\156\145\x64");
        update_site_option("\x73\141\155\x6c\137\x73\163\x6f\137\163\145\164\164\151\156\x67\x73", $pK);
    }
    function mo_saml_show_success_message()
    {
        remove_action("\x6e\x65\x74\x77\157\162\153\137\x61\x64\x6d\x69\156\137\156\157\164\151\x63\145\x73", array($this, "\155\x6f\137\163\x61\x6d\154\x5f\163\x75\x63\x63\x65\x73\x73\137\x6d\x65\x73\163\x61\x67\x65"));
        add_action("\x6e\x65\164\167\157\162\153\x5f\141\x64\155\151\156\x5f\x6e\x6f\164\x69\143\145\x73", array($this, "\x6d\x6f\x5f\163\141\x6d\154\x5f\145\x72\x72\157\x72\137\x6d\x65\163\163\141\x67\145"));
    }
    function mo_saml_show_error_message()
    {
        remove_action("\156\x65\164\167\x6f\162\153\137\x61\x64\155\151\156\x5f\156\x6f\164\151\x63\x65\x73", array($this, "\x6d\157\x5f\x73\x61\x6d\x6c\x5f\x65\x72\162\157\162\x5f\x6d\x65\x73\163\x61\147\145"));
        add_action("\156\145\164\167\157\162\x6b\x5f\x61\x64\155\x69\x6e\x5f\156\x6f\x74\x69\143\145\x73", array($this, "\x6d\157\137\163\141\155\x6c\137\x73\x75\x63\143\145\163\x73\137\155\145\163\163\141\147\145"));
    }
    function plugin_settings_style($yp)
    {
        if (!("\164\x6f\x70\154\145\166\145\x6c\137\x70\141\x67\145\137\x6d\x6f\x5f\x73\141\155\154\x5f\x73\x65\164\x74\151\156\147\x73" != $yp)) {
            goto h8;
        }
        return;
        h8:
        if (!(isset($_REQUEST["\x74\x61\x62"]) && $_REQUEST["\164\141\142"] == "\154\x69\x63\x65\x6e\x73\x69\x6e\147")) {
            goto PN;
        }
        wp_enqueue_style("\x6d\157\x5f\x73\x61\155\x6c\x5f\x62\157\x6f\x74\x73\164\162\x61\x70\137\143\x73\163", plugins_url("\151\x6e\143\154\x75\144\x65\163\57\x63\163\163\57\x62\x6f\157\x74\163\164\x72\x61\x70\57\x62\157\157\164\x73\x74\162\141\160\x2e\155\x69\x6e\x2e\143\163\163", __FILE__), array(), "\x31\62\56\x31\x38\56\x33", "\x61\x6c\x6c");
        PN:
        wp_enqueue_style("\155\x6f\x5f\x73\x61\155\x6c\137\x61\x64\x6d\151\156\137\163\x65\164\x74\151\x6e\147\x73\x5f\x73\x74\x79\154\145\x5f\164\162\x61\x63\x6b\x65\x72", plugins_url("\151\156\x63\154\x75\144\x65\x73\57\x63\163\163\x2f\x70\162\x6f\x67\162\145\163\163\x2d\164\x72\x61\x63\153\145\x72\x2e\143\x73\163", __FILE__), array(), "\61\62\x2e\61\x38\56\63", "\141\x6c\x6c");
        wp_enqueue_style("\x6d\x6f\137\163\x61\x6d\154\x5f\141\144\x6d\x69\156\137\163\x65\164\164\151\156\147\x73\137\163\164\x79\154\x65", plugins_url("\151\156\x63\x6c\165\x64\145\x73\57\x63\163\x73\57\163\164\171\x6c\x65\x5f\x73\145\x74\164\x69\x6e\147\163\56\x63\163\x73\77\x76\x65\162\x3d\63\56\x36\x2e\62", __FILE__), array(), "\61\x32\x2e\61\x38\x2e\63", "\141\x6c\x6c");
        wp_enqueue_style("\x6d\x6f\x5f\x73\141\155\x6c\x5f\141\144\155\151\156\137\163\145\164\164\x69\x6e\147\163\137\x70\x68\157\x6e\x65\x5f\163\164\x79\x6c\145", plugins_url("\x69\x6e\143\x6c\x75\144\x65\x73\57\x63\163\163\57\x70\x68\157\156\145\56\143\x73\163", __FILE__), array(), "\x31\62\56\x31\x38\x2e\x33", "\141\154\154");
        wp_enqueue_style("\155\x6f\137\163\141\155\154\x5f\167\x70\x62\55\x66\x61", plugins_url("\151\156\x63\x6c\x75\144\145\163\57\x63\x73\163\57\146\x6f\x6e\x74\55\141\167\145\x73\x6f\x6d\145\x2e\x6d\151\156\56\x63\x73\163", __FILE__), array(), "\61\x32\56\61\x38\x2e\x33", "\x61\154\154");
        wp_enqueue_style("\x6d\x6f\x5f\163\141\x6d\x6c\x5f\x61\x64\155\x69\156\x5f\x73\x65\x74\164\x69\156\x67\163\137\x73\x74\x79\154\145", plugins_url("\151\x6e\x63\154\165\x64\x65\163\x2f\143\163\x73\x2f\152\x71\165\x65\162\x79\56\x75\151\x2e\x63\163\163", __FILE__), array(), "\x31\x32\x2e\61\70\56\63", "\x61\x6c\154");
    }
    function plugin_settings_script($yp)
    {
        if (!("\x74\157\x70\x6c\x65\x76\x65\154\x5f\x70\141\x67\145\137\155\157\x5f\163\x61\x6d\154\137\163\145\x74\164\x69\156\x67\x73" != $yp)) {
            goto li;
        }
        return;
        li:
        wp_enqueue_script("\x6a\161\165\145\162\171");
        wp_enqueue_script("\x6d\157\x5f\163\x61\x6d\154\x5f\x61\x64\155\x69\x6e\137\163\x65\x74\164\x69\x6e\x67\x73\137\x73\x63\x72\151\x70\164", plugins_url("\x69\156\143\154\x75\x64\x65\x73\57\x6a\x73\x2f\163\145\164\164\x69\156\147\163\x2e\x6a\163", __FILE__), array(), "\x31\62\56\x31\x38\56\63", false);
        wp_enqueue_script("\155\x6f\x5f\163\141\155\x6c\x5f\141\x64\x6d\x69\156\x5f\163\x65\x74\x74\151\x6e\147\x73\x5f\160\x68\157\x6e\145\x5f\x73\x63\162\151\160\164", plugins_url("\151\156\x63\154\165\x64\x65\163\57\152\x73\57\160\150\x6f\x6e\145\56\x6a\x73", __FILE__), array(), "\x31\x32\56\61\x38\x2e\x33", false);
        if (!(isset($_REQUEST["\164\141\x62"]) && $_REQUEST["\x74\x61\142"] == "\154\x69\143\x65\x6e\163\x69\156\147")) {
            goto cK;
        }
        wp_enqueue_script("\155\157\137\x73\x61\155\x6c\137\155\157\x64\x65\x72\x6e\x69\172\x72\x5f\163\x63\162\x69\160\x74", plugins_url("\151\x6e\143\154\165\x64\x65\x73\57\152\x73\x2f\155\x6f\144\x65\x72\156\151\x7a\162\x2e\152\163", __FILE__), array(), "\x31\x32\x2e\61\70\56\x33", false);
        wp_enqueue_script("\155\x6f\137\x73\x61\x6d\154\x5f\x70\157\160\157\x76\x65\162\x5f\163\x63\162\151\x70\x74", plugins_url("\151\x6e\x63\154\x75\x64\x65\163\57\x6a\x73\57\142\157\157\164\x73\164\x72\x61\x70\57\x70\x6f\x70\x70\x65\162\56\155\x69\x6e\x2e\x6a\163", __FILE__), array(), "\61\62\56\x31\70\56\x33", false);
        wp_enqueue_script("\x6d\x6f\x5f\x73\x61\155\154\x5f\142\x6f\x6f\x74\163\164\162\141\x70\137\163\143\x72\151\x70\164", plugins_url("\151\156\143\x6c\165\x64\x65\x73\x2f\152\163\57\x62\x6f\157\x74\163\164\x72\141\160\x2f\142\x6f\157\164\163\x74\x72\x61\160\56\155\x69\x6e\x2e\x6a\163", __FILE__), array(), "\x31\62\56\x31\x38\56\x33", false);
        cK:
    }
    function mo_saml_login_enqueue_scripts()
    {
        wp_enqueue_script("\152\161\165\x65\x72\171");
    }
    function mo_saml_activation_message()
    {
        $Bq = "\x75\x70\144\141\x74\x65\144";
        $bf = get_site_option("\155\157\137\x73\x61\x6d\154\137\x6d\x65\x73\163\141\147\x65");
        echo "\74\x64\x69\x76\x20\143\154\x61\x73\x73\75\x27" . $Bq . "\47\x3e\40\74\160\x3e" . $bf . "\x3c\57\x70\x3e\x3c\57\x64\x69\x76\x3e";
    }
    function mo_saml_custom_attr_column($pp)
    {
        $Ve = maybe_unserialize(get_site_option("\155\157\x5f\x73\x61\155\154\x5f\143\x75\163\x74\x6f\x6d\x5f\141\x74\x74\x72\x73\137\x6d\x61\x70\x70\151\156\147"));
        $jB = get_site_option("\163\141\x6d\x6c\137\x73\x68\x6f\x77\137\x75\x73\x65\x72\x5f\x61\164\x74\162\151\142\x75\164\145");
        $cu = 0;
        if (!is_array($Ve)) {
            goto J2;
        }
        foreach ($Ve as $Z1 => $zF) {
            if (empty($Z1)) {
                goto L3;
            }
            if (!in_array($cu, $jB)) {
                goto kq;
            }
            $pp[$Z1] = $Z1;
            kq:
            L3:
            $cu++;
            Ic:
        }
        Gl:
        J2:
        return $pp;
    }
    function mo_saml_attr_column_content($Cu, $aL, $qU)
    {
        $Ve = maybe_unserialize(get_site_option("\155\157\x5f\163\141\155\154\137\143\x75\163\164\x6f\x6d\x5f\141\x74\x74\162\x73\137\x6d\x61\160\x70\x69\156\147"));
        if (!is_array($Ve)) {
            goto HP;
        }
        foreach ($Ve as $Z1 => $zF) {
            if (!($Z1 === $aL)) {
                goto qb;
            }
            $Zg = get_user_meta($qU, $aL, false);
            if (empty($Zg)) {
                goto Lq;
            }
            if (!is_array($Zg[0])) {
                goto Zw;
            }
            $ZG = '';
            foreach ($Zg[0] as $kx) {
                $ZG = $ZG . $kx;
                if (!next($Zg[0])) {
                    goto bD;
                }
                $ZG = $ZG . "\40\174\x20";
                bD:
                W8:
            }
            FD:
            return $ZG;
            goto iy;
            Zw:
            return $Zg[0];
            iy:
            Lq:
            qb:
            gO:
        }
        OI:
        HP:
        return $Cu;
    }
    static function mo_check_option_admin_referer($jd)
    {
        return isset($_POST["\x6f\x70\164\151\x6f\156"]) and $_POST["\157\x70\164\x69\157\x6e"] == $jd and check_admin_referer($jd);
    }
    function miniorange_login_widget_saml_save_settings()
    {
        if (!current_user_can("\x6d\x61\x6e\x61\147\x65\137\x6f\x70\x74\x69\x6f\x6e\x73")) {
            goto Wo;
        }
        if (!(is_admin() && get_option("\101\143\x74\151\166\141\x74\x65\144\137\x50\x6c\x75\x67\x69\156") == "\x50\154\x75\147\x69\x6e\55\x53\x6c\165\147")) {
            goto GQ;
        }
        delete_option("\x41\x63\164\151\x76\x61\x74\145\x64\x5f\120\x6c\x75\x67\151\x6e");
        update_site_option("\155\x6f\x5f\x73\141\x6d\x6c\137\x6d\x65\163\x73\141\147\x65", "\107\157\40\x74\x6f\40\x70\x6c\x75\x67\x69\x6e\x20\x3c\142\76\x3c\x61\x20\x68\162\145\146\75\42\x61\144\x6d\x69\x6e\x2e\160\150\x70\77\160\x61\147\145\x3d\x6d\157\x5f\x73\x61\155\x6c\x5f\x73\145\x74\164\151\156\x67\x73\x22\76\x73\145\164\164\151\x6e\x67\x73\74\x2f\x61\76\x3c\57\142\x3e\40\x74\157\40\x63\x6f\x6e\146\151\x67\165\x72\x65\x20\x53\101\115\x4c\40\x53\151\156\147\154\x65\x20\x53\151\x67\156\40\x4f\x6e\x20\x62\171\40\x6d\151\x6e\151\117\162\141\x6e\x67\145\56");
        add_action("\x6e\x65\x74\167\157\x72\153\137\141\144\155\x69\x6e\x5f\156\x6f\x74\x69\143\145\x73", array($this, "\155\157\137\x73\141\x6d\x6c\x5f\141\143\x74\x69\166\x61\164\151\157\156\x5f\x6d\x65\x73\x73\141\x67\145"));
        GQ:
        if (!self::mo_check_option_admin_referer("\x6c\157\x67\151\x6e\137\x77\x69\144\147\145\164\137\163\141\155\x6c\x5f\163\x61\x76\145\x5f\163\x65\x74\x74\151\x6e\x67\x73")) {
            goto lG;
        }
        if (mo_saml_is_extension_installed("\143\x75\162\154")) {
            goto qK;
        }
        update_site_option("\155\x6f\137\x73\141\x6d\x6c\x5f\x6d\145\x73\x73\141\x67\145", "\105\122\x52\x4f\122\x3a\x20\x3c\141\40\x68\162\145\146\x3d\x22\150\x74\164\x70\x3a\57\57\160\150\x70\56\156\x65\x74\x2f\x6d\141\x6e\165\x61\x6c\57\x65\156\57\143\165\x72\154\56\151\x6e\163\164\141\154\x6c\141\x74\x69\x6f\x6e\x2e\160\x68\x70\42\x20\x74\x61\x72\147\x65\164\75\42\137\142\154\141\x6e\x6b\x22\x3e\120\x48\120\40\x63\x55\x52\114\40\x65\170\164\x65\156\163\x69\157\156\x3c\x2f\x61\x3e\40\151\163\x20\156\157\x74\40\151\156\163\164\141\x6c\154\x65\144\x20\x6f\162\40\x64\x69\163\x61\142\154\x65\x64\56\40\x53\141\166\x65\x20\111\144\x65\x6e\x74\151\164\x79\40\x50\x72\157\x76\151\x64\x65\x72\40\x43\157\x6e\x66\x69\147\x75\x72\141\164\x69\x6f\x6e\x20\x66\141\x69\x6c\145\x64\x2e");
        $this->mo_saml_show_error_message();
        return;
        qK:
        $uh = '';
        $O9 = '';
        $Ce = '';
        $da = '';
        $oP = '';
        $qx = '';
        $y3 = '';
        $PE = '';
        $Xq = '';
        if ($this->mo_saml_check_empty_or_null($_POST["\x73\x61\x6d\154\137\151\144\145\156\164\x69\164\x79\137\156\x61\x6d\x65"]) || $this->mo_saml_check_empty_or_null($_POST["\163\x61\x6d\x6c\137\154\157\147\x69\156\x5f\x75\162\x6c"]) || $this->mo_saml_check_empty_or_null($_POST["\163\x61\x6d\x6c\137\151\x73\163\165\x65\162"])) {
            goto tC;
        }
        if (!preg_match("\57\136\134\x77\x2a\44\x2f", $_POST["\163\141\155\154\137\x69\144\x65\x6e\164\151\x74\171\137\x6e\141\x6d\x65"])) {
            goto Wn;
        }
        $uh = htmlspecialchars(trim($_POST["\x73\x61\x6d\154\137\151\x64\145\156\164\151\x74\x79\x5f\156\141\155\x65"]));
        $Ce = htmlspecialchars(trim($_POST["\163\x61\x6d\154\137\x6c\x6f\147\151\x6e\137\x75\162\x6c"]));
        if (!array_key_exists("\x73\x61\155\x6c\137\154\157\x67\151\156\x5f\142\x69\x6e\144\x69\x6e\147\x5f\164\171\160\x65", $_POST)) {
            goto WU;
        }
        $O9 = htmlspecialchars($_POST["\163\x61\x6d\x6c\x5f\x6c\x6f\x67\151\156\137\x62\151\x6e\144\151\x6e\147\137\x74\x79\x70\x65"]);
        WU:
        if (!array_key_exists("\163\141\x6d\x6c\137\154\157\147\157\x75\164\137\x62\x69\156\x64\x69\156\147\137\164\171\x70\x65", $_POST)) {
            goto cG;
        }
        $da = htmlspecialchars($_POST["\163\x61\x6d\x6c\x5f\154\x6f\147\x6f\x75\164\x5f\142\151\x6e\144\151\156\x67\137\x74\x79\160\x65"]);
        cG:
        if (!array_key_exists("\x73\x61\155\x6c\137\x6c\x6f\147\157\165\164\137\165\162\x6c", $_POST)) {
            goto fM;
        }
        $oP = htmlspecialchars(trim($_POST["\x73\141\x6d\x6c\137\x6c\x6f\147\157\165\x74\137\165\x72\154"]));
        fM:
        $qx = htmlspecialchars(trim($_POST["\163\141\x6d\154\137\x69\x73\x73\x75\x65\162"]));
        $r0 = htmlspecialchars(trim($_POST["\163\141\155\154\137\x69\144\145\156\x74\x69\x74\171\x5f\160\x72\x6f\166\151\144\145\162\x5f\147\165\x69\144\x65\x5f\x6e\x61\155\x65"]));
        $y3 = maybe_unserialize($_POST["\163\x61\x6d\x6c\137\x78\x35\60\x39\137\143\x65\162\x74\151\146\x69\143\141\x74\145"]);
        $Xq = htmlspecialchars($_POST["\x73\x61\x6d\x6c\x5f\156\141\155\145\151\144\x5f\x66\x6f\162\x6d\141\x74"]);
        goto kI;
        Wn:
        update_site_option("\155\x6f\137\163\141\x6d\x6c\137\x6d\x65\x73\x73\141\x67\145", "\x50\154\x65\x61\x73\x65\x20\155\x61\x74\143\150\40\164\x68\145\x20\x72\145\x71\x75\x65\x73\x74\x65\x64\x20\146\157\x72\x6d\x61\x74\40\146\157\162\40\x49\x64\145\x6e\164\x69\x74\x79\40\120\x72\157\x76\x69\144\145\x72\40\116\x61\155\x65\x2e\40\x4f\156\154\171\40\141\154\160\x68\141\x62\x65\x74\x73\54\x20\x6e\x75\155\x62\x65\162\x73\40\141\x6e\x64\x20\165\x6e\x64\145\x72\x73\143\157\x72\x65\x20\151\163\x20\141\x6c\154\x6f\x77\145\144\56");
        $this->mo_saml_show_error_message();
        return;
        kI:
        goto Ap;
        tC:
        update_site_option("\x6d\157\137\x73\141\155\154\x5f\x6d\x65\163\163\x61\x67\145", "\101\154\154\40\164\x68\x65\40\x66\x69\145\154\x64\x73\x20\x61\162\145\40\x72\145\x71\165\x69\x72\x65\144\x2e\40\x50\154\145\141\x73\145\x20\145\156\x74\x65\162\x20\166\x61\x6c\151\x64\40\145\x6e\x74\x72\151\x65\163\56");
        $this->mo_saml_show_error_message();
        return;
        Ap:
        update_site_option("\163\x61\x6d\154\x5f\x69\144\145\156\164\x69\164\171\x5f\156\141\155\145", $uh);
        update_site_option("\163\141\x6d\154\137\154\157\x67\x69\156\137\x62\x69\x6e\144\151\x6e\147\x5f\164\171\160\x65", $O9);
        update_site_option("\x73\x61\x6d\154\137\154\x6f\147\151\156\x5f\165\x72\x6c", $Ce);
        update_site_option("\x73\141\155\x6c\137\154\157\x67\157\165\x74\137\142\x69\x6e\x64\x69\x6e\x67\137\x74\171\160\145", $da);
        update_site_option("\163\x61\155\154\x5f\x6c\x6f\x67\x6f\x75\164\x5f\x75\x72\154", $oP);
        update_site_option("\163\141\x6d\x6c\x5f\x69\163\x73\x75\145\x72", $qx);
        update_site_option("\x73\x61\155\x6c\x5f\x6e\x61\155\145\151\144\137\x66\157\162\155\x61\x74", $Xq);
        update_site_option("\163\141\155\154\137\x69\144\x65\x6e\x74\151\164\171\137\160\162\x6f\166\151\x64\x65\162\x5f\x67\165\x69\144\145\x5f\x6e\x61\x6d\x65", $r0);
        if (isset($_POST["\163\141\x6d\x6c\x5f\162\145\x71\x75\145\x73\164\137\x73\151\x67\x6e\145\x64"])) {
            goto rt;
        }
        update_site_option("\163\x61\155\154\137\x72\145\x71\x75\145\x73\164\137\163\x69\x67\x6e\145\144", "\x75\x6e\143\x68\x65\143\153\x65\144");
        goto pD;
        rt:
        update_site_option("\x73\141\155\x6c\x5f\x72\145\x71\165\x65\163\164\137\x73\151\x67\x6e\x65\144", "\143\150\x65\x63\x6b\x65\x64");
        pD:
        foreach ($y3 as $Z1 => $zF) {
            if (empty($zF)) {
                goto kB;
            }
            $y3[$Z1] = Utilities::sanitize_certificate($zF);
            if (@openssl_x509_read($y3[$Z1])) {
                goto R2;
            }
            update_site_option("\x6d\157\137\163\x61\155\154\x5f\155\145\x73\x73\141\147\x65", "\111\156\x76\x61\x6c\x69\144\x20\x63\145\x72\164\x69\x66\151\x63\141\x74\x65\40\x66\x6f\x72\x6d\x61\164\x3a\x50\x6c\145\x61\163\x65\40\160\162\157\166\x69\x64\x65\x20\141\x20\x76\141\x6c\x69\x64\40\x63\145\162\x74\x69\146\151\143\x61\164\145");
            $this->mo_saml_show_error_message();
            delete_site_option("\163\141\x6d\x6c\x5f\170\65\x30\71\137\143\x65\162\x74\x69\x66\x69\143\141\164\x65");
            return;
            R2:
            goto bu;
            kB:
            unset($y3[$Z1]);
            bu:
            uj:
        }
        Ha:
        if (!empty($y3)) {
            goto KU;
        }
        update_site_option("\x6d\x6f\137\163\141\x6d\x6c\137\x6d\145\163\x73\141\x67\x65", "\x49\x6e\x76\141\x6c\151\144\x20\x63\x65\162\164\x69\x66\x69\143\x61\x74\x65\40\x66\157\162\x6d\141\164\x20\x3a\x20\x50\x6c\145\141\163\145\40\160\x72\157\166\x69\x64\145\40\x61\x20\143\x65\x72\x74\151\146\151\143\x61\x74\145");
        $this->mo_saml_show_error_message();
        return;
        KU:
        if (isset($_POST["\x73\141\155\154\x5f\x72\145\163\160\157\x6e\163\145\137\163\151\147\x6e\145\x64"])) {
            goto gV;
        }
        update_site_option("\163\141\x6d\x6c\x5f\162\x65\163\x70\x6f\156\x73\145\x5f\x73\151\x67\x6e\x65\144", "\131\x65\163");
        goto Oz;
        gV:
        update_site_option("\x73\x61\155\154\x5f\162\x65\x73\x70\157\156\163\x65\137\x73\x69\x67\x6e\x65\144", "\x63\x68\x65\x63\x6b\x65\x64");
        Oz:
        if (isset($_POST["\163\141\x6d\x6c\137\x61\163\163\x65\x72\164\x69\157\x6e\137\x73\151\x67\156\x65\144"])) {
            goto Xu;
        }
        update_site_option("\163\x61\155\x6c\x5f\x61\163\x73\145\x72\164\x69\x6f\156\x5f\x73\x69\x67\156\145\x64", "\x59\145\163");
        goto vI;
        Xu:
        update_site_option("\163\x61\155\x6c\137\x61\163\163\x65\162\x74\151\x6f\x6e\137\x73\x69\x67\156\x65\144", "\143\x68\145\x63\153\145\144");
        vI:
        update_site_option("\x73\x61\155\154\137\170\x35\60\71\137\143\x65\x72\x74\x69\146\151\x63\141\x74\x65", $y3);
        if (array_key_exists("\145\x6e\x61\x62\x6c\145\137\x69\143\157\156\x76", $_POST)) {
            goto ro;
        }
        update_site_option("\x6d\157\137\163\x61\155\154\137\145\156\x63\157\144\x69\156\x67\137\x65\156\141\x62\154\x65\144", '');
        goto bK;
        ro:
        update_site_option("\155\x6f\x5f\x73\x61\x6d\x6c\137\x65\156\143\157\x64\151\156\x67\137\x65\x6e\x61\x62\x6c\x65\x64", "\143\150\145\x63\153\x65\x64");
        bK:
        update_site_option("\155\157\137\x73\141\x6d\154\x5f\x6d\x65\x73\163\x61\x67\x65", "\111\x64\145\156\x74\151\164\x79\40\120\x72\x6f\166\151\144\145\x72\x20\144\x65\164\x61\151\154\x73\40\x73\x61\166\x65\144\x20\x73\x75\x63\x63\145\163\x73\x66\x75\x6c\x6c\171\56");
        $this->mo_saml_show_success_message();
        lG:
        if (!self::mo_check_option_admin_referer("\x6c\x6f\x67\x69\x6e\x5f\x77\x69\x64\x67\145\x74\x5f\x73\141\155\154\137\141\x74\x74\162\x69\x62\165\164\x65\x5f\155\x61\160\160\151\156\x67")) {
            goto Yt;
        }
        if (mo_saml_is_extension_installed("\x63\x75\162\x6c")) {
            goto L2;
        }
        update_site_option("\x6d\157\137\x73\x61\155\154\137\x6d\145\163\163\x61\x67\x65", "\105\x52\122\117\122\x3a\40\74\141\x20\x68\162\x65\x66\75\42\x68\164\x74\160\72\57\57\x70\x68\160\x2e\x6e\145\164\57\155\141\156\x75\141\154\57\x65\x6e\x2f\x63\x75\162\x6c\56\151\156\x73\x74\x61\154\154\141\x74\151\x6f\156\56\x70\x68\x70\x22\x20\x74\x61\x72\x67\x65\164\x3d\42\x5f\x62\x6c\141\156\x6b\x22\x3e\x50\x48\120\40\x63\x55\x52\114\40\145\170\x74\x65\156\x73\x69\157\x6e\x3c\x2f\141\76\x20\151\163\x20\156\157\164\x20\x69\x6e\x73\x74\141\154\154\x65\x64\40\157\x72\40\144\x69\163\x61\142\x6c\145\144\x2e\40\123\141\x76\145\40\x41\164\x74\162\151\142\x75\x74\145\40\115\x61\160\160\x69\156\147\40\x66\141\x69\154\x65\x64\x2e");
        $this->mo_saml_show_error_message();
        return;
        L2:
        update_site_option("\163\141\x6d\x6c\x5f\141\155\x5f\x75\163\145\x72\x6e\x61\x6d\x65", htmlspecialchars(stripslashes($_POST["\163\x61\x6d\x6c\x5f\x61\155\x5f\165\x73\145\x72\156\141\x6d\x65"])));
        update_site_option("\x73\141\x6d\x6c\x5f\141\155\x5f\x65\x6d\141\x69\154", htmlspecialchars(stripslashes($_POST["\163\141\155\x6c\137\141\155\137\x65\155\x61\x69\154"])));
        update_site_option("\x73\x61\x6d\154\137\x61\x6d\137\146\151\162\163\x74\137\156\x61\x6d\145", htmlspecialchars(stripslashes($_POST["\163\x61\155\154\x5f\x61\x6d\137\x66\151\x72\x73\x74\137\156\x61\155\145"])));
        update_site_option("\x73\x61\x6d\154\x5f\x61\x6d\137\154\141\163\164\137\156\x61\x6d\x65", htmlspecialchars(stripslashes($_POST["\163\x61\155\x6c\x5f\141\x6d\x5f\x6c\x61\163\164\x5f\x6e\141\x6d\145"])));
        update_site_option("\163\x61\x6d\x6c\137\141\x6d\x5f\x67\162\157\x75\160\x5f\156\141\x6d\145", htmlspecialchars(stripslashes($_POST["\163\141\x6d\154\137\141\155\x5f\x67\x72\157\165\160\137\156\x61\155\145"])));
        update_site_option("\x73\x61\155\154\137\141\155\137\x64\x69\163\x70\154\141\x79\x5f\x6e\x61\155\x65", htmlspecialchars(stripslashes($_POST["\163\x61\155\x6c\x5f\x61\x6d\137\x64\x69\x73\160\154\141\171\137\x6e\x61\155\x65"])));
        $Ve = array();
        $wp = array();
        $pC = array();
        $Zs = array();
        if (!(isset($_POST["\x6d\157\x5f\x73\x61\155\154\137\143\x75\x73\164\157\155\x5f\141\x74\164\x72\x69\142\x75\x74\145\x5f\153\x65\171\163"]) && !empty($_POST["\155\x6f\137\163\x61\x6d\x6c\x5f\x63\165\163\164\x6f\155\x5f\x61\x74\x74\x72\x69\x62\x75\164\145\137\x6b\x65\171\x73"]))) {
            goto MK;
        }
        $wp = $_POST["\x6d\157\137\x73\x61\x6d\154\x5f\x63\x75\x73\164\x6f\155\x5f\x61\164\x74\x72\151\142\x75\x74\x65\x5f\153\145\x79\163"];
        MK:
        if (!(isset($_POST["\x6d\x6f\x5f\163\x61\155\154\x5f\143\165\163\x74\x6f\155\x5f\141\164\164\x72\151\x62\165\164\145\x5f\x76\141\x6c\x75\x65\163"]) && !empty($_POST["\155\157\x5f\163\141\155\x6c\x5f\x63\165\x73\164\x6f\x6d\x5f\x61\164\164\162\x69\x62\x75\x74\145\x5f\166\x61\154\x75\145\163"]))) {
            goto TJ;
        }
        $pC = $_POST["\155\157\137\x73\x61\155\x6c\x5f\143\165\163\164\x6f\x6d\137\141\164\164\162\x69\x62\x75\x74\145\x5f\166\x61\154\165\x65\163"];
        TJ:
        $fK = count($wp);
        if (!($fK > 0)) {
            goto Z0;
        }
        $wp = array_map("\150\x74\155\x6c\x73\x70\x65\x63\151\141\154\143\x68\141\x72\163", $wp);
        $pC = array_map("\150\164\155\x6c\163\x70\x65\x63\x69\141\x6c\143\x68\x61\x72\163", $pC);
        $Ga = 0;
        tc:
        if (!($Ga < $fK)) {
            goto Ot;
        }
        if (!(isset($_POST["\x6d\157\x5f\163\141\155\x6c\137\x64\x69\x73\x70\154\141\171\137\141\x74\x74\162\151\142\x75\x74\x65\x5f" . $Ga]) && !empty($_POST["\155\157\x5f\163\x61\155\x6c\x5f\144\x69\163\160\x6c\141\x79\137\141\x74\164\162\151\142\165\164\145\x5f" . $Ga]))) {
            goto Xr;
        }
        array_push($Zs, $Ga);
        Xr:
        $Ga++;
        goto tc;
        Ot:
        Z0:
        update_site_option("\x73\x61\155\154\137\x73\x68\157\x77\137\x75\x73\145\162\137\x61\164\164\162\x69\142\x75\164\x65", $Zs);
        $Ve = array_combine($wp, $pC);
        $Ve = array_filter($Ve);
        if (!empty($Ve)) {
            goto Bx;
        }
        $Ve = get_site_option("\x6d\157\x5f\163\141\x6d\x6c\137\x63\x75\163\x74\x6f\155\137\x61\x74\x74\x72\163\137\155\141\x70\x70\151\156\x67");
        if (empty($Ve)) {
            goto tu;
        }
        delete_site_option("\x6d\x6f\x5f\163\141\155\154\137\x63\x75\x73\x74\x6f\155\137\x61\164\x74\162\x73\137\155\141\x70\x70\x69\156\x67");
        tu:
        goto yJ;
        Bx:
        update_site_option("\155\x6f\x5f\x73\x61\155\x6c\137\x63\x75\163\164\157\x6d\137\141\x74\164\162\x73\x5f\155\141\160\160\151\x6e\147", $Ve);
        yJ:
        update_site_option("\155\x6f\x5f\163\141\x6d\x6c\x5f\155\145\x73\x73\141\147\x65", "\x41\x74\164\162\151\142\x75\x74\145\40\115\141\x70\x70\x69\156\147\40\144\x65\164\141\x69\x6c\x73\x20\163\141\166\145\144\x20\x73\165\143\143\x65\163\163\146\x75\x6c\x6c\171");
        $this->mo_saml_show_success_message();
        Yt:
        if (!self::mo_check_option_admin_referer("\143\x6c\x65\x61\x72\137\141\164\x74\x72\163\x5f\x6c\151\163\164")) {
            goto pH;
        }
        delete_site_option("\x6d\x6f\137\x73\141\155\154\137\x74\145\x73\x74\x5f\143\x6f\156\x66\151\147\137\x61\x74\164\162\x73");
        update_site_option("\x6d\x6f\x5f\x73\x61\155\x6c\x5f\x6d\x65\163\x73\141\x67\145", "\x41\x74\164\x72\151\142\165\x74\x65\x73\40\154\151\163\164\x20\x72\145\x6d\x6f\x76\145\144\x20\163\165\143\143\x65\x73\163\146\x75\154\x6c\171");
        $this->mo_saml_show_success_message();
        pH:
        if (!self::mo_check_option_admin_referer("\x73\141\155\x6c\x5f\146\157\162\x6d\x5f\x64\157\155\141\x69\156\137\162\x65\x73\164\162\x69\143\x74\151\157\156\137\157\x70\x74\151\157\156")) {
            goto nG;
        }
        $wq = isset($_POST["\x6d\157\137\163\141\155\154\x5f\x65\x6e\x61\x62\x6c\x65\137\x64\x6f\x6d\141\151\156\137\x72\145\163\164\162\x69\143\164\x69\x6f\x6e\137\x6c\157\x67\x69\x6e"]) && !empty($_POST["\x6d\x6f\x5f\x73\x61\155\x6c\137\145\156\141\142\x6c\x65\x5f\x64\157\155\141\151\156\137\162\145\163\x74\x72\x69\x63\164\x69\x6f\x6e\137\x6c\x6f\147\x69\x6e"]) ? htmlspecialchars($_POST["\155\x6f\137\163\x61\x6d\154\x5f\145\156\x61\142\154\145\137\x64\157\x6d\x61\151\x6e\137\162\x65\163\x74\162\x69\x63\164\x69\157\x6e\x5f\154\157\147\151\156"]) : '';
        $AE = isset($_POST["\x6d\157\137\x73\x61\x6d\154\137\x61\154\x6c\x6f\x77\137\144\145\x6e\x79\x5f\x75\163\x65\x72\x5f\x77\151\x74\150\137\x64\x6f\x6d\141\151\x6e"]) && !empty($_POST["\x6d\157\x5f\163\x61\155\x6c\x5f\141\154\154\157\167\137\x64\145\x6e\x79\137\165\163\x65\162\x5f\x77\151\164\150\137\144\x6f\x6d\x61\x69\156"]) ? htmlspecialchars($_POST["\155\x6f\137\x73\x61\155\x6c\137\141\x6c\x6c\x6f\x77\x5f\144\145\156\x79\137\x75\163\145\162\x5f\x77\151\164\x68\137\144\x6f\x6d\x61\x69\156"]) : "\x61\154\x6c\157\167";
        $cG = isset($_POST["\163\x61\x6d\x6c\137\141\x6d\x5f\x65\x6d\x61\151\154\137\144\x6f\155\x61\151\x6e\x73"]) && !empty($_POST["\x73\141\155\x6c\137\141\x6d\x5f\145\x6d\x61\151\154\x5f\144\157\x6d\x61\151\156\x73"]) ? htmlspecialchars($_POST["\x73\141\x6d\x6c\137\x61\x6d\x5f\x65\155\141\x69\154\137\144\157\155\x61\x69\156\x73"]) : '';
        update_site_option("\x6d\157\137\x73\x61\x6d\154\x5f\x65\x6e\x61\142\154\145\x5f\144\x6f\x6d\x61\151\156\137\162\145\x73\x74\162\x69\143\164\x69\x6f\x6e\137\154\157\x67\x69\156", $wq);
        update_site_option("\155\x6f\137\163\x61\155\154\x5f\141\x6c\154\x6f\x77\x5f\144\x65\x6e\x79\137\165\x73\145\x72\x5f\167\x69\x74\150\137\x64\157\x6d\141\151\x6e", $AE);
        update_site_option("\x73\141\x6d\x6c\137\x61\x6d\137\x65\155\141\151\x6c\137\144\157\x6d\x61\151\156\x73", $cG);
        update_site_option("\155\157\x5f\163\x61\155\154\137\155\x65\163\163\x61\x67\x65", "\x44\x6f\155\141\x69\x6e\40\122\145\163\x74\162\151\x63\x74\151\157\x6e\40\150\x61\163\40\x62\145\145\x6e\x20\x73\141\166\145\x64\x20\x73\x75\143\x63\145\x73\x73\x66\165\154\154\x79\56");
        $this->mo_saml_show_success_message();
        nG:
        if (!self::mo_check_option_admin_referer("\154\157\147\151\x6e\x5f\167\151\x64\147\145\164\x5f\163\141\155\154\137\x72\157\x6c\145\137\155\141\x70\x70\x69\156\x67")) {
            goto qG;
        }
        if (mo_saml_is_extension_installed("\x63\165\162\154")) {
            goto p0;
        }
        update_site_option("\x6d\x6f\137\163\x61\x6d\x6c\x5f\155\x65\163\x73\x61\x67\x65", "\x45\x52\x52\117\x52\x3a\40\x3c\141\40\150\162\x65\x66\75\42\150\x74\x74\160\x3a\x2f\57\x70\150\x70\x2e\x6e\x65\x74\x2f\x6d\x61\x6e\165\x61\x6c\x2f\x65\x6e\x2f\143\165\x72\x6c\56\151\156\163\x74\141\154\154\141\x74\x69\157\156\56\x70\150\160\42\40\164\141\x72\x67\145\164\75\42\x5f\x62\x6c\x61\x6e\153\42\76\x50\x48\120\40\143\x55\122\114\x20\x65\170\164\145\x6e\163\x69\157\x6e\x3c\57\x61\x3e\40\151\163\x20\x6e\x6f\164\40\151\156\x73\164\x61\154\154\145\144\40\x6f\x72\x20\144\151\x73\141\x62\154\145\x64\56\40\x53\141\166\x65\x20\x52\157\x6c\145\40\115\x61\x70\160\x69\x6e\x67\x20\146\x61\x69\x6c\145\x64\56");
        $this->mo_saml_show_error_message();
        return;
        p0:
        if (isset($_POST["\155\157\x5f\x61\160\160\154\171\137\162\157\154\x65\137\155\141\x70\160\151\156\x67\137\x66\x6f\162\x5f\163\x69\164\x65\x73"]) && $_POST["\155\x6f\x5f\x61\160\x70\154\x79\x5f\x72\157\154\145\137\x6d\141\160\x70\151\156\147\x5f\146\157\x72\137\163\x69\x74\145\163"] == 0) {
            goto zO;
        }
        if (!(isset($_POST["\x6d\157\137\141\x70\160\x6c\171\x5f\x72\x6f\x6c\x65\x5f\x6d\141\x70\160\x69\x6e\x67\x5f\146\157\x72\x5f\163\x69\x74\x65\163"]) && $_POST["\155\157\137\141\160\x70\x6c\x79\137\x72\157\x6c\145\137\x6d\141\160\160\151\156\x67\137\x66\x6f\x72\x5f\x73\x69\x74\x65\x73"] == 1)) {
            goto xY;
        }
        update_site_option("\x6d\157\x5f\141\x70\160\154\171\137\162\157\x6c\x65\137\155\x61\x70\160\151\x6e\x67\137\x66\157\x72\x5f\163\151\164\145\163", 1);
        $iy = 0;
        $yQ = array();
        update_site_option("\163\x61\155\154\137\x61\x6d\x5f\162\157\x6c\x65\137\x6d\x61\x70\160\x69\156\147", $yQ);
        xY:
        goto jg;
        zO:
        if (!$this->mo_saml_check_empty_or_null($_POST["\162\157\x6c\145\x5f\x6d\141\160\160\151\156\x67\x5f\163\151\x74\145"])) {
            goto QH;
        }
        update_site_option("\155\x6f\x5f\x73\x61\x6d\x6c\x5f\x6d\145\x73\163\141\x67\145", "\x50\154\x65\x61\163\x65\x20\x73\145\154\145\x63\164\40\141\x20\163\x69\x74\x65\40\x74\x6f\x20\x73\x61\166\x65\x20\162\157\154\x65\x20\155\x61\160\160\151\156\x67");
        $this->mo_saml_show_error_message();
        return;
        QH:
        update_site_option("\x6d\157\x5f\141\160\160\x6c\171\x5f\x72\x6f\154\145\x5f\155\141\x70\160\x69\x6e\x67\137\146\x6f\x72\x5f\163\x69\x74\145\x73", 0);
        $iy = htmlspecialchars($_POST["\x72\157\x6c\x65\x5f\155\x61\160\x70\x69\156\x67\x5f\x73\x69\x74\x65"]);
        jg:
        if (!isset($_POST["\x73\141\155\x6c\137\141\x6d\137\144\x65\x66\x61\165\154\x74\x5f\x75\163\145\162\x5f\162\x6f\154\x65"])) {
            goto ic;
        }
        $w8 = htmlspecialchars($_POST["\163\141\155\x6c\137\141\x6d\137\144\x65\146\141\x75\154\164\x5f\165\x73\145\x72\x5f\x72\x6f\154\145"]);
        ic:
        if (isset($_POST["\163\141\155\154\x5f\x61\155\x5f\x64\157\156\164\x5f\141\154\x6c\157\167\x5f\x75\x6e\x6c\x69\163\164\145\x64\x5f\x75\163\x65\162\137\162\157\x6c\x65"])) {
            goto Jw;
        }
        $h3 = "\165\x6e\143\150\145\x63\x6b\145\x64";
        goto Y4;
        Jw:
        $h3 = "\x63\150\x65\143\x6b\145\x64";
        $w8 = false;
        Y4:
        if (isset($_POST["\155\157\137\x73\x61\155\x6c\137\x64\157\156\164\x5f\143\162\x65\141\x74\145\x5f\x75\x73\145\x72\137\x69\146\137\162\157\154\145\x5f\x6e\157\x74\137\155\141\160\x70\145\x64"])) {
            goto xk;
        }
        $CV = "\165\156\x63\x68\x65\143\153\145\x64";
        goto ci;
        xk:
        $CV = "\x63\150\145\x63\153\x65\144";
        $h3 = "\x75\156\143\150\x65\x63\153\x65\x64";
        $w8 = false;
        ci:
        if (isset($_POST["\155\157\137\x73\x61\155\154\x5f\x6b\145\x65\160\x5f\145\170\151\x73\x74\x69\156\x67\137\165\163\145\x72\163\x5f\x72\157\154\x65"])) {
            goto r1;
        }
        $M2 = "\165\156\x63\150\145\x63\153\145\x64";
        goto kw;
        r1:
        $M2 = "\x63\150\145\143\x6b\x65\x64";
        kw:
        if (isset($_POST["\155\157\137\163\x61\x6d\x6c\x5f\144\x6f\x6e\164\137\141\154\154\157\167\137\x75\x73\145\x72\137\164\x6f\x6c\157\147\x69\156\x5f\x63\162\145\x61\x74\x65\x5f\167\151\x74\x68\137\147\151\166\x65\x6e\137\x67\x72\x6f\165\160\163"])) {
            goto Qk;
        }
        $mw = "\x75\x6e\143\150\x65\x63\x6b\145\x64";
        goto by;
        Qk:
        $mw = "\143\x68\x65\143\x6b\x65\144";
        by:
        if (!isset($_POST["\x6d\x6f\x5f\163\x61\x6d\154\137\162\145\163\164\162\151\143\x74\x5f\x75\163\x65\162\x73\137\x77\151\164\x68\137\147\162\157\x75\x70\x73"])) {
            goto TD;
        }
        $Ys = htmlspecialchars($_POST["\155\x6f\137\163\x61\x6d\x6c\137\x72\145\x73\164\162\151\143\164\x5f\x75\163\x65\162\163\137\x77\x69\164\150\137\147\162\157\165\160\x73"]);
        TD:
        if (!isset($_POST["\x73\141\x6d\x6c\137\x61\155\137\x67\162\x6f\x75\x70\x5f\141\164\164\x72\x5f\166\x61\x6c\165\x65\x73\x5f\163\165\160\x65\162\141\144\155\151\x6e"])) {
            goto xD;
        }
        $cd = htmlspecialchars($_POST["\x73\x61\x6d\x6c\137\x61\x6d\x5f\x67\x72\x6f\165\x70\x5f\141\164\164\x72\137\x76\141\154\x75\145\x73\137\x73\x75\x70\x65\162\x61\144\x6d\151\x6e"]);
        xD:
        $wp_roles = new WP_Roles($iy);
        $hp = $wp_roles->get_names();
        $yQ = maybe_unserialize(get_site_option("\163\141\155\154\x5f\x61\x6d\x5f\162\x6f\154\145\x5f\155\x61\x70\x70\x69\x6e\147"));
        foreach ($hp as $Ps => $gO) {
            $fN = "\x73\x61\x6d\x6c\137\x61\155\x5f\147\x72\157\x75\x70\x5f\x61\x74\164\162\137\x76\x61\154\x75\x65\x73\137" . $Ps;
            $yQ[$iy][$Ps] = stripslashes($_POST[$fN]);
            aJ:
        }
        rB:
        $yQ[$iy]["\144\x65\146\x61\x75\x6c\164\x5f\162\x6f\154\145"] = $w8;
        $yQ[$iy]["\x64\x6f\x6e\x74\137\x63\x72\145\141\x74\x65\x5f\x75\163\x65\x72"] = $CV;
        $yQ[$iy]["\144\x6f\156\164\137\x61\154\x6c\x6f\167\x5f\165\x6e\x6c\151\163\x74\145\x64\x5f\x75\163\x65\162"] = $h3;
        $yQ[$iy]["\153\145\145\x70\137\145\x78\x69\163\164\151\156\x67\137\x75\163\145\x72\163\137\x72\x6f\154\x65"] = $M2;
        $yQ[$iy]["\x6d\x6f\137\163\141\x6d\x6c\137\x64\157\156\164\x5f\141\154\x6c\157\167\137\x75\x73\145\x72\x5f\x74\157\x6c\157\147\x69\156\x5f\143\162\x65\x61\x74\x65\137\x77\151\x74\150\x5f\147\151\166\x65\x6e\137\x67\x72\157\165\x70\x73"] = $mw;
        $yQ[$iy]["\155\x6f\x5f\x73\141\x6d\x6c\137\162\x65\x73\x74\162\151\143\x74\137\x75\x73\x65\x72\163\x5f\x77\151\x74\x68\137\x67\162\x6f\165\x70\163"] = $Ys;
        $yQ = array_filter($yQ, "\x66\151\154\164\145\162\x5f\x65\x6d\160\x74\x79\137\166\x61\154\165\145\163");
        $PM = false;
        if (!(isset($_POST["\x6d\157\x5f\x73\141\155\x6c\137\144\151\163\x61\142\154\x65\x5f\162\157\x6c\x65\x5f\x6d\x61\x70\x70\x69\x6e\147"]) and $_POST["\155\x6f\137\163\x61\155\x6c\x5f\x64\x69\163\x61\x62\x6c\145\x5f\162\x6f\x6c\x65\x5f\x6d\x61\x70\x70\151\x6e\147"] == "\x74\162\165\x65")) {
            goto DE;
        }
        $PM = true;
        DE:
        update_site_option("\155\x6f\137\x73\x61\155\x6c\x5f\144\x69\163\x61\x62\154\x65\x5f\162\x6f\154\145\137\155\141\160\x70\x69\x6e\147", $PM);
        update_site_option("\x73\141\x6d\154\x5f\x61\155\137\162\157\154\145\x5f\x6d\x61\x70\160\x69\x6e\147", $yQ);
        update_site_option("\155\157\137\163\141\155\x6c\137\x6d\x65\163\x73\141\x67\x65", "\122\x6f\154\x65\40\115\141\160\160\151\156\x67\40\x64\145\164\141\151\154\163\x20\163\141\x76\x65\144\40\x73\x75\x63\143\x65\x73\163\x66\165\x6c\x6c\171\56");
        update_site_option("\155\x6f\x5f\x73\141\155\x6c\137\x73\x75\160\x65\x72\x5f\x61\x64\155\x69\x6e\137\x72\x6f\154\145\x5f\x6d\141\x70\x70\151\x6e\x67", $cd);
        $this->mo_saml_show_success_message();
        qG:
        if (!self::mo_check_option_admin_referer("\155\157\x5f\x73\141\155\x6c\137\163\165\x62\163\x69\164\145\137\x73\145\154\145\143\164\x69\x6f\156\137\x66\x6f\x72\137\162\x6f\x6c\x65\x5f\x6d\x61\160\160\151\x6e\147")) {
            goto eA;
        }
        $t1 = isset($_POST["\x6d\x6f\x5f\x61\x70\x70\154\x79\137\x72\157\154\145\137\x6d\x61\160\x70\151\156\x67\x5f\x66\157\162\137\163\x69\x74\x65\x73"]) ? htmlspecialchars($_POST["\x6d\157\137\x61\x70\160\x6c\171\137\162\157\x6c\145\137\155\141\160\160\151\x6e\147\137\x66\157\x72\x5f\x73\x69\x74\145\x73"]) : 0;
        $Is = isset($_POST["\x72\157\x6c\x65\137\x6d\x61\160\160\151\156\x67\137\x73\x69\164\x65"]) ? htmlspecialchars($_POST["\162\x6f\154\x65\137\x6d\141\160\160\x69\x6e\147\137\x73\151\x74\x65"]) : false;
        update_site_option("\x72\157\x6c\x65\x5f\155\141\x70\160\151\x6e\147\x5f\x73\x69\164\145", $Is);
        update_site_option("\x6d\157\137\x61\x70\160\x6c\x79\137\x72\x6f\154\x65\x5f\155\x61\160\x70\x69\156\147\137\146\157\162\x5f\x73\x69\164\x65\163", $t1);
        eA:
        if (!self::mo_check_option_admin_referer("\x6d\x6f\137\x73\x61\x6d\x6c\137\162\x65\x73\145\164\x5f\162\x6f\154\145\137\x6d\x61\x70\160\151\156\x67\x5f\141\x74\x74\162\x69\142\x75\164\x65\163")) {
            goto wS;
        }
        update_site_option("\x6d\157\137\x61\160\160\x6c\x79\x5f\x72\x6f\x6c\x65\137\155\x61\x70\x70\151\x6e\147\137\x66\157\x72\137\163\x69\164\145\x73", 0);
        $yQ = array();
        update_site_option("\x73\x61\x6d\154\137\x61\155\137\162\x6f\154\x65\x5f\155\141\x70\160\151\156\x67", $yQ);
        update_site_option("\155\157\x5f\163\x61\155\x6c\x5f\163\x75\160\145\x72\137\141\x64\155\x69\156\137\162\x6f\x6c\145\x5f\155\141\x70\160\151\156\147", '');
        update_site_option("\155\157\137\x73\x61\155\154\x5f\x6d\x65\x73\x73\141\x67\x65", "\122\x6f\154\145\x20\115\141\x70\x70\x69\156\x67\x20\x68\141\163\x20\142\x65\145\x6e\40\162\x65\163\145\x74\x20\163\x75\x63\x63\x65\163\x73\x66\165\154\154\171\x2e");
        $this->mo_saml_show_success_message();
        wS:
        if (!self::mo_check_option_admin_referer("\x6d\157\137\163\x61\155\x6c\x5f\162\x65\163\x65\x74\137\x73\163\157\137\163\x65\x74\x74\x69\156\147\x73\137\157\160\x74\x69\157\156")) {
            goto Zr;
        }
        $pK = array();
        update_site_option("\163\141\155\x6c\x5f\163\x73\x6f\x5f\163\x65\164\x74\x69\x6e\x67\163", $pK);
        update_site_option("\155\x6f\137\x73\x61\155\154\x5f\155\145\x73\163\x61\147\x65", "\123\123\117\x20\x53\145\164\164\x69\x6e\x67\x73\40\x68\141\x73\x20\x62\145\x65\156\x20\x72\x65\x73\145\x74\x20\x73\165\x63\143\x65\x73\x73\x66\165\x6c\154\x79\x20\146\157\x72\40\141\154\154\40\x79\x6f\x75\162\40\x73\165\142\x2d\x73\151\x74\x65\163\x2e");
        $this->mo_saml_show_success_message();
        Zr:
        if (!(isset($_POST["\157\x70\x74\x69\x6f\x6e"]) and $_POST["\157\160\x74\x69\157\156"] == "\x6d\157\x5f\163\x61\155\154\x5f\165\x70\144\141\164\x65\137\x73\x70\x5f\142\141\x73\x65\x5f\x75\x72\x6c\137\x6f\160\164\151\157\x6e")) {
            goto so;
        }
        if (!(isset($_POST["\x6d\x6f\x5f\x73\141\155\x6c\137\163\x70\x5f\142\x61\x73\x65\x5f\165\x72\154"]) && isset($_POST["\155\157\x5f\163\x61\x6d\154\137\x73\160\137\x65\x6e\x74\151\x74\x79\137\x69\144"]))) {
            goto TL;
        }
        $Wz = sanitize_text_field($_POST["\155\157\x5f\x73\141\x6d\x6c\137\x73\160\137\142\x61\163\x65\x5f\x75\162\x6c"]);
        $iB = sanitize_text_field($_POST["\x6d\x6f\137\163\141\x6d\154\x5f\163\160\x5f\145\x6e\x74\x69\x74\171\x5f\x69\x64"]);
        if (!(substr($Wz, -1) == "\x2f")) {
            goto p3;
        }
        $Wz = substr($Wz, 0, -1);
        p3:
        update_site_option("\155\x6f\x5f\x73\141\155\x6c\x5f\163\x70\x5f\142\141\163\145\137\165\x72\154", $Wz);
        update_site_option("\x6d\157\x5f\163\141\x6d\154\137\163\x70\x5f\145\156\x74\151\x74\171\137\151\144", $iB);
        TL:
        update_site_option("\155\x6f\137\x73\141\155\x6c\x5f\x6d\x65\x73\x73\141\147\x65", "\123\x50\40\102\x61\x73\145\40\x55\x52\x4c\40\165\160\x64\141\x74\145\144\x20\x73\x75\x63\x63\145\x73\x73\x66\165\154\154\x79\x2e");
        $this->mo_saml_show_success_message();
        so:
        if (!self::mo_check_option_admin_referer("\x73\x61\x6d\154\137\x75\x70\154\x6f\141\x64\x5f\x6d\x65\x74\x61\x64\x61\164\141")) {
            goto P0;
        }
        if (function_exists("\167\x70\137\x68\x61\x6e\144\154\145\x5f\165\x70\x6c\157\141\x64")) {
            goto Te;
        }
        require_once ABSPATH . "\x77\x70\x2d\x61\x64\155\151\156\57\x69\x6e\143\154\165\x64\x65\x73\x2f\x66\151\x6c\x65\x2e\x70\150\x70";
        Te:
        $this->upload_metadata();
        P0:
        if (!self::mo_check_option_admin_referer("\155\157\x5f\163\x61\x6d\154\137\165\x70\144\141\164\145\137\x69\x64\x70\137\163\145\x74\164\151\x6e\147\x73\137\157\160\x74\151\157\156")) {
            goto qz;
        }
        if (!(isset($_POST["\155\x6f\x5f\x73\141\x6d\154\x5f\163\160\x5f\x62\x61\163\145\x5f\165\x72\x6c"]) && isset($_POST["\155\x6f\137\x73\x61\155\x6c\x5f\x73\160\137\x65\156\x74\x69\x74\171\x5f\151\144"]))) {
            goto Ih;
        }
        $Wz = sanitize_text_field($_POST["\x6d\x6f\137\163\141\155\154\137\163\160\x5f\x62\141\x73\x65\x5f\x75\x72\154"]);
        $iB = sanitize_text_field($_POST["\x6d\x6f\x5f\x73\x61\x6d\154\x5f\163\160\x5f\x65\x6e\x74\151\164\x79\x5f\x69\144"]);
        if (!(substr($Wz, -1) == "\x2f")) {
            goto KZ;
        }
        $Wz = substr($Wz, 0, -1);
        KZ:
        update_site_option("\x6d\x6f\137\x73\x61\155\x6c\x5f\x73\x70\x5f\142\x61\163\x65\137\165\162\154", $Wz);
        update_site_option("\155\157\137\163\141\x6d\x6c\x5f\163\160\137\x65\156\x74\151\164\x79\137\x69\144", $iB);
        Ih:
        update_site_option("\155\x6f\x5f\163\x61\x6d\x6c\137\155\145\163\x73\x61\147\145", "\123\x65\164\164\x69\156\147\163\x20\165\160\144\141\x74\145\x64\x20\163\165\143\x63\145\163\163\146\165\x6c\x6c\171\x2e");
        $this->mo_saml_show_success_message();
        qz:
        if (!self::mo_check_option_admin_referer("\145\156\x61\x62\154\x65\137\x73\163\157\x5f\x6e\x65\167\x5f\x73\x69\164\x65\137\x6f\160\164\151\157\156")) {
            goto QX;
        }
        if (isset($_POST["\145\156\141\142\154\145\x5f\x73\163\157\x5f\x6e\145\x77\x5f\x73\151\x74\145"]) and $_POST["\145\156\141\x62\154\145\x5f\163\x73\157\x5f\x6e\x65\x77\x5f\163\151\164\x65"] == "\x74\162\165\x65") {
            goto oF;
        }
        $t4 = "\x66\141\x6c\x73\x65";
        goto zq;
        oF:
        $t4 = "\164\162\165\145";
        zq:
        update_site_option("\155\x6f\137\x73\141\155\154\x5f\145\156\141\x62\154\x65\137\x73\163\x6f\x5f\x6e\x65\167\x5f\x73\x69\x74\145", $t4);
        update_site_option("\155\x6f\x5f\x73\x61\155\x6c\137\155\145\163\163\141\147\x65", "\123\x65\x74\164\151\x6e\147\163\x20\165\x70\x64\141\164\x65\144\40\x73\165\143\143\x65\163\x73\146\165\x6c\154\x79\x2e");
        $this->mo_saml_show_success_message();
        QX:
        if (self::mo_check_option_admin_referer("\x61\144\x64\x5f\x63\x75\163\x74\x6f\155\137\143\145\162\164\151\x66\151\143\x61\164\x65")) {
            goto wz;
        }
        if (self::mo_check_option_admin_referer("\155\x6f\137\x73\141\x6d\154\137\162\x65\155\157\166\145\137\141\x63\x63\157\165\156\164")) {
            goto Ag;
        }
        if (!self::mo_check_option_admin_referer("\x61\x64\x64\137\143\165\163\164\x6f\155\x5f\x6d\145\163\163\x61\147\145\x73")) {
            goto Eg;
        }
        update_site_option("\155\x6f\x5f\163\x61\x6d\154\137\x61\143\x63\x6f\165\156\164\x5f\x63\162\145\x61\164\151\157\x6e\137\144\151\163\x61\x62\x6c\145\x64\137\155\163\x67", sanitize_text_field($_POST["\x6d\157\x5f\163\x61\155\x6c\x5f\x61\143\143\157\x75\x6e\x74\137\143\x72\x65\x61\x74\151\x6f\x6e\137\x64\x69\x73\x61\142\x6c\x65\x64\137\x6d\163\147"]));
        update_site_option("\x6d\x6f\137\163\x61\x6d\154\x5f\162\145\x73\x74\x72\x69\143\x74\x65\144\137\144\157\155\141\151\x6e\137\x65\x72\x72\157\162\137\x6d\x73\147", sanitize_text_field($_POST["\155\x6f\x5f\x73\x61\155\154\137\162\x65\163\x74\162\151\x63\164\145\x64\x5f\x64\157\x6d\x61\x69\156\137\x65\x72\162\157\x72\x5f\155\x73\147"]));
        update_site_option("\x6d\x6f\137\x73\x61\155\x6c\137\155\x65\163\163\x61\x67\x65", "\x43\157\156\x66\151\147\x75\162\141\164\x69\157\156\x20\x68\x61\x73\40\142\145\x65\x6e\40\163\141\166\145\x64\x20\x73\x75\143\x63\145\x73\x73\x66\x75\x6c\154\x79\56");
        $this->mo_saml_show_success_message();
        Eg:
        goto Jr;
        Ag:
        $this->mo_sso_saml_deactivate();
        add_site_option("\155\157\137\x73\141\x6d\x6c\x5f\162\145\x67\x69\x73\x74\162\x61\164\x69\x6f\156\137\163\x74\141\164\x75\163", "\162\145\155\x6f\166\x65\x64\137\x61\143\143\x6f\165\x6e\164");
        $Oy = add_query_arg(array("\164\x61\x62" => "\x6c\157\147\x69\156"), $_SERVER["\x52\x45\121\125\105\x53\124\x5f\125\x52\x49"]);
        header("\114\157\x63\141\164\151\157\x6e\x3a\x20" . $Oy);
        Jr:
        goto rS;
        wz:
        if (isset($_POST["\x73\x75\x62\x6d\x69\x74"]) and $_POST["\163\x75\x62\155\151\x74"] == "\125\160\x6c\157\141\x64") {
            goto iT;
        }
        if (!(isset($_POST["\163\165\142\155\151\x74"]) and $_POST["\163\165\142\x6d\x69\x74"] == "\x52\x65\x73\x65\x74")) {
            goto Ks;
        }
        delete_site_option("\x6d\x6f\137\163\141\155\154\x5f\x63\x75\163\x74\x6f\x6d\137\143\145\162\164");
        delete_site_option("\155\157\x5f\x73\x61\x6d\x6c\x5f\x63\165\163\x74\x6f\155\137\143\x65\x72\164\x5f\x70\162\151\166\141\x74\145\137\153\145\x79");
        update_site_option("\155\157\137\x73\141\x6d\154\x5f\143\165\x72\x72\145\156\x74\x5f\143\x65\162\164", isset($Go));
        update_site_option("\x6d\157\x5f\163\141\x6d\x6c\137\x63\165\x72\162\145\x6e\x74\137\x63\145\162\x74\x5f\160\x72\151\x76\141\x74\x65\x5f\x6b\x65\171", isset($Dh));
        update_site_option("\x6d\157\137\163\141\x6d\154\x5f\155\145\163\163\x61\x67\145", "\122\145\163\145\164\x20\103\145\162\x74\x69\146\151\x63\141\x74\145\x20\x73\165\143\143\145\x73\x73\x66\165\154\154\171\56");
        $this->mo_saml_show_success_message();
        Ks:
        goto Hm;
        iT:
        if (!@openssl_x509_read($_POST["\163\141\155\154\137\160\x75\142\154\x69\x63\137\170\65\x30\x39\137\143\x65\x72\x74\151\146\x69\143\x61\164\145"])) {
            goto yB;
        }
        if (!@openssl_x509_check_private_key($_POST["\163\141\x6d\154\137\160\165\142\154\151\143\137\x78\x35\60\x39\x5f\x63\x65\162\x74\x69\x66\x69\x63\141\x74\145"], $_POST["\163\x61\x6d\154\x5f\160\162\151\x76\x61\164\145\137\x78\65\60\71\137\x63\145\x72\164\151\x66\x69\143\x61\164\x65"])) {
            goto Vz;
        }
        if (openssl_x509_read($_POST["\163\x61\155\154\137\x70\165\x62\x6c\x69\143\x5f\170\x35\x30\71\x5f\x63\x65\x72\164\x69\146\151\x63\x61\164\145"]) && openssl_x509_check_private_key($_POST["\163\141\155\154\x5f\160\x75\142\154\x69\143\137\x78\65\60\71\137\x63\145\x72\164\x69\x66\151\x63\x61\164\145"], $_POST["\x73\x61\155\x6c\137\x70\162\x69\x76\x61\x74\145\x5f\170\x35\60\71\x5f\143\145\x72\164\x69\x66\151\x63\141\x74\145"])) {
            goto X9;
        }
        goto YU;
        yB:
        update_site_option("\155\x6f\x5f\163\x61\x6d\x6c\137\x6d\145\x73\163\x61\147\145", "\x49\x6e\x76\141\154\x69\144\x20\x43\145\162\x74\151\146\x69\x63\x61\164\145\40\x66\157\x72\x6d\x61\x74\56\40\x50\x6c\x65\141\163\145\x20\x65\156\x74\145\162\40\141\x20\166\141\x6c\151\144\x20\143\145\162\164\x69\x66\x69\143\x61\164\145\x2e");
        $this->mo_saml_show_error_message();
        return;
        goto YU;
        Vz:
        update_site_option("\155\x6f\x5f\x73\141\x6d\154\137\x6d\145\x73\x73\141\x67\145", "\x49\x6e\166\x61\154\151\x64\40\x50\x72\x69\x76\x61\164\x65\x20\x4b\x65\171\x2e");
        $this->mo_saml_show_error_message();
        return;
        goto YU;
        X9:
        $gw = $_POST["\163\x61\x6d\x6c\137\160\165\142\x6c\151\143\137\x78\65\x30\71\137\143\145\162\x74\151\146\x69\x63\141\164\145"];
        $Y0 = $_POST["\163\141\x6d\154\137\x70\162\x69\x76\141\164\145\x5f\170\x35\60\71\137\x63\145\x72\164\x69\146\x69\x63\x61\x74\145"];
        update_site_option("\155\157\137\x73\141\155\154\x5f\x63\x75\163\x74\157\155\x5f\143\145\x72\x74", $gw);
        update_site_option("\x6d\x6f\137\163\x61\155\x6c\137\143\x75\x73\x74\x6f\x6d\x5f\143\x65\x72\x74\x5f\x70\162\x69\166\x61\164\x65\137\153\x65\171", $Y0);
        update_site_option("\x6d\x6f\x5f\x73\141\x6d\154\x5f\143\x75\162\x72\x65\x6e\x74\x5f\x63\x65\x72\164", $gw);
        update_site_option("\155\x6f\137\x73\x61\x6d\x6c\x5f\x63\165\162\162\145\156\x74\137\143\145\162\164\x5f\x70\x72\x69\x76\141\164\145\137\x6b\x65\x79", $Y0);
        update_site_option("\x6d\x6f\x5f\163\141\x6d\154\137\155\145\x73\163\141\x67\x65", "\x43\165\x73\164\157\155\x20\103\145\162\164\151\146\151\143\141\x74\145\40\165\160\144\141\164\x65\x64\40\x73\x75\x63\143\x65\163\163\146\x75\x6c\x6c\171\56");
        $this->mo_saml_show_success_message();
        YU:
        Hm:
        rS:
        if (!self::mo_check_option_admin_referer("\x6d\141\x6e\x61\147\x65\x5f\163\x73\157\137\163\x69\x74\145\x73")) {
            goto gS;
        }
        $mM = Utilities::get_sites();
        $gn = Utilities::get_active_sites();
        $R1 = false;
        $US = false;
        if (isset($_POST["\145\x6e\141\142\x6c\x65\x41\154\x6c"]) and $_POST["\x65\x6e\x61\142\154\145\101\154\154"] == "\164\162\x75\145") {
            goto pF;
        }
        if (!(isset($_POST["\144\x69\x73\x61\x62\x6c\x65\101\154\x6c"]) and $_POST["\144\151\x73\141\x62\x6c\x65\x41\154\154"] == "\164\x72\x75\145")) {
            goto Uk;
        }
        $US = true;
        Uk:
        goto p4;
        pF:
        $R1 = true;
        p4:
        foreach ($mM as $NU) {
            $iy = $NU->blog_id;
            if ($R1) {
                goto RN;
            }
            if ($US) {
                goto jL;
            }
            if (!isset($_POST[$iy])) {
                goto tU;
            }
            if ($_POST[$iy] == "\146\x61\154\163\x65") {
                goto Cx;
            }
            if ($_POST[$iy] == "\164\162\x75\x65") {
                goto EO;
            }
            goto kQ;
            Cx:
            $Z1 = array_search($iy, $gn);
            if (!($Z1 !== false)) {
                goto wD;
            }
            unset($gn[$Z1]);
            wD:
            goto kQ;
            EO:
            if (in_array($iy, $gn)) {
                goto eb;
            }
            array_push($gn, $iy);
            eb:
            kQ:
            tU:
            goto WJ;
            RN:
            if (in_array($iy, $gn)) {
                goto T4;
            }
            array_push($gn, $iy);
            T4:
            goto WJ;
            jL:
            $Z1 = array_search($iy, $gn);
            if (!($Z1 !== false)) {
                goto a4;
            }
            unset($gn[$Z1]);
            a4:
            WJ:
            Sy:
        }
        uy:
        $Hd = Utilities::get_main_subsite_id();
        if (in_array($Hd, $gn)) {
            goto pI;
        }
        array_push($gn, $Hd);
        pI:
        update_site_option("\155\157\137\145\x6e\141\x62\x6c\x65\137\x73\x73\x6f\137\163\x69\x74\145\x73", $gn);
        gS:
        if (!self::mo_check_option_admin_referer("\x6d\157\137\x73\141\155\x6c\137\162\145\154\141\171\x5f\x73\x74\x61\x74\145\x5f\x6f\160\x74\151\157\x6e")) {
            goto IS;
        }
        $Hr = sanitize_text_field($_POST["\155\157\137\x73\x61\155\154\137\162\x65\154\141\x79\x5f\163\x74\x61\x74\x65"]);
        $pK = get_site_option("\163\141\x6d\154\x5f\x73\x73\157\137\163\x65\164\x74\x69\x6e\x67\163");
        if ($pK) {
            goto p8;
        }
        $pK = array();
        p8:
        $NU = "\104\x45\106\101\125\114\x54";
        if (!isset($_POST["\163\x69\x74\x65"])) {
            goto Ei;
        }
        $NU = htmlspecialchars($_POST["\x73\x69\x74\145"]);
        Ei:
        if (!(empty($pK[$NU]) && !empty($pK["\104\x45\x46\101\125\x4c\124"]))) {
            goto BJ;
        }
        $pK[$NU] = $pK["\x44\105\106\x41\125\114\124"];
        BJ:
        $pK[$NU]["\155\x6f\x5f\x73\x61\155\154\x5f\x72\145\154\x61\x79\x5f\163\x74\x61\164\145"] = $Hr;
        update_site_option("\163\x61\x6d\x6c\x5f\163\163\157\137\x73\145\x74\164\151\156\x67\x73", $pK);
        update_site_option("\155\x6f\137\x73\141\x6d\x6c\x5f\155\x65\163\163\x61\x67\x65", "\x52\x65\x6c\x61\x79\x20\x53\164\141\164\x65\x20\165\160\x64\x61\x74\145\x64\40\163\165\x63\143\x65\163\x73\146\x75\154\x6c\171\x2e");
        $this->mo_saml_show_success_message();
        IS:
        if (!self::mo_check_option_admin_referer("\x6d\x6f\x5f\x73\141\155\x6c\137\167\151\x64\147\145\x74\x5f\x6f\x70\164\x69\x6f\156")) {
            goto d1;
        }
        $pK = get_site_option("\163\141\155\x6c\137\163\x73\157\137\163\145\x74\164\x69\x6e\147\163");
        if ($pK) {
            goto hC;
        }
        $pK = array();
        hC:
        $NU = "\104\105\106\101\125\114\x54";
        if (!isset($_POST["\163\x69\x74\x65"])) {
            goto Cw;
        }
        $NU = htmlspecialchars($_POST["\x73\151\164\x65"]);
        Cw:
        if (!(empty($pK[$NU]) && !empty($pK["\x44\x45\x46\x41\x55\x4c\x54"]))) {
            goto Kt;
        }
        $pK[$NU] = $pK["\x44\x45\x46\101\125\114\124"];
        Kt:
        $w3 = sanitize_text_field($_POST["\155\x6f\x5f\x73\x61\155\x6c\x5f\143\165\x73\164\x6f\155\x5f\154\x6f\x67\151\156\137\x74\145\x78\164"]);
        $pK[$NU]["\155\x6f\137\163\x61\155\x6c\137\x63\165\x73\164\x6f\155\x5f\154\157\x67\151\156\137\164\145\x78\164"] = stripcslashes($w3);
        $e2 = sanitize_text_field($_POST["\155\x6f\137\163\x61\x6d\154\x5f\143\x75\163\164\x6f\155\137\147\162\145\145\x74\151\x6e\147\x5f\x74\x65\170\x74"]);
        $pK[$NU]["\x6d\157\x5f\163\141\155\154\x5f\x63\x75\163\164\157\155\137\147\x72\145\145\x74\x69\156\x67\137\x74\x65\170\x74"] = stripcslashes($e2);
        $pw = htmlspecialchars(stripslashes($_POST["\155\157\x5f\x73\141\x6d\x6c\137\x67\162\x65\145\164\x69\156\147\137\x6e\x61\155\145"]));
        $pK[$NU]["\x6d\157\x5f\x73\x61\155\x6c\137\x67\162\x65\x65\x74\151\156\147\137\x6e\141\x6d\145"] = $pw;
        $b8 = sanitize_text_field($_POST["\155\x6f\137\x73\x61\x6d\x6c\x5f\x63\x75\x73\164\x6f\155\x5f\x6c\x6f\x67\x6f\165\164\137\164\145\x78\x74"]);
        $pK[$NU]["\155\157\x5f\x73\x61\155\x6c\x5f\x63\165\163\164\157\x6d\x5f\154\157\147\157\x75\x74\x5f\164\145\170\x74"] = stripcslashes($b8);
        update_site_option("\x73\x61\x6d\154\137\x73\163\157\137\x73\x65\x74\x74\151\156\x67\163", $pK);
        update_site_option("\x6d\157\137\x73\x61\155\x6c\x5f\x6d\145\163\163\141\x67\x65", "\x57\151\144\147\x65\x74\x20\123\145\x74\164\151\156\x67\x73\x20\165\160\x64\141\x74\x65\144\40\163\165\143\x63\x65\163\x73\146\165\x6c\154\171\x2e");
        $this->mo_saml_show_success_message();
        d1:
        if (!self::mo_check_option_admin_referer("\163\x73\x6f\137\163\145\164\x74\x69\x6e\147\x73\137\163\x69\x74\145\137\x6f\x70\164\x69\x6f\156")) {
            goto h3;
        }
        if (!isset($_POST["\x73\x73\157\x5f\x73\x65\x74\164\x69\156\147\x73\137\163\x69\x74\145"])) {
            goto g9;
        }
        update_site_option("\x73\163\x6f\x5f\x73\x65\164\164\151\156\x67\x73\137\163\151\x74\145", htmlspecialchars($_POST["\x73\163\157\137\x73\145\164\x74\151\x6e\x67\x73\x5f\163\x69\x74\x65"]));
        g9:
        h3:
        if (!self::mo_check_option_admin_referer("\x6d\x6f\x5f\163\141\155\154\x5f\162\145\147\151\x73\164\145\x72\137\143\x75\x73\x74\157\155\x65\162")) {
            goto iM;
        }
        if (mo_saml_is_extension_installed("\143\165\162\154")) {
            goto HC;
        }
        update_site_option("\x6d\157\x5f\163\x61\x6d\154\x5f\x6d\145\163\x73\x61\147\145", "\105\122\122\x4f\x52\x3a\40\74\141\x20\x68\x72\x65\146\x3d\42\x68\x74\164\160\72\57\57\x70\x68\x70\x2e\156\x65\164\57\x6d\141\156\x75\x61\154\x2f\x65\156\x2f\x63\165\162\x6c\x2e\x69\156\163\164\x61\154\154\x61\x74\151\157\x6e\56\160\150\x70\x22\x20\x74\x61\162\x67\x65\x74\75\x22\x5f\x62\154\x61\156\153\42\x3e\120\x48\x50\40\143\125\122\x4c\x20\x65\170\164\x65\x6e\x73\x69\x6f\156\74\57\141\x3e\40\x69\163\x20\x6e\157\164\40\x69\156\163\x74\141\154\154\x65\x64\x20\157\162\40\144\151\163\x61\142\154\x65\144\56\x20\122\145\x67\x69\163\x74\162\141\x74\x69\157\156\x20\146\141\151\154\x65\144\x2e");
        $this->mo_saml_show_error_message();
        return;
        HC:
        $sf = '';
        $BH = '';
        $sG = '';
        $gD = '';
        if ($this->mo_saml_check_empty_or_null($_POST["\x65\x6d\x61\x69\154"]) || $this->mo_saml_check_empty_or_null($_POST["\160\141\x73\x73\x77\157\x72\144"]) || $this->mo_saml_check_empty_or_null($_POST["\143\x6f\x6e\x66\x69\x72\x6d\120\141\163\x73\x77\157\162\x64"])) {
            goto xa;
        }
        if (strlen($_POST["\160\141\163\x73\x77\157\162\144"]) < 6 || strlen($_POST["\x63\x6f\156\x66\x69\162\x6d\120\x61\163\x73\x77\x6f\162\x64"]) < 6) {
            goto vb;
        }
        if ($this->checkPasswordPattern(strip_tags($_POST["\x70\x61\163\x73\x77\x6f\x72\144"]))) {
            goto El;
        }
        $sf = sanitize_email($_POST["\145\x6d\141\x69\154"]);
        if (!array_key_exists("\160\x68\157\156\x65", $_POST)) {
            goto ul;
        }
        $BH = sanitize_text_field($_POST["\x70\x68\x6f\156\x65"]);
        ul:
        $sG = stripslashes(strip_tags($_POST["\160\141\x73\163\x77\157\162\x64"]));
        $gD = stripslashes(strip_tags($_POST["\x63\157\x6e\x66\151\162\x6d\x50\141\163\163\x77\157\162\144"]));
        goto q_;
        El:
        update_site_option("\x6d\157\x5f\x73\141\x6d\154\x5f\155\x65\163\x73\141\147\145", "\115\151\x6e\151\x6d\x75\155\x20\x36\40\x63\x68\x61\x72\x61\x63\x74\145\x72\x73\x20\x73\150\157\165\154\144\x20\142\145\x20\x70\x72\x65\x73\x65\x6e\164\56\x20\115\x61\170\x69\x6d\x75\155\x20\x31\65\x20\143\x68\141\162\x61\x63\x74\x65\162\163\40\163\150\157\x75\154\144\x20\142\145\40\160\x72\x65\x73\x65\x6e\164\56\40\x4f\156\154\x79\40\146\x6f\154\154\157\x77\x69\156\x67\x20\163\171\x6d\142\157\x6c\x73\x20\x28\x21\100\43\x2e\x24\45\136\46\x2a\x2d\x5f\x29\40\163\x68\x6f\165\154\144\40\142\145\40\x70\x72\x65\163\145\156\x74\x2e");
        $this->mo_saml_show_error_message();
        return;
        q_:
        goto KD;
        vb:
        update_site_option("\155\x6f\x5f\163\x61\155\154\x5f\155\x65\x73\163\x61\147\145", "\x43\x68\x6f\x6f\x73\145\40\141\40\x70\x61\x73\163\x77\x6f\x72\144\40\167\x69\x74\x68\x20\155\151\156\151\x6d\x75\x6d\x20\x6c\x65\156\x67\x74\150\40\66\x2e");
        $this->mo_saml_show_error_message();
        return;
        KD:
        goto wC;
        xa:
        update_site_option("\x6d\x6f\x5f\163\x61\x6d\154\x5f\155\145\163\x73\141\147\145", "\x41\154\x6c\x20\164\x68\x65\40\146\151\x65\x6c\144\163\x20\x61\162\x65\x20\x72\145\x71\165\x69\x72\145\x64\56\40\120\154\145\x61\x73\x65\40\145\156\164\x65\162\40\x76\x61\x6c\151\x64\x20\145\x6e\x74\162\151\x65\163\x2e");
        $this->mo_saml_show_error_message();
        return;
        wC:
        update_site_option("\155\157\137\x73\141\155\154\137\141\144\155\x69\x6e\x5f\x65\x6d\141\151\x6c", $sf);
        update_site_option("\x6d\157\x5f\x73\141\x6d\x6c\x5f\x61\144\x6d\151\x6e\137\160\x68\x6f\x6e\x65", $BH);
        if (strcmp($sG, $gD) == 0) {
            goto k5;
        }
        update_site_option("\155\157\137\163\x61\155\154\x5f\x6d\x65\163\x73\x61\147\145", "\120\141\163\x73\167\x6f\x72\144\x73\40\x64\x6f\40\156\x6f\x74\x20\x6d\x61\x74\143\150\56");
        delete_site_option("\x6d\157\137\163\141\x6d\x6c\137\166\145\162\x69\146\171\x5f\x63\x75\163\164\157\x6d\145\162");
        $this->mo_saml_show_error_message();
        goto x5;
        k5:
        update_site_option("\x6d\157\137\x73\x61\x6d\x6c\137\x61\144\155\x69\156\x5f\160\141\163\x73\x77\x6f\x72\144", $sG);
        $sf = get_site_option("\x6d\157\x5f\163\141\155\x6c\137\x61\144\x6d\151\x6e\x5f\145\x6d\x61\151\x6c");
        $EF = new CustomerSaml();
        $b1 = $EF->check_customer($this);
        if ($b1) {
            goto Dx;
        }
        return;
        Dx:
        $Zg = json_decode($b1, true);
        if (strcasecmp($Zg["\163\164\x61\164\165\x73"], "\103\x55\123\x54\117\115\x45\122\x5f\116\x4f\124\x5f\x46\x4f\x55\116\104") == 0) {
            goto Yb;
        }
        $this->get_current_customer();
        goto pc;
        Yb:
        $Zg = json_decode($EF->send_otp_token($sf, '', $this), true);
        if (strcasecmp($Zg["\163\164\141\x74\x75\163"], "\123\125\103\103\105\x53\123") == 0) {
            goto lu;
        }
        update_site_option("\x6d\x6f\x5f\163\141\x6d\x6c\137\x6d\145\163\x73\x61\147\145", "\124\150\x65\x72\145\40\167\x61\163\x20\141\156\40\145\x72\162\157\162\x20\151\156\x20\163\x65\x6e\x64\151\156\147\x20\145\x6d\141\151\x6c\56\x20\x50\x6c\x65\141\163\145\40\x76\x65\x72\x69\146\171\40\171\157\165\x72\40\145\x6d\141\151\154\40\141\x6e\144\x20\164\162\x79\x20\x61\x67\x61\x69\156\56");
        update_site_option("\155\x6f\x5f\x73\141\155\154\x5f\x72\x65\147\x69\163\x74\162\x61\x74\x69\x6f\156\x5f\x73\164\x61\x74\x75\x73", "\x4d\117\137\117\x54\120\137\x44\105\114\111\x56\x45\122\x45\104\x5f\x46\101\x49\114\125\x52\105\137\x45\115\101\111\x4c");
        $this->mo_saml_show_error_message();
        goto FX;
        lu:
        update_site_option("\155\x6f\137\163\141\155\154\x5f\155\145\x73\x73\x61\x67\x65", "\40\x41\40\x6f\x6e\145\40\164\x69\x6d\x65\40\x70\x61\x73\x73\x63\157\x64\145\x20\x69\163\x20\x73\145\x6e\164\40\x74\x6f\40" . get_site_option("\x6d\157\137\163\x61\155\x6c\137\x61\144\155\151\156\137\x65\155\141\x69\154") . "\x2e\x20\120\154\145\x61\x73\x65\x20\x65\x6e\x74\x65\x72\x20\x74\x68\x65\x20\x6f\164\x70\x20\150\x65\x72\145\x20\x74\x6f\x20\x76\145\x72\x69\x66\x79\40\x79\x6f\165\162\40\145\x6d\141\x69\154\x2e");
        update_site_option("\155\x6f\x5f\163\141\x6d\x6c\x5f\x74\162\x61\156\163\141\x63\164\151\x6f\156\x49\x64", $Zg["\164\170\x49\x64"]);
        update_site_option("\x6d\x6f\x5f\x73\x61\155\154\137\x72\145\147\151\163\x74\162\x61\164\151\x6f\x6e\x5f\x73\x74\x61\x74\x75\163", "\x4d\x4f\x5f\117\x54\120\137\x44\x45\114\x49\x56\105\122\105\x44\x5f\x53\x55\103\103\105\x53\123\x5f\x45\115\101\111\x4c");
        $this->mo_saml_show_success_message();
        FX:
        pc:
        x5:
        iM:
        if (!self::mo_check_option_admin_referer("\x6d\x6f\137\x73\141\x6d\154\x5f\x76\141\154\151\144\x61\164\x65\x5f\x6f\164\160")) {
            goto lE;
        }
        if (mo_saml_is_extension_installed("\143\165\x72\x6c")) {
            goto XT;
        }
        update_site_option("\x6d\157\137\x73\x61\155\154\137\x6d\145\x73\163\141\x67\x65", "\105\122\122\117\122\x3a\40\x3c\x61\40\x68\x72\x65\x66\75\x22\150\164\164\x70\x3a\57\57\160\x68\160\56\156\x65\x74\x2f\x6d\141\x6e\x75\141\x6c\x2f\145\156\57\143\x75\162\x6c\x2e\151\156\163\164\x61\x6c\x6c\x61\x74\x69\157\156\x2e\x70\x68\x70\42\x20\164\x61\x72\x67\x65\164\75\42\137\142\x6c\141\x6e\153\42\x3e\120\110\120\x20\x63\x55\122\114\40\145\170\x74\145\156\x73\x69\x6f\x6e\74\57\x61\76\40\151\163\x20\156\157\x74\x20\x69\156\x73\x74\x61\x6c\154\145\144\40\157\x72\40\144\151\163\141\x62\x6c\x65\144\x2e\x20\126\x61\x6c\x69\x64\141\164\x65\40\x4f\x54\x50\x20\146\x61\151\154\x65\x64\x2e");
        $this->mo_saml_show_error_message();
        return;
        XT:
        $xC = '';
        if ($this->mo_saml_check_empty_or_null($_POST["\157\164\160\137\x74\x6f\153\x65\x6e"])) {
            goto Zp;
        }
        $xC = sanitize_text_field($_POST["\157\x74\160\x5f\164\x6f\x6b\145\156"]);
        goto w2;
        Zp:
        update_site_option("\155\157\x5f\163\x61\x6d\x6c\x5f\155\145\163\163\141\147\x65", "\x50\x6c\145\x61\163\x65\x20\x65\156\x74\145\x72\x20\x61\40\x76\x61\x6c\x75\x65\40\151\156\x20\x6f\x74\x70\x20\x66\x69\145\154\x64\56");
        $this->mo_saml_show_error_message();
        return;
        w2:
        $EF = new CustomerSaml();
        $Zg = json_decode($EF->validate_otp_token(get_site_option("\x6d\x6f\x5f\163\141\x6d\154\137\164\162\141\156\x73\141\143\x74\151\157\156\x49\x64"), $xC, $this), true);
        if (strcasecmp($Zg["\x73\164\x61\164\x75\163"], "\x53\125\103\x43\105\x53\123") == 0) {
            goto Pq;
        }
        update_site_option("\x6d\x6f\x5f\163\141\x6d\154\137\155\145\x73\163\141\x67\x65", "\x49\156\166\x61\x6c\151\x64\40\x6f\x6e\145\x20\x74\x69\155\145\40\160\141\x73\163\x63\157\144\x65\56\40\120\154\145\x61\163\145\40\x65\x6e\x74\145\x72\x20\141\40\x76\x61\154\151\x64\x20\157\x74\x70\56");
        $this->mo_saml_show_error_message();
        goto R5;
        Pq:
        $this->create_customer();
        R5:
        lE:
        if (self::mo_check_option_admin_referer("\155\x6f\137\x73\141\155\x6c\x5f\166\145\x72\x69\146\x79\x5f\143\165\x73\164\x6f\x6d\145\x72")) {
            goto Xg;
        }
        if (self::mo_check_option_admin_referer("\x6d\157\x5f\x73\x61\x6d\154\137\x63\x6f\156\164\x61\143\x74\137\x75\x73\x5f\161\x75\x65\x72\171\137\x6f\x70\x74\151\x6f\156")) {
            goto eo;
        }
        if (self::mo_check_option_admin_referer("\x6d\157\137\163\141\x6d\x6c\x5f\162\x65\x73\x65\156\x64\x5f\x6f\x74\x70\137\x65\155\141\x69\154")) {
            goto WF;
        }
        if (self::mo_check_option_admin_referer("\x6d\x6f\x5f\x73\x61\155\154\x5f\162\x65\163\145\156\x64\137\x6f\x74\160\137\x70\x68\157\x6e\x65")) {
            goto Ep;
        }
        if (self::mo_check_option_admin_referer("\x6d\x6f\137\163\141\x6d\x6c\x5f\147\157\x5f\x62\x61\x63\153")) {
            goto c3;
        }
        if (self::mo_check_option_admin_referer("\155\157\137\163\x61\155\x6c\137\x72\145\147\151\163\164\145\162\137\167\151\x74\x68\x5f\160\x68\x6f\156\145\137\157\x70\164\x69\x6f\x6e")) {
            goto N6;
        }
        if (self::mo_check_option_admin_referer("\x6d\157\137\x73\x61\x6d\154\137\162\145\x67\x69\x73\164\x65\162\x65\x64\x5f\157\x6e\154\171\137\141\x63\143\x65\163\x73\137\x6f\160\x74\151\x6f\x6e")) {
            goto Th;
        }
        if (self::mo_check_option_admin_referer("\155\157\137\x73\x61\155\154\x5f\163\x75\x62\163\x69\x74\145\137\141\x63\143\145\x73\x73\137\x64\145\156\151\x65\144\137\x6f\160\x74\151\157\x6e")) {
            goto L1;
        }
        if (self::mo_check_option_admin_referer("\155\x6f\x5f\x73\141\x6d\x6c\137\146\x6f\162\x63\x65\137\x61\165\x74\150\145\x6e\164\151\143\141\x74\x69\157\156\x5f\157\x70\164\x69\157\x6e")) {
            goto X_;
        }
        if (self::mo_check_option_admin_referer("\x6d\x6f\137\163\141\x6d\x6c\x5f\x65\x6e\x61\x62\x6c\145\x5f\154\x6f\147\x69\x6e\x5f\162\x65\x64\x69\x72\x65\x63\x74\137\x6f\160\164\x69\157\156")) {
            goto dt;
        }
        if (self::mo_check_option_admin_referer("\155\157\137\163\x61\x6d\x6c\x5f\x72\x65\144\151\x72\145\x63\164\137\x74\157\137\167\160\137\154\157\x67\151\156\137\x6f\x70\x74\151\x6f\x6e")) {
            goto ca;
        }
        if (self::mo_check_option_admin_referer("\x6d\157\x5f\163\x61\x6d\x6c\137\x61\144\144\137\x73\163\157\137\x62\x75\x74\164\157\x6e\137\x77\x70\x5f\x6f\x70\x74\151\157\156")) {
            goto u4;
        }
        if (self::mo_check_option_admin_referer("\155\157\x5f\x73\141\x6d\x6c\137\165\163\x65\x5f\142\165\164\164\157\x6e\137\x61\x73\137\163\x68\157\162\x74\143\x6f\144\145\137\x6f\x70\164\151\x6f\156")) {
            goto it;
        }
        if (self::mo_check_option_admin_referer("\x6d\157\x5f\163\141\155\154\137\x75\163\x65\137\x62\165\164\164\157\x6e\137\x61\x73\x5f\x77\151\144\x67\145\164\x5f\x6f\160\164\151\157\156")) {
            goto Ed;
        }
        if (self::mo_check_option_admin_referer("\x6d\157\137\x73\141\x6d\x6c\x5f\143\x75\163\x74\157\x6d\137\142\x75\x74\x74\x6f\156\x5f\x6f\160\164\x69\x6f\x6e")) {
            goto nF;
        }
        if (self::mo_check_option_admin_referer("\x6d\x6f\137\x73\141\155\x6c\x5f\x61\154\154\157\167\137\167\160\x5f\x73\x69\147\156\151\x6e\x5f\157\x70\x74\151\157\x6e")) {
            goto Nk;
        }
        if (!self::mo_check_option_admin_referer("\x6d\x6f\137\163\x61\155\154\137\146\x6f\x72\x67\x6f\164\137\x70\x61\x73\x73\x77\x6f\x72\144\137\x66\157\162\x6d\x5f\157\x70\164\151\x6f\156")) {
            goto P7;
        }
        if (mo_saml_is_extension_installed("\x63\165\x72\x6c")) {
            goto D1;
        }
        update_site_option("\155\157\x5f\x73\141\155\154\137\x6d\x65\x73\163\141\x67\145", "\105\x52\x52\x4f\122\x3a\x20\74\141\40\150\162\145\146\x3d\42\x68\164\164\160\x3a\x2f\57\x70\x68\160\x2e\x6e\x65\x74\57\155\141\x6e\x75\141\154\57\x65\x6e\x2f\x63\x75\x72\x6c\x2e\151\156\x73\164\141\x6c\x6c\141\x74\x69\x6f\x6e\56\160\x68\x70\42\40\164\141\x72\147\x65\x74\75\x22\x5f\x62\154\x61\x6e\x6b\42\x3e\120\110\x50\40\143\x55\x52\x4c\x20\x65\170\x74\145\x6e\163\x69\157\x6e\x3c\57\141\76\x20\x69\x73\x20\x6e\157\x74\x20\151\x6e\163\164\141\154\x6c\x65\x64\x20\x6f\162\40\x64\x69\x73\x61\142\x6c\x65\144\x2e\40\122\145\163\x65\156\x64\x20\117\x54\120\x20\146\x61\x69\x6c\145\x64\x2e");
        $this->mo_saml_show_error_message();
        return;
        D1:
        $sf = get_site_option("\x6d\x6f\137\x73\x61\x6d\154\x5f\141\144\155\151\x6e\137\145\x6d\141\x69\x6c");
        $EF = new Customersaml();
        $Zg = json_decode($EF->mo_saml_forgot_password($sf, $this), true);
        if (strcasecmp($Zg["\x73\164\141\164\165\163"], "\x53\x55\x43\x43\x45\x53\123") == 0) {
            goto r0;
        }
        update_site_option("\155\157\137\x73\141\155\x6c\x5f\155\x65\163\163\x61\x67\x65", "\x41\x6e\40\145\x72\x72\x6f\x72\x20\x6f\x63\143\x75\x72\145\144\40\x77\150\151\154\x65\x20\x70\162\x6f\143\x65\x73\x73\x69\156\147\x20\171\x6f\x75\162\40\162\x65\161\x75\145\x73\164\x2e\40\x50\x6c\x65\x61\163\x65\40\x54\x72\x79\40\x61\147\x61\x69\x6e\56");
        $this->mo_saml_show_error_message();
        goto N1;
        r0:
        update_site_option("\x6d\157\137\163\141\x6d\x6c\137\155\145\x73\x73\x61\x67\x65", "\x59\x6f\165\x72\x20\x70\141\163\x73\x77\x6f\x72\144\40\x68\141\163\40\x62\x65\145\156\x20\162\145\163\x65\x74\x20\x73\165\143\x63\145\163\x73\146\165\154\154\171\56\x20\120\154\145\141\163\x65\40\x65\156\164\x65\x72\40\x74\150\x65\40\156\x65\x77\x20\x70\141\163\x73\167\x6f\162\x64\40\163\145\x6e\164\x20\x74\x6f\x20" . $sf . "\56");
        $this->mo_saml_show_success_message();
        N1:
        P7:
        goto VG;
        Nk:
        $pK = get_site_option("\163\141\x6d\154\x5f\163\163\x6f\x5f\x73\x65\x74\x74\151\x6e\147\163");
        if (!empty($pK)) {
            goto Zn;
        }
        $pK = array();
        Zn:
        $NU = "\x44\105\x46\x41\x55\114\x54";
        if (!isset($_POST["\x73\151\164\x65"])) {
            goto DW;
        }
        $NU = htmlspecialchars($_POST["\x73\151\x74\145"]);
        DW:
        if (!(empty($pK[$NU]) && !empty($pK["\x44\105\x46\x41\x55\x4c\124"]))) {
            goto sj;
        }
        $pK[$NU] = $pK["\104\x45\x46\x41\125\x4c\124"];
        sj:
        $Xc = "\x66\x61\x6c\x73\x65";
        if (array_key_exists("\155\x6f\x5f\x73\x61\x6d\154\137\x61\154\x6c\x6f\x77\137\167\160\x5f\163\x69\147\x6e\151\x6e", $_POST)) {
            goto Lb;
        }
        $Eo = "\x66\x61\154\x73\145";
        goto iu;
        Lb:
        $Eo = htmlspecialchars($_POST["\x6d\157\137\x73\x61\155\154\x5f\x61\x6c\x6c\x6f\x77\137\x77\x70\137\163\x69\147\x6e\x69\x6e"]);
        iu:
        if ($Eo == "\164\162\x75\x65") {
            goto M6;
        }
        $pK[$NU]["\x6d\157\137\x73\141\x6d\154\x5f\x61\154\154\157\167\x5f\x77\160\x5f\x73\151\147\156\x69\x6e"] = '';
        goto bj;
        M6:
        $pK[$NU]["\155\157\137\x73\x61\155\154\x5f\141\x6c\x6c\157\x77\137\x77\x70\x5f\163\x69\147\156\151\156"] = "\164\162\165\145";
        if (!array_key_exists("\x6d\x6f\137\x73\141\155\154\x5f\x62\141\x63\x6b\144\x6f\157\162\x5f\165\x72\x6c", $_POST)) {
            goto iU;
        }
        $Xc = htmlspecialchars(trim($_POST["\x6d\157\137\x73\141\x6d\x6c\x5f\142\x61\143\x6b\x64\157\x6f\162\x5f\165\x72\x6c"]));
        iU:
        bj:
        $pK[$NU]["\155\x6f\137\163\x61\155\x6c\x5f\x62\141\143\153\144\x6f\157\162\x5f\x75\x72\154"] = $Xc;
        update_site_option("\x73\x61\x6d\154\137\x73\x73\157\x5f\163\x65\x74\164\151\156\147\x73", $pK);
        update_site_option("\x6d\x6f\137\x73\x61\x6d\154\x5f\155\x65\163\163\x61\147\x65", "\123\151\147\156\40\x49\x6e\40\x73\145\x74\x74\151\x6e\147\x73\x20\x75\x70\144\x61\164\x65\144\56");
        $this->mo_saml_show_success_message();
        VG:
        goto D5;
        nF:
        $pK = get_site_option("\163\x61\x6d\x6c\x5f\x73\163\157\137\x73\x65\x74\x74\x69\156\147\163");
        if ($pK) {
            goto Me;
        }
        $pK = array();
        Me:
        $NU = "\104\105\106\x41\125\x4c\124";
        if (!isset($_POST["\x73\151\164\x65"])) {
            goto pt;
        }
        $NU = htmlspecialchars($_POST["\163\x69\x74\x65"]);
        pt:
        if (!(empty($pK[$NU]) && !empty($pK["\x44\105\x46\x41\125\114\x54"]))) {
            goto lO;
        }
        $pK[$NU] = $pK["\x44\105\x46\x41\125\x4c\x54"];
        lO:
        $f_ = '';
        $SJ = '';
        $LT = '';
        $KO = '';
        $cf = '';
        $rs = '';
        $te = '';
        $Xs = '';
        $Z0 = '';
        $zQ = '';
        $Qk = "\x61\142\157\166\x65";
        if (!(array_key_exists("\x6d\157\x5f\x73\x61\x6d\154\x5f\142\x75\164\164\157\156\137\x73\151\x7a\145", $_POST) && !empty($_POST["\x6d\x6f\x5f\x73\x61\155\154\137\x62\x75\164\164\x6f\156\137\163\151\x7a\x65"]))) {
            goto dH;
        }
        $LT = htmlspecialchars($_POST["\155\157\x5f\x73\141\155\154\137\x62\165\164\x74\x6f\156\137\x73\151\172\145"]);
        dH:
        if (!(array_key_exists("\155\157\x5f\163\x61\155\154\x5f\x62\x75\x74\164\x6f\x6e\137\167\x69\144\164\150", $_POST) && !empty($_POST["\155\157\137\x73\141\x6d\154\x5f\142\165\164\164\157\x6e\137\167\x69\x64\164\x68"]))) {
            goto yD;
        }
        $KO = htmlspecialchars($_POST["\155\x6f\x5f\163\141\x6d\x6c\x5f\142\x75\164\x74\x6f\156\137\167\x69\x64\x74\150"]);
        yD:
        if (!(array_key_exists("\x6d\157\137\x73\141\x6d\154\137\142\165\164\x74\157\156\x5f\x68\145\151\147\x68\164", $_POST) && !empty($_POST["\155\157\x5f\163\141\155\x6c\137\x62\x75\164\x74\x6f\156\137\150\145\151\147\x68\x74"]))) {
            goto XP;
        }
        $cf = htmlspecialchars($_POST["\x6d\157\x5f\163\x61\x6d\154\137\x62\x75\164\x74\x6f\156\137\150\145\151\147\150\164"]);
        XP:
        if (!(array_key_exists("\x6d\157\x5f\x73\141\155\x6c\137\x62\165\164\164\x6f\x6e\x5f\143\165\162\166\x65", $_POST) && !empty($_POST["\155\x6f\137\x73\x61\x6d\154\137\x62\x75\164\164\x6f\156\137\x63\165\x72\x76\x65"]))) {
            goto VJ;
        }
        $rs = htmlspecialchars($_POST["\155\x6f\x5f\x73\141\x6d\154\x5f\142\165\x74\x74\x6f\x6e\x5f\x63\165\162\x76\x65"]);
        VJ:
        if (!array_key_exists("\x6d\157\137\x73\141\155\154\137\x62\x75\164\164\x6f\x6e\137\x63\157\x6c\157\x72", $_POST)) {
            goto sw;
        }
        $te = htmlspecialchars($_POST["\x6d\157\x5f\163\x61\x6d\154\137\x62\165\x74\x74\x6f\x6e\137\143\x6f\154\x6f\x72"]);
        sw:
        if (!array_key_exists("\x6d\x6f\x5f\163\141\x6d\154\137\142\165\164\164\157\x6e\137\x74\150\x65\155\x65", $_POST)) {
            goto qo;
        }
        $f_ = htmlspecialchars($_POST["\x6d\157\x5f\x73\141\x6d\x6c\137\142\165\x74\164\x6f\156\137\x74\150\x65\155\x65"]);
        qo:
        if (!array_key_exists("\155\x6f\137\x73\141\x6d\x6c\x5f\x62\165\x74\x74\x6f\156\x5f\164\x65\x78\164", $_POST)) {
            goto d6;
        }
        $Xs = htmlspecialchars($_POST["\155\157\137\x73\x61\155\x6c\137\x62\x75\164\x74\157\x6e\137\x74\145\x78\x74"]);
        if (!(empty($Xs) || $Xs == "\43\x23\x49\x44\120\43\x23")) {
            goto NV;
        }
        $Xs = "\43\43\111\104\120\43\43";
        NV:
        $bj = get_site_option("\x73\x61\155\x6c\137\x69\x64\145\x6e\164\151\x74\171\x5f\156\141\155\x65");
        $Xs = str_replace("\43\x23\111\x44\120\43\43", $bj, $Xs);
        d6:
        if (!array_key_exists("\x6d\x6f\x5f\x73\x61\x6d\x6c\137\x66\157\156\164\137\x63\x6f\x6c\x6f\162", $_POST)) {
            goto Le;
        }
        $Z0 = htmlspecialchars($_POST["\x6d\x6f\x5f\163\x61\155\x6c\137\x66\x6f\x6e\164\x5f\x63\157\x6c\157\x72"]);
        Le:
        if (!array_key_exists("\155\157\137\x73\x61\x6d\154\x5f\146\x6f\156\164\x5f\x73\151\172\145", $_POST)) {
            goto fG;
        }
        $zQ = htmlspecialchars($_POST["\x6d\x6f\x5f\163\x61\155\x6c\137\146\x6f\x6e\x74\137\163\151\172\145"]);
        fG:
        if (!array_key_exists("\163\x73\157\x5f\x62\165\x74\164\157\156\137\154\x6f\147\151\156\137\146\157\x72\155\x5f\x70\157\163\x69\164\x69\x6f\x6e", $_POST)) {
            goto Nh;
        }
        $Qk = htmlspecialchars($_POST["\x73\x73\157\137\142\165\164\164\157\156\x5f\x6c\x6f\x67\x69\156\137\x66\x6f\x72\x6d\x5f\160\x6f\x73\x69\164\x69\157\156"]);
        Nh:
        $pK[$NU]["\x6d\157\x5f\x73\x61\x6d\x6c\137\x62\x75\164\x74\157\156\137\x74\150\x65\155\x65"] = $f_;
        $pK[$NU]["\155\x6f\x5f\163\141\x6d\154\137\142\165\x74\164\x6f\156\137\163\x69\x7a\145"] = $LT;
        $pK[$NU]["\155\157\x5f\x73\x61\155\154\137\142\x75\x74\164\157\156\x5f\167\151\144\164\x68"] = $KO;
        $pK[$NU]["\x6d\x6f\137\x73\x61\155\x6c\x5f\142\165\x74\x74\157\x6e\x5f\150\145\x69\x67\150\x74"] = $cf;
        $pK[$NU]["\x6d\157\x5f\x73\x61\155\x6c\x5f\x62\165\164\164\x6f\156\137\x63\x75\x72\x76\145"] = $rs;
        $pK[$NU]["\155\157\x5f\163\x61\x6d\154\x5f\x62\165\x74\164\x6f\156\x5f\143\157\x6c\x6f\x72"] = $te;
        $pK[$NU]["\x6d\157\137\163\x61\155\x6c\x5f\142\165\x74\x74\157\x6e\x5f\164\x65\x78\164"] = $Xs;
        $pK[$NU]["\x6d\157\x5f\x73\141\155\x6c\137\x66\157\156\x74\x5f\x63\x6f\x6c\157\162"] = $Z0;
        $pK[$NU]["\155\157\x5f\x73\x61\x6d\154\137\x66\x6f\x6e\x74\x5f\x73\x69\172\x65"] = $zQ;
        $pK[$NU]["\x73\163\x6f\x5f\x62\165\x74\164\x6f\x6e\137\154\x6f\x67\x69\x6e\137\x66\157\162\x6d\137\160\157\163\x69\164\151\157\156"] = $Qk;
        update_site_option("\163\x61\x6d\x6c\x5f\163\x73\x6f\x5f\163\145\164\x74\x69\x6e\147\163", $pK);
        update_site_option("\155\157\x5f\x73\141\x6d\x6c\137\155\x65\163\x73\x61\147\145", "\123\x69\147\156\x20\111\156\x20\163\x65\164\164\x69\156\147\163\40\x75\160\x64\x61\x74\x65\144\56");
        $this->mo_saml_show_success_message();
        D5:
        goto Qe;
        Ed:
        if (mo_saml_is_sp_configured()) {
            goto ZF;
        }
        update_site_option("\x6d\157\137\x73\x61\155\154\x5f\155\x65\x73\x73\141\147\145", "\x50\154\x65\141\x73\x65\40\143\157\155\160\154\x65\164\145\x20" . addLink("\x53\x65\162\x76\151\x63\x65\40\120\x72\x6f\x76\x69\x64\x65\x72", add_query_arg(array("\x74\x61\142" => "\163\141\166\x65"), $_SERVER["\122\105\x51\125\105\x53\124\137\x55\122\111"])) . "\x20\x63\157\x6e\146\151\147\165\x72\141\x74\151\x6f\x6e\x20\146\x69\162\x73\x74\56");
        $this->mo_saml_show_error_message();
        goto IU;
        ZF:
        $pK = get_site_option("\x73\x61\x6d\x6c\137\x73\x73\157\x5f\x73\145\164\x74\x69\156\x67\163");
        if ($pK) {
            goto Jk;
        }
        $pK = array();
        Jk:
        $NU = "\104\x45\x46\x41\125\x4c\124";
        if (!isset($_POST["\x73\151\x74\x65"])) {
            goto PO;
        }
        $NU = htmlspecialchars($_POST["\163\151\164\145"]);
        PO:
        if (!(empty($pK[$NU]) && !empty($pK["\104\105\106\x41\x55\114\x54"]))) {
            goto u8;
        }
        $pK[$NU] = $pK["\104\105\x46\x41\x55\x4c\124"];
        u8:
        if (array_key_exists("\x6d\157\137\163\x61\155\154\137\165\163\145\137\142\x75\164\164\x6f\x6e\137\141\163\x5f\167\x69\x64\x67\145\x74", $_POST)) {
            goto GO;
        }
        $HC = "\146\x61\154\x73\145";
        goto Fj;
        GO:
        $HC = htmlspecialchars($_POST["\155\157\137\x73\x61\155\154\x5f\165\x73\x65\x5f\x62\x75\164\164\157\156\137\141\x73\x5f\x77\x69\144\147\x65\x74"]);
        Fj:
        $pK[$NU]["\155\x6f\137\x73\x61\x6d\154\x5f\x75\x73\145\x5f\x62\165\x74\x74\157\x6e\137\141\x73\137\167\151\144\147\145\x74"] = $HC;
        update_site_option("\x73\x61\x6d\154\137\x73\x73\157\x5f\x73\145\x74\x74\x69\x6e\147\163", $pK);
        update_site_option("\155\157\x5f\163\x61\155\154\x5f\x6d\x65\163\163\x61\147\145", "\123\x69\147\156\x20\151\156\40\x6f\x70\x74\151\157\x6e\163\40\x75\160\x64\141\164\145\144\56");
        $this->mo_saml_show_success_message();
        IU:
        Qe:
        goto VR;
        it:
        if (mo_saml_is_sp_configured()) {
            goto kD;
        }
        update_site_option("\155\157\x5f\x73\141\155\154\137\x6d\x65\x73\163\141\147\145", "\x50\154\x65\x61\163\x65\x20\143\157\155\160\154\x65\x74\145\x20" . addLink("\123\x65\162\x76\151\x63\x65\40\x50\x72\x6f\166\x69\x64\145\x72", add_query_arg(array("\164\141\142" => "\163\141\166\x65"), $_SERVER["\122\x45\121\x55\105\x53\124\137\x55\x52\111"])) . "\40\x63\x6f\x6e\146\151\x67\165\162\x61\164\x69\157\156\40\x66\x69\x72\163\x74\56");
        $this->mo_saml_show_error_message();
        goto ix;
        kD:
        $pK = get_site_option("\x73\141\155\x6c\137\163\x73\157\137\163\145\164\x74\x69\156\x67\163");
        if ($pK) {
            goto f5;
        }
        $pK = array();
        f5:
        $NU = "\104\x45\x46\x41\125\x4c\x54";
        if (!isset($_POST["\163\151\x74\145"])) {
            goto gZ;
        }
        $NU = htmlspecialchars($_POST["\x73\x69\164\145"]);
        gZ:
        if (!(empty($pK[$NU]) && !empty($pK["\104\105\x46\x41\x55\114\124"]))) {
            goto Nj;
        }
        $pK[$NU] = $pK["\104\105\106\101\x55\114\124"];
        Nj:
        if (array_key_exists("\x6d\x6f\x5f\x73\x61\155\154\x5f\x75\x73\x65\137\142\165\x74\164\157\x6e\x5f\141\163\x5f\163\150\157\x72\164\143\x6f\144\145", $_POST)) {
            goto Fo;
        }
        $HC = "\x66\141\x6c\163\x65";
        goto b7;
        Fo:
        $HC = htmlspecialchars($_POST["\x6d\157\x5f\x73\141\155\154\x5f\x75\163\x65\x5f\x62\x75\x74\164\x6f\156\x5f\x61\163\137\x73\x68\x6f\162\x74\143\157\144\x65"]);
        b7:
        $pK[$NU]["\x6d\x6f\137\163\x61\x6d\154\137\165\163\145\137\x62\165\x74\164\x6f\156\137\x61\163\x5f\x73\150\157\162\164\x63\157\x64\145"] = $HC;
        update_site_option("\x73\141\155\x6c\x5f\163\163\157\x5f\x73\x65\x74\164\x69\x6e\x67\163", $pK);
        update_site_option("\x6d\x6f\137\x73\x61\155\154\137\x6d\x65\163\x73\141\147\145", "\x53\151\147\x6e\x20\x69\x6e\40\157\x70\164\151\x6f\156\x73\x20\x75\x70\x64\141\164\x65\144\56");
        $this->mo_saml_show_success_message();
        ix:
        VR:
        goto nk;
        u4:
        if (mo_saml_is_sp_configured()) {
            goto ms;
        }
        update_site_option("\155\157\x5f\163\x61\x6d\x6c\x5f\x6d\145\163\163\141\147\145", "\120\x6c\145\x61\163\145\x20\143\157\x6d\x70\154\145\x74\x65\x20" . addLink("\x53\145\x72\166\x69\143\x65\40\120\x72\157\166\x69\x64\x65\x72", add_query_arg(array("\x74\141\x62" => "\x73\x61\166\145"), $_SERVER["\122\105\121\x55\x45\123\x54\137\125\122\x49"])) . "\40\x63\157\156\146\x69\x67\x75\x72\x61\x74\x69\x6f\x6e\x20\x66\x69\x72\163\x74\x2e");
        $this->mo_saml_show_error_message();
        goto Mf;
        ms:
        $pK = get_site_option("\x73\141\155\154\x5f\163\163\x6f\x5f\x73\x65\164\164\x69\x6e\147\x73");
        if ($pK) {
            goto s9;
        }
        $pK = array();
        s9:
        $NU = "\104\105\106\x41\x55\114\124";
        if (!isset($_POST["\x73\151\164\145"])) {
            goto Bh;
        }
        $NU = htmlspecialchars($_POST["\163\151\x74\145"]);
        Bh:
        if (!(empty($pK[$NU]) && !empty($pK["\x44\x45\106\101\125\x4c\x54"]))) {
            goto B8;
        }
        $pK[$NU] = $pK["\x44\105\106\x41\x55\114\124"];
        B8:
        if (array_key_exists("\155\x6f\x5f\x73\141\x6d\x6c\x5f\141\144\144\x5f\x73\x73\x6f\x5f\142\x75\x74\x74\x6f\156\x5f\x77\x70", $_POST)) {
            goto m5;
        }
        $gx = "\x66\141\x6c\x73\145";
        goto Kl;
        m5:
        $gx = htmlspecialchars($_POST["\155\157\137\x73\x61\155\x6c\137\141\x64\144\x5f\x73\x73\x6f\137\142\165\164\x74\x6f\156\x5f\167\x70"]);
        Kl:
        $pK[$NU]["\155\x6f\x5f\163\x61\x6d\154\x5f\141\x64\x64\137\163\163\157\137\x62\x75\x74\164\157\156\x5f\167\160"] = $gx;
        update_site_option("\163\141\155\x6c\x5f\x73\163\157\x5f\x73\x65\x74\x74\151\x6e\x67\x73", $pK);
        update_site_option("\x6d\x6f\137\x73\x61\155\154\x5f\x6d\145\x73\163\x61\147\145", "\x53\151\x67\x6e\x20\151\x6e\40\x6f\160\164\151\x6f\156\163\40\165\160\x64\x61\164\x65\144\56");
        $this->mo_saml_show_success_message();
        Mf:
        nk:
        goto v8;
        ca:
        if (!mo_saml_is_sp_configured()) {
            goto ZU;
        }
        $pK = get_site_option("\163\141\155\x6c\x5f\163\x73\x6f\137\163\x65\164\x74\151\156\147\163");
        if ($pK) {
            goto SQ;
        }
        $pK = array();
        SQ:
        $NU = "\x44\105\106\101\x55\114\124";
        if (!isset($_POST["\163\151\164\x65"])) {
            goto ND;
        }
        $NU = htmlspecialchars($_POST["\163\151\164\x65"]);
        ND:
        if (!(empty($pK[$NU]) && !empty($pK["\x44\105\106\x41\x55\114\x54"]))) {
            goto p2;
        }
        $pK[$NU] = $pK["\104\105\106\x41\x55\x4c\x54"];
        p2:
        if (array_key_exists("\x6d\157\x5f\x73\x61\155\x6c\137\x72\145\x64\x69\162\145\143\x74\137\164\x6f\137\167\160\x5f\x6c\157\147\x69\x6e", $_POST)) {
            goto mS;
        }
        $t5 = "\146\x61\154\163\x65";
        goto rD;
        mS:
        $t5 = htmlspecialchars($_POST["\155\157\x5f\x73\141\x6d\154\137\162\145\144\151\162\x65\143\164\x5f\x74\x6f\x5f\x77\160\x5f\x6c\x6f\x67\151\x6e"]);
        rD:
        $pK[$NU]["\155\157\x5f\x73\x61\155\x6c\x5f\162\145\x64\x69\162\145\x63\x74\137\164\x6f\x5f\x77\160\137\154\157\x67\151\x6e"] = $t5;
        update_site_option("\163\141\155\x6c\x5f\163\163\x6f\137\163\x65\164\x74\151\x6e\x67\163", $pK);
        update_site_option("\155\157\137\163\x61\x6d\154\x5f\x6d\145\163\163\x61\x67\145", "\x53\151\147\x6e\x20\151\x6e\x20\x6f\160\x74\x69\x6f\x6e\x73\40\x75\160\144\x61\x74\145\144\x2e");
        $this->mo_saml_show_success_message();
        ZU:
        v8:
        goto Pl;
        dt:
        if (mo_saml_is_sp_configured()) {
            goto VT;
        }
        update_site_option("\x6d\x6f\x5f\x73\141\155\x6c\x5f\155\145\x73\x73\x61\147\145", "\120\x6c\x65\141\163\145\x20\x63\x6f\155\x70\154\145\164\145\x20\74\141\x20\150\162\145\x66\75\42" . add_query_arg(array("\x74\141\x62" => "\x73\141\x76\x65"), $_SERVER["\x52\105\121\125\105\x53\x54\x5f\x55\122\111"]) . "\42\40\x2f\x3e\123\x65\162\x76\151\143\x65\40\x50\x72\157\166\151\x64\145\x72\x3c\57\x61\76\40\143\157\156\146\x69\x67\x75\x72\141\164\151\157\x6e\x20\146\151\162\x73\164\x2e");
        $this->mo_saml_show_error_message();
        goto gK;
        VT:
        $pK = get_site_option("\163\141\x6d\154\137\x73\163\157\x5f\x73\x65\x74\x74\x69\156\x67\163");
        if (!empty($pK)) {
            goto tZ;
        }
        $pK = array();
        tZ:
        $NU = "\104\105\106\101\x55\114\124";
        if (!isset($_POST["\x73\151\x74\145"])) {
            goto R0;
        }
        $NU = htmlspecialchars($_POST["\x73\151\164\x65"]);
        R0:
        if (!(empty($pK[$NU]) && !empty($pK["\104\105\106\x41\x55\x4c\x54"]))) {
            goto Xz;
        }
        $pK[$NU] = $pK["\104\x45\x46\101\125\114\124"];
        Xz:
        if (array_key_exists("\x6d\x6f\137\163\x61\155\154\x5f\145\156\x61\142\x6c\145\137\154\x6f\x67\151\x6e\137\x72\x65\144\151\162\145\x63\164", $_POST)) {
            goto s3;
        }
        $RO = "\x66\x61\154\x73\x65";
        goto DJ;
        s3:
        $RO = htmlspecialchars($_POST["\155\157\x5f\x73\141\x6d\154\137\x65\x6e\x61\x62\x6c\145\137\154\157\147\151\156\137\x72\x65\144\151\162\x65\x63\164"]);
        DJ:
        if ($RO == "\164\162\x75\x65") {
            goto Hy;
        }
        $pK[$NU]["\x6d\157\x5f\x73\141\155\x6c\137\x65\156\141\x62\154\145\137\x6c\x6f\x67\x69\156\x5f\162\145\144\x69\x72\145\143\x74"] = '';
        $pK[$NU]["\x6d\157\x5f\163\141\155\154\x5f\x61\154\154\157\x77\x5f\167\x70\x5f\x73\151\147\x6e\x69\156"] = '';
        goto Td;
        Hy:
        $pK[$NU]["\x6d\x6f\137\x73\x61\155\154\x5f\145\x6e\x61\142\x6c\145\x5f\x6c\157\147\151\156\137\162\145\x64\x69\162\x65\x63\164"] = "\x74\x72\165\x65";
        $pK[$NU]["\155\157\137\163\141\x6d\x6c\137\x61\154\154\x6f\167\137\x77\160\x5f\x73\151\x67\156\151\x6e"] = "\x74\162\x75\145";
        Td:
        update_site_option("\163\x61\x6d\154\x5f\x73\163\157\x5f\x73\145\x74\x74\x69\156\x67\x73", $pK);
        update_site_option("\x6d\157\x5f\x73\141\x6d\x6c\137\155\x65\163\x73\141\147\145", "\x53\151\x67\156\40\151\x6e\40\x6f\160\164\x69\x6f\156\x73\x20\165\x70\144\141\x74\x65\x64\x2e");
        $this->mo_saml_show_success_message();
        gK:
        Pl:
        goto Tz;
        X_:
        if (mo_saml_is_sp_configured()) {
            goto hn;
        }
        update_site_option("\x6d\x6f\x5f\163\141\155\154\137\155\145\x73\163\x61\147\x65", "\x50\x6c\145\x61\x73\x65\x20\143\x6f\x6d\160\x6c\145\164\145\40\x3c\x61\40\x68\x72\x65\146\75\x22" . add_query_arg(array("\164\x61\x62" => "\x73\x61\166\145"), $_SERVER["\x52\x45\x51\125\105\x53\124\x5f\x55\x52\111"]) . "\42\x20\x2f\76\123\x65\x72\166\151\143\x65\40\120\162\157\x76\151\144\x65\x72\74\57\x61\76\x20\x63\157\156\x66\151\147\x75\x72\x61\x74\151\157\x6e\x20\x66\151\162\x73\164\x2e");
        $this->mo_saml_show_error_message();
        goto ap;
        hn:
        $pK = get_site_option("\x73\141\x6d\154\137\x73\x73\x6f\x5f\x73\145\164\164\x69\x6e\x67\x73");
        if (!empty($pK)) {
            goto su;
        }
        $pK = array();
        su:
        $NU = "\104\105\106\101\x55\114\124";
        if (!isset($_POST["\163\151\164\x65"])) {
            goto CN;
        }
        $NU = htmlspecialchars($_POST["\x73\151\x74\145"]);
        CN:
        if (!(empty($pK[$NU]) && !empty($pK["\x44\x45\x46\x41\125\x4c\124"]))) {
            goto ye;
        }
        $pK[$NU] = $pK["\104\105\106\x41\x55\114\x54"];
        ye:
        if (array_key_exists("\155\x6f\x5f\x73\x61\x6d\x6c\137\x66\157\162\143\145\137\x61\x75\x74\x68\145\156\x74\x69\143\x61\164\151\x6f\x6e", $_POST)) {
            goto uR;
        }
        $RO = "\x66\141\x6c\163\145";
        goto hL;
        uR:
        $RO = htmlspecialchars($_POST["\155\157\x5f\x73\x61\x6d\154\x5f\146\157\x72\x63\145\137\141\165\x74\x68\x65\x6e\x74\x69\x63\x61\x74\x69\x6f\x6e"]);
        hL:
        if ($RO == "\164\162\x75\x65") {
            goto at;
        }
        $pK[$NU]["\x6d\157\x5f\163\141\x6d\154\x5f\146\157\x72\143\145\x5f\141\x75\164\150\x65\x6e\164\x69\143\x61\x74\151\157\x6e"] = '';
        goto Xn;
        at:
        $pK[$NU]["\155\157\137\163\141\155\x6c\137\x66\157\x72\x63\145\x5f\141\x75\x74\x68\145\x6e\x74\151\143\141\x74\151\157\156"] = "\164\162\x75\x65";
        Xn:
        update_site_option("\x73\x61\155\154\x5f\163\x73\157\x5f\163\x65\x74\x74\151\x6e\x67\x73", $pK);
        update_site_option("\x6d\x6f\137\163\141\x6d\x6c\x5f\x6d\x65\x73\x73\x61\x67\x65", "\123\151\x67\156\40\151\x6e\40\157\x70\x74\151\x6f\x6e\163\40\165\x70\144\x61\x74\x65\144\56");
        $this->mo_saml_show_success_message();
        ap:
        Tz:
        goto gh;
        L1:
        if (mo_saml_is_sp_configured()) {
            goto gF;
        }
        update_site_option("\x6d\157\x5f\163\x61\x6d\x6c\137\x6d\145\163\x73\x61\x67\x65", "\x50\x6c\145\141\x73\145\x20\143\x6f\155\x70\154\x65\164\x65\40\74\141\x20\x68\162\x65\146\x3d\x22" . add_query_arg(array("\x74\141\142" => "\163\141\x76\145"), $_SERVER["\x52\x45\x51\125\x45\x53\124\x5f\125\x52\111"]) . "\x22\40\57\76\x53\x65\162\x76\151\143\x65\40\x50\x72\157\x76\x69\x64\145\x72\74\57\x61\76\x20\143\157\156\x66\x69\x67\165\x72\x61\164\151\157\156\x20\146\x69\162\x73\x74\x2e");
        $this->mo_saml_show_error_message();
        goto OY;
        gF:
        $pK = get_site_option("\163\x61\x6d\154\137\x73\x73\x6f\137\163\x65\164\x74\151\156\x67\163");
        if (!empty($pK)) {
            goto X2;
        }
        $pK = array();
        X2:
        $NU = "\104\105\106\x41\125\114\x54";
        if (!isset($_POST["\163\x69\164\145"])) {
            goto JV;
        }
        $NU = htmlspecialchars($_POST["\163\x69\x74\x65"]);
        JV:
        if (!(empty($pK[$NU]) && !empty($pK["\104\x45\106\x41\125\114\x54"]))) {
            goto E6;
        }
        $pK[$NU] = $pK["\x44\x45\106\101\x55\x4c\x54"];
        E6:
        if (array_key_exists("\155\x6f\x5f\x73\141\x6d\x6c\137\x73\x75\x62\x73\151\164\x65\x5f\141\143\143\x65\163\x73\137\x64\145\156\x69\145\144", $_POST)) {
            goto D7;
        }
        $Ab = "\x66\141\x6c\163\145";
        goto vV;
        D7:
        $Ab = htmlspecialchars($_POST["\x6d\157\x5f\163\x61\x6d\154\x5f\163\x75\142\x73\151\x74\x65\137\x61\x63\143\145\163\x73\x5f\144\145\156\151\145\x64"]);
        vV:
        if ($Ab == "\x74\x72\165\x65") {
            goto Pr;
        }
        $pK[$NU]["\155\x6f\x5f\163\141\155\x6c\x5f\163\x75\142\163\x69\164\x65\x5f\141\x63\143\145\x73\163\x5f\144\145\x6e\151\x65\x64"] = '';
        goto Qt;
        Pr:
        $pK[$NU]["\155\157\137\163\x61\155\154\137\163\x75\142\163\151\x74\145\x5f\x61\x63\143\x65\163\x73\x5f\x64\145\x6e\x69\x65\144"] = "\164\x72\165\145";
        Qt:
        update_site_option("\163\141\x6d\154\x5f\163\x73\x6f\x5f\x73\x65\164\x74\x69\x6e\x67\163", $pK);
        update_site_option("\155\157\137\x73\141\x6d\154\137\155\145\x73\163\x61\x67\145", "\x53\151\147\156\x20\151\x6e\x20\x6f\160\164\151\x6f\156\163\x20\165\160\x64\141\x74\x65\144\56");
        $this->mo_saml_show_success_message();
        OY:
        gh:
        goto Nq;
        Th:
        if (mo_saml_is_sp_configured()) {
            goto hy;
        }
        update_site_option("\x6d\157\137\163\141\x6d\154\137\x6d\145\x73\x73\x61\x67\145", "\120\154\x65\141\x73\x65\x20\x63\157\155\160\x6c\145\164\145\x20\74\141\x20\150\x72\x65\x66\x3d\x22" . add_query_arg(array("\x74\141\x62" => "\x73\x61\166\x65"), $_SERVER["\x52\105\x51\125\x45\x53\124\x5f\x55\x52\111"]) . "\42\40\57\76\123\x65\162\x76\151\x63\x65\40\120\162\157\166\x69\x64\x65\x72\74\x2f\141\x3e\x20\x63\x6f\156\146\x69\x67\165\x72\x61\164\151\157\x6e\x20\146\151\x72\x73\164\56");
        $this->mo_saml_show_error_message();
        goto Y0;
        hy:
        if (array_key_exists("\155\157\137\163\x61\x6d\x6c\x5f\162\x65\x67\151\163\164\x65\x72\145\144\137\x6f\156\154\171\x5f\141\x63\x63\145\163\163", $_POST)) {
            goto Ol;
        }
        $RO = "\146\x61\x6c\x73\145";
        goto vJ;
        Ol:
        $RO = htmlspecialchars($_POST["\x6d\x6f\137\x73\141\155\x6c\x5f\x72\145\147\x69\163\x74\145\x72\145\x64\137\157\x6e\x6c\171\137\141\x63\143\145\163\x73"]);
        vJ:
        $pK = get_site_option("\163\141\x6d\154\x5f\163\163\157\137\163\x65\x74\164\151\156\x67\163");
        if (!empty($pK)) {
            goto to;
        }
        $pK = array();
        to:
        $NU = "\x44\x45\106\x41\x55\114\x54";
        if (!isset($_POST["\163\x69\164\x65"])) {
            goto Jn;
        }
        $NU = htmlspecialchars($_POST["\163\151\x74\145"]);
        Jn:
        if (!(empty($pK[$NU]) && !empty($pK["\104\105\106\x41\125\114\124"]))) {
            goto tE;
        }
        $pK[$NU] = $pK["\x44\105\106\101\x55\x4c\124"];
        tE:
        if ($RO == "\164\x72\165\145") {
            goto Pb;
        }
        $pK[$NU]["\155\157\137\x73\141\x6d\154\137\162\145\x67\151\163\x74\x65\x72\145\x64\137\x6f\x6e\x6c\171\137\x61\x63\143\x65\163\x73"] = '';
        goto Hz;
        Pb:
        $pK[$NU]["\x6d\157\x5f\163\x61\155\x6c\137\x72\x65\x67\x69\x73\x74\145\162\x65\x64\x5f\x6f\156\154\x79\137\x61\143\143\145\x73\x73"] = "\164\162\x75\x65";
        Hz:
        update_site_option("\163\x61\155\154\x5f\x73\x73\x6f\137\163\x65\x74\164\151\x6e\x67\163", $pK);
        update_site_option("\x6d\x6f\x5f\163\x61\155\154\x5f\155\145\x73\x73\x61\x67\x65", "\x53\x69\x67\156\x20\151\156\40\x6f\160\x74\x69\157\x6e\x73\40\x75\160\144\141\164\x65\x64\x2e");
        $this->mo_saml_show_success_message();
        Y0:
        Nq:
        goto ad;
        N6:
        if (mo_saml_is_extension_installed("\143\x75\162\154")) {
            goto S5;
        }
        update_site_option("\155\157\x5f\163\141\x6d\x6c\x5f\x6d\145\163\163\x61\x67\145", "\x45\x52\122\x4f\122\x3a\40\x3c\141\x20\x68\x72\145\x66\x3d\42\150\x74\x74\160\x3a\x2f\57\x70\150\x70\x2e\156\x65\164\57\155\141\156\x75\x61\x6c\57\x65\x6e\57\143\165\162\154\56\x69\156\163\x74\x61\x6c\x6c\x61\164\151\157\156\x2e\x70\150\x70\x22\40\x74\x61\162\147\145\164\75\x22\137\142\x6c\141\156\153\42\x3e\x50\x48\120\40\x63\125\x52\x4c\40\145\170\x74\x65\x6e\x73\151\157\x6e\x3c\57\x61\76\40\151\x73\40\x6e\x6f\x74\x20\151\x6e\163\x74\x61\x6c\154\x65\144\40\x6f\x72\x20\x64\151\163\141\142\154\145\x64\x2e\40\122\145\163\x65\x6e\x64\x20\117\x54\120\40\x66\141\x69\154\145\144\56");
        $this->mo_saml_show_error_message();
        return;
        S5:
        $BH = sanitize_text_field($_POST["\160\x68\157\x6e\x65"]);
        $BH = str_replace("\x20", '', $BH);
        $BH = str_replace("\55", '', $BH);
        update_site_option("\155\x6f\137\x73\141\x6d\154\137\141\144\x6d\x69\x6e\137\160\150\157\156\x65", $BH);
        $EF = new CustomerSaml();
        $Zg = json_decode($EF->send_otp_token('', $BH, $this, FALSE, TRUE), true);
        if (strcasecmp($Zg["\x73\164\x61\x74\x75\x73"], "\123\x55\103\x43\105\123\x53") == 0) {
            goto YK;
        }
        update_site_option("\x6d\x6f\x5f\163\141\x6d\x6c\137\155\x65\x73\163\x61\x67\145", "\124\x68\145\162\x65\x20\167\141\x73\x20\141\156\x20\x65\x72\x72\157\162\40\151\156\40\x73\145\x6e\144\151\x6e\x67\x20\123\x4d\x53\56\40\120\154\145\141\x73\145\x20\x63\x6c\151\143\153\40\x6f\x6e\40\x52\x65\x73\145\x6e\x64\x20\117\x54\120\40\x74\157\x20\164\162\x79\x20\x61\x67\x61\x69\156\56");
        update_site_option("\x6d\157\137\163\x61\155\154\x5f\x72\145\147\151\x73\164\x72\x61\164\151\x6f\156\137\163\x74\x61\x74\x75\163", "\115\117\x5f\x4f\x54\x50\137\104\x45\x4c\111\126\x45\122\x45\x44\x5f\106\101\x49\114\x55\x52\x45\x5f\120\110\x4f\x4e\105");
        $this->mo_saml_show_error_message();
        goto uW;
        YK:
        update_site_option("\x6d\x6f\x5f\x73\x61\155\154\137\155\145\x73\x73\141\x67\145", "\40\x41\x20\157\156\x65\x20\164\151\x6d\145\40\160\x61\163\163\143\157\144\x65\40\151\x73\x20\163\145\x6e\x74\40\x74\x6f\40" . get_site_option("\x6d\157\x5f\163\x61\155\x6c\x5f\x61\x64\155\x69\x6e\x5f\160\x68\157\156\145") . "\56\x20\120\x6c\145\x61\163\145\40\145\156\164\x65\162\40\x74\x68\145\x20\157\164\160\40\x68\145\162\145\40\164\x6f\40\x76\145\162\x69\146\171\x20\171\157\165\162\40\145\x6d\141\151\154\x2e");
        update_site_option("\155\157\x5f\163\141\155\x6c\137\164\162\141\x6e\163\x61\143\164\x69\x6f\156\111\144", $Zg["\164\170\x49\x64"]);
        update_site_option("\x6d\157\x5f\x73\x61\x6d\154\137\x72\x65\147\x69\x73\164\162\141\164\151\157\156\x5f\x73\x74\x61\x74\x75\x73", "\x4d\x4f\137\x4f\124\x50\137\104\x45\x4c\111\126\105\122\105\x44\x5f\x53\x55\x43\x43\x45\123\123\x5f\120\110\117\x4e\x45");
        $this->mo_saml_show_success_message();
        uW:
        ad:
        goto Fg;
        c3:
        update_site_option("\155\x6f\x5f\163\141\155\154\137\162\x65\147\151\163\x74\162\x61\x74\x69\157\156\137\163\164\141\164\165\x73", '');
        update_site_option("\x6d\157\x5f\163\x61\155\154\137\166\x65\x72\x69\x66\x79\137\143\x75\163\164\157\x6d\x65\x72", '');
        delete_site_option("\x6d\157\137\x73\x61\x6d\x6c\x5f\x6e\x65\x77\137\x72\145\x67\151\x73\x74\x72\141\x74\x69\157\156");
        delete_site_option("\155\157\x5f\x73\x61\x6d\x6c\137\141\144\155\151\156\137\x65\x6d\x61\151\154");
        delete_site_option("\155\157\137\x73\141\x6d\154\137\x61\144\155\x69\x6e\137\x70\x68\157\156\145");
        delete_site_option("\163\x6d\154\137\x6c\x6b");
        delete_site_option("\x74\137\x73\151\164\x65\137\x73\164\141\164\x75\x73");
        delete_site_option("\163\x69\164\x65\x5f\143\x6b\137\154");
        delete_site_option("\x6e\157\137\x73\142\163");
        delete_site_option("\x75\163\x65\162\137\x61\154\x65\162\x74\x5f\x65\155\x61\151\x6c\137\163\145\x6e\x74");
        delete_site_option("\154\x69\143\145\x6e\x73\x65\x5f\141\154\x65\x72\x74\x5f\145\155\x61\x69\x6c\x5f\163\145\x6e\x74");
        delete_site_option("\x6d\x6f\137\163\x61\155\154\x5f\141\154\145\x72\164\x5f\163\x65\x6e\164\137\x66\157\162\x5f\157\156\x65");
        delete_site_option("\x6d\x6f\x5f\x73\x61\x6d\154\x5f\141\154\145\x72\x74\x5f\163\145\156\164\137\x66\x6f\162\x5f\164\x77\157");
        delete_site_option("\x6d\157\137\163\141\155\154\x5f\165\x73\x72\137\154\155\164");
        Fg:
        goto ak;
        Ep:
        if (mo_saml_is_extension_installed("\x63\x75\162\x6c")) {
            goto AO;
        }
        update_site_option("\x6d\157\x5f\x73\x61\155\154\x5f\155\145\163\x73\141\x67\145", "\x45\122\122\117\122\x3a\40\x3c\x61\40\150\162\x65\146\75\x22\x68\x74\164\x70\x3a\57\x2f\160\150\160\56\156\x65\164\x2f\x6d\141\x6e\165\x61\154\57\145\x6e\x2f\143\x75\162\x6c\x2e\x69\x6e\x73\x74\x61\x6c\x6c\x61\x74\151\x6f\x6e\56\160\x68\160\x22\40\164\x61\162\147\x65\164\x3d\x22\x5f\142\154\141\x6e\153\x22\76\120\x48\120\40\x63\x55\x52\x4c\x20\145\170\164\145\x6e\x73\151\x6f\x6e\74\57\x61\76\x20\151\163\x20\x6e\x6f\164\40\151\x6e\x73\164\141\154\154\x65\144\40\157\162\40\x64\151\163\141\x62\154\x65\x64\x2e\x20\x52\x65\x73\x65\x6e\x64\x20\x4f\124\x50\40\146\141\x69\x6c\145\x64\56");
        $this->mo_saml_show_error_message();
        return;
        AO:
        $BH = get_site_option("\x6d\x6f\x5f\163\x61\155\x6c\x5f\x61\x64\155\151\156\137\x70\150\157\x6e\x65");
        $EF = new CustomerSaml();
        $Zg = json_decode($EF->send_otp_token('', $BH, $this, FALSE, TRUE), true);
        if (strcasecmp($Zg["\x73\164\141\x74\x75\x73"], "\123\x55\x43\103\x45\x53\123") == 0) {
            goto KL;
        }
        update_site_option("\x6d\157\x5f\x73\x61\x6d\x6c\137\155\145\163\x73\141\x67\145", "\124\150\145\x72\x65\x20\x77\x61\163\x20\141\x6e\x20\145\162\x72\x6f\162\40\151\156\x20\163\x65\156\144\x69\156\147\x20\145\x6d\141\151\154\56\x20\120\154\x65\x61\x73\145\40\x63\154\x69\x63\x6b\x20\x6f\x6e\x20\122\145\x73\x65\x6e\144\x20\x4f\124\x50\40\x74\157\x20\164\162\171\40\141\x67\x61\x69\x6e\x2e");
        update_site_option("\x6d\157\x5f\163\141\155\154\x5f\x72\145\x67\x69\163\164\x72\x61\x74\x69\x6f\156\x5f\163\164\141\164\165\x73", "\x4d\x4f\137\117\124\120\137\x44\x45\114\111\126\105\122\x45\104\x5f\x46\101\x49\x4c\x55\x52\x45\x5f\x50\x48\x4f\116\x45");
        $this->mo_saml_show_error_message();
        goto N8;
        KL:
        update_site_option("\155\157\137\163\141\155\154\x5f\x6d\x65\163\x73\141\x67\145", "\x20\101\40\x6f\x6e\145\x20\164\x69\155\x65\40\x70\141\x73\163\x63\x6f\144\145\40\151\163\40\x73\145\x6e\x74\40\164\157\x20" . $BH . "\40\x61\147\x61\x69\156\56\x20\x50\x6c\145\141\x73\145\x20\143\150\x65\143\153\40\151\x66\40\x79\157\165\40\147\x6f\164\x20\x74\x68\145\40\157\x74\160\x20\x61\x6e\144\x20\145\156\x74\x65\162\40\151\x74\40\150\x65\x72\145\x2e");
        update_site_option("\155\157\137\163\141\x6d\x6c\137\x74\x72\x61\156\x73\x61\143\164\151\157\156\x49\x64", $Zg["\164\170\x49\x64"]);
        update_site_option("\155\157\x5f\x73\141\155\154\x5f\x72\145\147\x69\163\x74\162\141\164\x69\x6f\x6e\x5f\x73\164\x61\x74\165\163", "\115\x4f\x5f\x4f\x54\x50\x5f\x44\105\114\x49\126\x45\122\105\104\x5f\123\x55\103\x43\105\x53\x53\137\x50\110\x4f\x4e\x45");
        $this->mo_saml_show_success_message();
        N8:
        ak:
        goto KO;
        WF:
        if (mo_saml_is_extension_installed("\143\165\162\154")) {
            goto z8;
        }
        update_site_option("\x6d\x6f\137\163\141\155\x6c\137\155\x65\x73\x73\x61\147\x65", "\x45\x52\x52\x4f\x52\72\x20\74\141\40\150\x72\145\x66\75\42\x68\x74\164\x70\72\57\x2f\160\x68\160\56\x6e\145\x74\x2f\155\141\x6e\x75\141\154\57\145\156\x2f\x63\x75\162\154\x2e\x69\156\x73\x74\x61\x6c\x6c\x61\164\x69\x6f\x6e\56\x70\x68\x70\42\x20\x74\x61\x72\x67\145\164\75\42\x5f\142\154\141\156\153\42\x3e\x50\110\120\x20\x63\125\x52\x4c\x20\x65\x78\164\145\x6e\x73\151\x6f\156\x3c\x2f\x61\x3e\40\x69\163\x20\x6e\x6f\164\x20\151\x6e\163\x74\141\x6c\154\145\144\x20\x6f\162\40\144\151\163\x61\142\154\145\144\56\40\122\x65\163\x65\x6e\x64\40\x4f\124\120\x20\146\x61\151\x6c\x65\144\x2e");
        $this->mo_saml_show_error_message();
        return;
        z8:
        $sf = get_site_option("\x6d\157\137\x73\141\155\x6c\x5f\141\x64\x6d\x69\156\x5f\x65\x6d\141\x69\154");
        $EF = new CustomerSaml();
        $Zg = json_decode($EF->send_otp_token($sf, '', $this), true);
        if (strcasecmp($Zg["\x73\164\x61\x74\x75\163"], "\x53\x55\x43\103\x45\x53\x53") == 0) {
            goto lB;
        }
        update_site_option("\x6d\x6f\137\x73\x61\155\154\137\x6d\x65\x73\x73\141\x67\x65", "\124\x68\x65\162\145\x20\x77\x61\163\40\141\x6e\40\145\x72\162\157\x72\40\151\156\40\163\x65\x6e\144\x69\x6e\x67\40\x65\x6d\141\x69\154\x2e\x20\x50\x6c\145\141\x73\145\x20\143\x6c\151\143\153\x20\x6f\x6e\x20\x52\x65\x73\145\156\144\40\x4f\x54\x50\x20\x74\157\x20\164\162\171\x20\141\x67\141\x69\x6e\x2e");
        update_site_option("\155\157\137\163\x61\155\154\137\162\x65\x67\x69\163\x74\x72\x61\164\x69\157\156\x5f\x73\164\x61\x74\165\163", "\x4d\117\137\x4f\124\120\137\x44\105\114\111\x56\105\122\x45\x44\137\106\101\111\114\125\122\x45\137\x45\115\101\x49\114");
        $this->mo_saml_show_error_message();
        goto CS;
        lB:
        update_site_option("\x6d\x6f\x5f\x73\141\x6d\x6c\137\155\145\163\163\141\147\x65", "\x20\x41\x20\157\x6e\145\40\x74\x69\x6d\145\x20\160\x61\x73\x73\x63\x6f\144\145\x20\x69\x73\x20\x73\x65\x6e\164\x20\x74\157\x20" . get_site_option("\155\x6f\137\163\x61\x6d\154\x5f\x61\144\155\x69\156\x5f\x65\x6d\141\151\x6c") . "\x20\x61\x67\141\x69\156\x2e\40\x50\154\145\x61\x73\x65\x20\143\150\145\x63\153\40\151\x66\x20\171\157\165\x20\x67\x6f\164\40\164\150\145\40\157\164\x70\40\141\x6e\144\40\145\x6e\x74\145\162\40\x69\x74\x20\x68\145\162\145\56");
        update_site_option("\155\x6f\137\x73\x61\155\154\137\x74\162\x61\x6e\x73\x61\143\x74\x69\x6f\x6e\x49\144", $Zg["\x74\170\111\144"]);
        update_site_option("\x6d\x6f\137\x73\x61\155\154\137\162\145\147\151\x73\x74\162\141\164\x69\x6f\156\x5f\x73\164\x61\164\165\x73", "\115\x4f\x5f\117\x54\x50\137\x44\105\x4c\x49\x56\x45\x52\x45\x44\x5f\x53\x55\x43\x43\105\x53\123\137\x45\115\101\x49\x4c");
        $this->mo_saml_show_success_message();
        CS:
        KO:
        goto gD;
        eo:
        if (mo_saml_is_extension_installed("\143\x75\162\154")) {
            goto lf;
        }
        update_site_option("\x6d\x6f\137\x73\141\155\x6c\137\155\145\163\163\141\147\145", "\105\x52\122\117\122\x3a\x20\74\141\40\x68\x72\x65\146\75\42\x68\164\164\160\x3a\x2f\57\160\x68\x70\x2e\x6e\x65\164\57\x6d\141\x6e\x75\x61\x6c\x2f\x65\156\x2f\143\165\x72\x6c\x2e\x69\156\x73\164\141\x6c\x6c\141\164\151\x6f\x6e\x2e\160\x68\x70\x22\40\164\141\x72\x67\145\x74\x3d\x22\137\142\x6c\x61\x6e\x6b\42\x3e\120\x48\x50\x20\x63\x55\x52\114\40\x65\170\164\145\156\163\151\x6f\156\x3c\57\141\x3e\40\151\x73\40\156\157\164\40\x69\156\163\164\141\154\x6c\145\144\40\x6f\162\x20\x64\151\163\141\x62\x6c\x65\x64\56\40\121\x75\145\x72\x79\40\x73\165\x62\155\151\x74\40\x66\141\151\x6c\145\x64\56");
        $this->mo_saml_show_error_message();
        return;
        lf:
        $sf = htmlspecialchars($_POST["\x6d\x6f\137\163\x61\155\x6c\137\x63\x6f\x6e\x74\141\x63\164\137\x75\x73\137\x65\155\x61\151\154"]);
        $BH = htmlspecialchars($_POST["\155\x6f\x5f\x73\141\x6d\154\137\x63\x6f\x6e\x74\141\x63\164\x5f\x75\163\137\x70\150\x6f\156\145"]);
        $wQ = htmlspecialchars($_POST["\x6d\157\137\x73\x61\155\x6c\137\x63\x6f\156\164\141\x63\x74\137\x75\163\137\161\165\x65\x72\x79"]);
        if (array_key_exists("\x73\145\x6e\x64\137\x70\154\x75\147\151\156\x5f\x63\x6f\x6e\146\x69\x67", $_POST) === true) {
            goto JJ;
        }
        update_site_option("\x73\x65\x6e\x64\137\x70\154\x75\x67\151\x6e\137\x63\157\x6e\146\151\147", "\157\x66\146");
        goto FW;
        JJ:
        $EC = miniorange_import_export(true, true);
        $wQ .= $EC;
        delete_site_option("\163\145\x6e\144\137\x70\154\165\147\x69\156\137\143\157\x6e\x66\151\x67");
        FW:
        $EF = new CustomerSaml();
        if ($this->mo_saml_check_empty_or_null($sf) || $this->mo_saml_check_empty_or_null($wQ)) {
            goto gG;
        }
        $c9 = $EF->submit_contact_us($sf, $BH, $wQ, $this);
        if ($c9 == false) {
            goto KQ;
        }
        update_site_option("\x6d\157\137\x73\x61\155\x6c\x5f\155\x65\x73\x73\141\147\x65", "\x54\x68\x61\x6e\x6b\x73\40\x66\157\x72\40\x67\145\x74\164\x69\x6e\147\40\x69\x6e\x20\164\x6f\x75\x63\x68\41\40\x57\145\40\x73\x68\x61\154\154\x20\x67\x65\x74\40\142\141\143\153\40\x74\157\x20\171\x6f\x75\x20\163\x68\x6f\162\164\154\171\x2e");
        $this->mo_saml_show_success_message();
        goto fY;
        KQ:
        update_site_option("\x6d\157\137\x73\141\x6d\x6c\x5f\x6d\x65\163\x73\141\x67\x65", "\x59\x6f\x75\162\x20\161\x75\x65\x72\171\40\143\157\x75\154\144\40\x6e\157\164\x20\x62\145\40\x73\165\x62\x6d\151\x74\x74\x65\144\x2e\x20\120\154\145\x61\163\145\x20\164\x72\171\x20\141\147\141\151\x6e\56");
        $this->mo_saml_show_error_message();
        fY:
        goto V6;
        gG:
        update_site_option("\x6d\x6f\x5f\163\141\x6d\x6c\x5f\x6d\x65\x73\x73\x61\147\145", "\x50\x6c\x65\x61\x73\x65\x20\x66\151\154\x6c\x20\165\x70\x20\x45\x6d\x61\151\x6c\40\x61\x6e\x64\x20\x51\x75\x65\x72\x79\x20\x66\x69\145\154\x64\163\x20\x74\157\x20\163\x75\142\x6d\151\x74\x20\x79\x6f\165\162\x20\x71\165\145\162\171\56");
        $this->mo_saml_show_error_message();
        V6:
        gD:
        goto oS;
        Xg:
        if (mo_saml_is_extension_installed("\143\165\162\x6c")) {
            goto dw;
        }
        update_site_option("\x6d\157\x5f\163\141\x6d\x6c\137\155\145\x73\163\x61\x67\145", "\105\122\122\117\x52\x3a\40\x3c\141\x20\150\162\x65\146\x3d\x22\x68\x74\x74\160\72\x2f\x2f\160\150\160\56\156\x65\x74\x2f\x6d\x61\156\165\x61\x6c\57\145\x6e\57\x63\165\162\x6c\56\151\156\x73\164\x61\154\x6c\141\x74\x69\157\156\56\x70\150\160\42\40\164\x61\162\147\x65\x74\x3d\42\137\x62\x6c\141\156\x6b\42\76\120\110\120\x20\143\125\122\x4c\40\145\170\164\145\x6e\163\x69\157\x6e\74\57\141\x3e\40\x69\163\40\x6e\157\164\x20\x69\156\163\164\x61\x6c\x6c\145\x64\40\x6f\162\40\x64\151\163\x61\x62\154\x65\144\56\x20\114\x6f\x67\x69\156\40\146\x61\x69\x6c\x65\x64\x2e");
        $this->mo_saml_show_error_message();
        return;
        dw:
        $sf = '';
        $sG = '';
        if ($this->mo_saml_check_empty_or_null($_POST["\x65\155\141\x69\154"]) || $this->mo_saml_check_empty_or_null($_POST["\160\141\x73\x73\x77\x6f\162\x64"])) {
            goto hj;
        }
        if ($this->checkPasswordPattern(strip_tags($_POST["\160\x61\163\x73\x77\x6f\162\x64"]))) {
            goto VB;
        }
        $sf = sanitize_email($_POST["\145\155\x61\x69\154"]);
        $sG = stripslashes(strip_tags($_POST["\160\141\x73\x73\x77\x6f\x72\144"]));
        goto DS;
        VB:
        update_site_option("\x6d\x6f\137\163\x61\155\x6c\137\155\145\x73\163\141\x67\x65", "\115\x69\x6e\x69\x6d\165\x6d\x20\66\x20\x63\150\141\x72\x61\x63\164\145\x72\163\40\x73\x68\x6f\165\154\x64\40\x62\x65\x20\160\162\145\163\145\156\164\56\x20\115\141\x78\x69\155\x75\x6d\40\61\65\40\143\x68\x61\x72\x61\143\x74\145\x72\x73\x20\x73\150\x6f\x75\x6c\144\x20\x62\x65\x20\160\162\x65\x73\x65\x6e\164\x2e\x20\117\156\154\171\x20\146\x6f\154\154\157\x77\151\x6e\147\x20\163\x79\155\x62\x6f\x6c\x73\40\50\41\100\43\x2e\x24\45\x5e\x26\52\x2d\137\51\x20\x73\x68\157\165\x6c\x64\x20\x62\x65\x20\160\162\x65\163\x65\x6e\x74\x2e");
        $this->mo_saml_show_error_message();
        return;
        DS:
        goto Hu;
        hj:
        update_site_option("\x6d\x6f\137\163\x61\x6d\x6c\x5f\x6d\145\x73\x73\141\x67\x65", "\101\x6c\x6c\x20\164\x68\x65\40\x66\151\x65\154\144\x73\40\141\162\x65\x20\x72\x65\x71\x75\151\x72\x65\144\56\40\120\x6c\x65\141\x73\x65\x20\x65\156\164\145\x72\x20\x76\x61\x6c\x69\x64\40\x65\156\x74\162\151\x65\163\56");
        $this->mo_saml_show_error_message();
        return;
        Hu:
        update_site_option("\x6d\157\137\163\141\155\154\x5f\x61\x64\x6d\151\x6e\137\145\155\x61\151\x6c", $sf);
        update_site_option("\155\x6f\x5f\163\x61\155\x6c\137\141\144\x6d\x69\x6e\x5f\160\141\x73\x73\x77\157\x72\x64", $sG);
        $EF = new Customersaml();
        $Zg = $EF->get_customer_key($this);
        if ($Zg) {
            goto x8;
        }
        return;
        x8:
        $Z8 = json_decode($Zg, true);
        if (json_last_error() == JSON_ERROR_NONE) {
            goto Mz;
        }
        update_site_option("\155\x6f\x5f\163\x61\x6d\x6c\x5f\x6d\x65\163\x73\x61\147\145", "\111\156\x76\x61\x6c\151\144\x20\165\163\145\x72\x6e\x61\155\145\x20\157\162\x20\x70\141\x73\x73\x77\157\x72\144\x2e\40\x50\154\x65\141\163\145\x20\164\x72\171\x20\x61\x67\x61\151\156\x2e");
        $this->mo_saml_show_error_message();
        goto eW;
        Mz:
        update_site_option("\155\157\x5f\x73\141\155\154\x5f\141\x64\155\x69\x6e\137\x63\165\x73\x74\x6f\x6d\x65\162\x5f\x6b\x65\x79", $Z8["\151\144"]);
        update_site_option("\155\157\137\x73\141\x6d\154\137\141\144\x6d\x69\x6e\137\141\x70\x69\137\x6b\145\171", $Z8["\x61\x70\x69\113\x65\x79"]);
        update_site_option("\x6d\x6f\x5f\163\x61\x6d\x6c\137\143\x75\x73\164\157\x6d\x65\162\137\164\x6f\153\145\156", $Z8["\x74\x6f\x6b\x65\156"]);
        update_site_option("\155\x6f\x5f\163\x61\155\x6c\137\x61\144\155\151\x6e\x5f\160\x61\x73\x73\x77\x6f\x72\x64", '');
        update_site_option("\155\x6f\137\x73\x61\155\154\x5f\155\x65\x73\163\141\x67\145", "\103\x75\x73\x74\157\155\x65\x72\40\x72\x65\x74\x72\x69\145\166\145\x64\40\x73\165\x63\143\x65\163\x73\x66\165\154\x6c\x79");
        update_site_option("\155\157\x5f\163\141\155\154\x5f\162\145\147\x69\163\x74\x72\x61\164\x69\x6f\x6e\137\x73\x74\141\x74\165\163", "\x45\x78\x69\x73\164\151\156\147\x20\125\163\x65\162");
        delete_site_option("\x6d\x6f\x5f\x73\141\155\154\x5f\x76\x65\162\151\x66\171\x5f\x63\165\x73\164\157\x6d\x65\x72");
        if (get_site_option("\163\x6d\154\x5f\x6c\x6b")) {
            goto Jh;
        }
        $this->mo_saml_show_success_message();
        goto tV;
        Jh:
        $Z1 = get_site_option("\x6d\x6f\x5f\x73\141\155\x6c\137\143\x75\163\x74\157\x6d\145\x72\x5f\x74\x6f\153\x65\156");
        $wC = AESEncryption::decrypt_data(get_site_option("\163\155\x6c\x5f\154\x6b"), $Z1);
        $Oy = add_query_arg(array("\x74\141\x62" => "\x6c\x69\x63\145\156\163\151\156\147"), $_SERVER["\x52\x45\x51\125\x45\x53\x54\x5f\x55\122\x49"]);
        $b1 = $EF->mo_saml_verify_license($wC, $this);
        if ($b1) {
            goto tl;
        }
        return;
        tl:
        $Zg = json_decode($b1, true);
        if (strcasecmp($Zg["\x73\164\x61\164\x75\163"], "\123\x55\103\x43\105\x53\x53") == 0) {
            goto DK;
        }
        update_site_option("\155\157\x5f\163\x61\155\x6c\137\155\145\x73\x73\x61\147\145", "\x4c\x69\143\x65\x6e\x73\x65\40\x6b\x65\x79\x20\146\x6f\x72\x20\x74\150\151\163\x20\x69\156\x73\x74\141\x6e\x63\145\40\x69\x73\x20\151\x6e\143\157\x72\162\145\143\x74\x2e\40\x4d\x61\x6b\145\x20\x73\x75\x72\145\40\171\157\165\x20\150\x61\x76\145\40\x6e\x6f\164\x20\164\141\x6d\160\x65\x72\145\144\x20\167\x69\164\x68\40\x69\x74\40\141\164\x20\141\154\x6c\56\40\x50\x6c\x65\141\x73\145\40\x65\x6e\x74\145\x72\40\x61\40\x76\x61\x6c\151\144\x20\x6c\151\x63\x65\x6e\163\x65\40\x6b\x65\171\x2e");
        delete_site_option("\x73\x6d\154\137\x6c\153");
        $this->mo_saml_show_error_message();
        goto nJ;
        DK:
        $this->mo_saml_show_success_message();
        nJ:
        tV:
        eW:
        update_site_option("\155\157\137\x73\x61\155\x6c\137\x61\x64\155\x69\156\x5f\160\x61\x73\163\167\x6f\x72\144", '');
        oS:
        if (!get_site_option("\x6e\x6f\x5f\163\x62\x73")) {
            goto Ps;
        }
        $Z1 = get_site_option("\155\x6f\x5f\163\x61\x6d\x6c\137\143\x75\163\x74\x6f\155\x65\162\137\164\157\x6b\x65\x6e");
        $Kp = AESEncryption::decrypt_data(get_site_option("\x6e\x6f\x5f\163\x62\163"), $Z1);
        $C_ = count(Utilities::get_sites());
        if (!($Kp < $C_)) {
            goto gy;
        }
        if (get_site_option("\x6c\151\143\x65\156\163\x65\137\141\154\145\162\164\x5f\x65\x6d\x61\x69\154\137\x73\x65\156\164")) {
            goto Ua;
        }
        $EF = new Customersaml();
        $EF->mo_saml_send_alert_email_for_license($Kp, $this);
        Ua:
        gy:
        Ps:
        if (self::mo_check_option_admin_referer("\x6d\x6f\x5f\x73\x61\x6d\154\x5f\x76\x65\x72\151\146\x79\137\x6c\x69\x63\x65\x6e\x73\145")) {
            goto CT;
        }
        if (self::mo_check_option_admin_referer("\x6d\x6f\x5f\163\x61\x6d\x6c\137\x66\162\x65\x65\x5f\164\x72\x69\x61\x6c")) {
            goto ny;
        }
        if (!self::mo_check_option_admin_referer("\x6d\157\x5f\163\x61\155\154\x5f\143\150\x65\x63\x6b\137\x6c\151\x63\145\x6e\163\x65")) {
            goto zJ;
        }
        $EF = new Customersaml();
        $b1 = $EF->check_customer_ln($this);
        if ($b1) {
            goto nd;
        }
        return;
        nd:
        $Zg = json_decode($b1, true);
        if (strcasecmp($Zg["\x73\x74\141\x74\165\x73"], "\x53\125\103\x43\x45\x53\123") == 0) {
            goto Vg;
        }
        $Z1 = get_site_option("\155\157\137\x73\141\x6d\154\x5f\143\165\163\164\x6f\155\145\x72\x5f\164\157\x6b\x65\x6e");
        $Oy = add_query_arg(array("\x74\x61\x62" => "\154\x69\143\145\156\x73\x69\156\147"), $_SERVER["\122\105\121\125\105\x53\124\x5f\x55\x52\111"]);
        update_site_option("\x6d\157\x5f\x73\141\x6d\154\x5f\155\x65\163\163\141\147\145", "\x59\x6f\x75\x20\150\141\166\145\40\156\x6f\164\x20\165\160\x67\162\141\144\145\x64\x20\171\145\164\x2e\40\74\141\x20\x68\x72\145\146\x3d\x22" . $Oy . "\x22\76\x43\x6c\x69\143\x6b\40\150\x65\162\x65\x3c\57\x61\76\40\x74\157\40\165\160\x67\x72\141\144\145\40\x74\x6f\40\x70\162\145\155\x69\x75\155\x20\x76\x65\x72\163\151\157\x6e\56");
        $this->mo_saml_show_error_message();
        goto dT;
        Vg:
        if (!array_key_exists("\x69\x73\115\165\x6c\x74\151\x53\x69\x74\145\x50\x6c\165\147\151\x6e\x52\145\x71\x75\145\163\x74\x65\x64", $Zg)) {
            goto MT;
        }
        $nj = $Zg["\x69\x73\x4d\x75\154\164\x69\x53\x69\164\145\x50\154\165\147\x69\x6e\x52\145\x71\x75\x65\x73\164\x65\144"];
        MT:
        $C_ = count(Utilities::get_sites());
        if ($nj && array_key_exists("\x6e\157\x4f\146\123\x75\142\123\x69\164\x65\x73", $Zg) && $C_ <= $Zg["\156\157\x4f\x66\x53\165\x62\x53\151\164\x65\163"]) {
            goto rq;
        }
        $Z1 = get_site_option("\x6d\x6f\137\x73\141\x6d\154\137\143\x75\x73\164\157\x6d\145\162\137\x74\x6f\153\145\x6e");
        if (empty($IH)) {
            goto vR;
        }
        update_site_option("\x6e\x6f\137\x73\142\x73", AESEncryption::encrypt_data($IH, $Z1));
        vR:
        update_site_option("\x73\x69\x74\145\x5f\143\x6b\x5f\x6c", AESEncryption::encrypt_data("\146\x61\154\163\145", $Z1));
        $Oy = add_query_arg(array("\x74\x61\142" => "\154\x69\143\x65\156\163\151\x6e\x67"), $_SERVER["\122\x45\x51\x55\105\123\x54\x5f\125\122\111"]);
        update_site_option("\x6d\157\x5f\163\x61\x6d\x6c\137\155\145\x73\163\141\x67\145", "\x59\157\x75\40\150\x61\166\x65\40\156\157\x74\x20\x75\160\x67\x72\141\x64\x65\144\x20\164\157\40\x74\x68\145\40\x63\x6f\162\x72\x65\x63\164\40\x6c\151\143\145\156\x73\x65\x20\160\x6c\x61\x6e\56\40\105\151\164\x68\145\x72\40\x79\x6f\x75\x20\150\x61\166\145\40\x70\165\162\x63\150\x61\163\x65\144\40\x66\x6f\162\40\151\x6e\x63\157\x72\x72\145\143\164\40\x6e\157\x2e\x20\157\x66\x20\163\x69\164\145\163\x20\157\162\40\x79\x6f\165\x20\x68\x61\x76\145\40\156\x6f\164\x20\163\145\154\145\143\164\x65\144\40\155\165\x6c\x74\x69\163\x69\x74\145\40\157\x70\164\151\157\156\x20\x77\x68\151\154\145\40\160\165\x72\143\x68\x61\163\151\156\x67\56\x20\x3c\141\x20\x68\162\x65\x66\x3d\42\x23\42\40\x6f\156\x63\154\x69\143\153\75\42\x67\145\164\165\160\147\x72\141\x64\x65\154\151\143\x65\156\x73\145\163\x66\x6f\162\x6d\50\51\x22\x3e\x43\x6c\x69\143\x6b\40\x68\145\x72\x65\74\x2f\141\x3e\x20\x74\157\x20\x75\x70\147\x72\141\x64\145\40\164\157\40\160\x72\145\x6d\x69\165\x6d\40\x76\x65\x72\163\x69\157\156\x2e");
        $this->mo_saml_show_error_message();
        goto a5;
        rq:
        $Z1 = get_site_option("\x6d\x6f\137\163\x61\155\154\137\143\165\163\164\x6f\x6d\145\x72\x5f\164\157\153\145\x6e");
        $IH = $Zg["\x6e\157\117\146\x53\165\142\x53\151\164\145\x73"];
        update_site_option("\x73\151\x74\x65\137\x63\x6b\137\x6c", AESEncryption::encrypt_data("\164\x72\x75\x65", $Z1));
        update_site_option("\x6e\157\x5f\163\x62\163", AESEncryption::encrypt_data($IH, $Z1));
        $Oy = add_query_arg(array("\x74\141\x62" => "\154\x6f\x67\x69\156"), $_SERVER["\122\x45\121\x55\105\123\124\x5f\125\x52\x49"]);
        update_site_option("\x6d\157\137\163\x61\155\154\137\155\x65\163\163\x61\x67\x65", "\x59\157\165\x20\x68\141\166\x65\x20\163\x75\x63\143\145\163\163\146\165\x6c\154\x79\40\165\x70\x67\162\141\144\x65\x64\40\164\157\x20\160\x72\x65\155\151\165\x6d\40\x76\145\162\x73\x69\157\x6e\x2e");
        $this->mo_saml_show_success_message();
        a5:
        dT:
        zJ:
        goto x0;
        ny:
        if (decryptSamlElement()) {
            goto Fr;
        }
        $wC = postResponse();
        $EF = new Customersaml();
        $b1 = $EF->mo_saml_verify_license($wC, $this);
        $Zg = json_decode($b1, true);
        if (strcasecmp($Zg["\163\164\141\x74\x75\x73"], "\123\125\103\x43\105\123\123") == 0) {
            goto n4;
        }
        if (strcasecmp($Zg["\163\164\141\164\x75\x73"], "\106\x41\x49\x4c\105\104") == 0) {
            goto LR;
        }
        update_site_option("\x6d\x6f\137\x73\x61\155\154\x5f\155\145\163\163\x61\147\145", "\x41\156\40\x65\162\162\x6f\x72\x20\157\x63\x63\x75\162\145\x64\40\167\x68\151\x6c\x65\x20\160\162\x6f\143\145\x73\x73\x69\156\x67\40\x79\x6f\165\162\x20\162\x65\x71\165\x65\163\164\56\40\x50\x6c\x65\x61\163\x65\40\124\162\x79\40\x61\147\x61\x69\x6e\56");
        $this->mo_saml_show_error_message();
        goto L_;
        LR:
        update_site_option("\155\157\x5f\163\141\x6d\154\137\155\145\163\163\141\x67\145", "\x54\150\x65\162\x65\40\167\141\x73\x20\141\x6e\x20\x65\162\162\157\x72\40\141\x63\x74\x69\166\141\x74\x69\x6e\147\x20\171\x6f\165\162\40\124\x52\x49\101\x4c\40\x76\145\162\x73\x69\157\156\x2e\40\120\x6c\x65\141\163\x65\40\143\157\x6e\x74\x61\143\x74\40\x69\x6e\x66\157\x40\155\151\156\x69\x6f\162\141\156\147\x65\56\x63\157\155\40\146\x6f\162\x20\x67\145\164\164\x69\156\147\40\x6e\x65\167\x20\154\151\143\x65\156\163\x65\x20\x66\x6f\x72\x20\164\x72\151\x61\x6c\40\166\x65\162\163\x69\157\x6e\x2e");
        $this->mo_saml_show_error_message();
        L_:
        goto P5;
        n4:
        $Z1 = get_site_option("\155\x6f\x5f\163\141\155\x6c\137\143\165\x73\164\x6f\155\x65\x72\x5f\x74\157\x6b\145\156");
        $Z1 = get_site_option("\155\157\x5f\x73\x61\x6d\x6c\x5f\143\165\x73\164\x6f\x6d\145\x72\137\164\x6f\153\x65\156");
        update_site_option("\164\137\163\x69\164\145\137\x73\x74\141\x74\x75\163", AESEncryption::encrypt_data("\164\x72\165\145", $Z1));
        update_site_option("\155\x6f\x5f\x73\141\155\154\137\155\145\x73\x73\x61\147\145", "\x59\x6f\165\x72\x20\65\x20\144\x61\x79\x73\40\124\122\111\x41\114\40\x69\163\x20\141\x63\x74\151\166\x61\x74\145\x64\x2e\x20\x59\x6f\x75\x20\x63\x61\156\40\x6e\x6f\167\40\x73\145\x74\x75\160\x20\164\x68\145\x20\x70\x6c\165\147\151\156\56");
        $this->mo_saml_show_success_message();
        P5:
        goto C1;
        Fr:
        update_site_option("\x6d\x6f\x5f\163\141\x6d\x6c\137\155\145\163\163\x61\147\145", "\124\x68\145\x72\x65\x20\167\141\163\x20\x61\156\x20\x65\162\x72\157\x72\x20\x61\143\x74\x69\x76\x61\x74\151\x6e\x67\40\x79\157\x75\x72\x20\124\122\x49\x41\x4c\40\x76\x65\x72\x73\x69\157\x6e\x2e\40\x45\151\x74\150\145\162\40\x79\x6f\x75\162\x20\x74\x72\x69\141\154\x20\160\x65\x72\x69\x6f\x64\40\x69\x73\40\145\170\160\x69\162\145\x64\40\157\x72\x20\171\157\165\40\141\162\145\40\165\x73\151\x6e\147\x20\167\162\x6f\x6e\147\40\x74\x72\x69\x61\154\x20\x76\145\162\x73\151\157\156\56\x20\x50\154\145\141\x73\x65\40\143\x6f\156\x74\141\143\x74\x20\151\x6e\x66\x6f\100\155\x69\x6e\151\x6f\x72\141\156\x67\145\x2e\143\157\x6d\x20\x66\x6f\x72\x20\147\x65\x74\x74\x69\x6e\147\x20\156\x65\167\x20\x6c\151\143\145\x6e\163\x65\40\x66\157\x72\40\x74\162\151\141\154\40\166\145\162\x73\151\x6f\x6e\56");
        $this->mo_saml_show_error_message();
        C1:
        x0:
        goto kL;
        CT:
        if (!$this->mo_saml_check_empty_or_null($_POST["\163\141\x6d\x6c\137\154\151\x63\x65\156\163\x65\137\x6b\145\171"])) {
            goto Ph;
        }
        update_site_option("\x6d\x6f\137\163\x61\x6d\x6c\137\x6d\145\163\163\141\x67\145", "\101\x6c\154\x20\x74\150\145\40\146\151\x65\x6c\x64\163\40\x61\x72\x65\x20\x72\x65\161\x75\x69\162\145\144\x2e\40\120\x6c\x65\141\x73\145\x20\145\156\x74\145\162\x20\x76\x61\154\x69\144\x20\154\151\x63\x65\x6e\163\145\40\153\x65\171\x2e");
        $this->mo_saml_show_error_message();
        return;
        Ph:
        $wC = trim($_POST["\163\141\x6d\154\137\x6c\151\x63\x65\156\163\x65\137\153\145\x79"]);
        $EF = new Customersaml();
        $b1 = $EF->check_customer_ln($this);
        if ($b1) {
            goto kJ;
        }
        return;
        kJ:
        $Zg = json_decode($b1, true);
        $Oy = add_query_arg(array("\x74\141\142" => "\154\x69\x63\145\156\163\x69\156\x67"), $_SERVER["\x52\x45\x51\x55\105\123\x54\137\x55\122\111"]);
        if (strcasecmp($Zg["\163\164\141\164\x75\163"], "\x53\x55\103\103\x45\123\123") == 0) {
            goto tM;
        }
        $Z1 = get_site_option("\155\x6f\137\163\x61\x6d\x6c\x5f\143\165\x73\x74\x6f\x6d\x65\162\137\x74\x6f\153\145\156");
        $Oy = add_query_arg(array("\164\141\x62" => "\154\x69\143\145\x6e\163\x69\x6e\147"), $_SERVER["\x52\x45\x51\x55\x45\123\x54\137\125\122\111"]);
        update_site_option("\155\157\137\163\141\155\x6c\137\155\145\x73\x73\141\147\x65", "\131\x6f\x75\40\x68\141\x76\x65\40\156\x6f\x74\x20\x75\x70\x67\162\141\x64\x65\144\40\171\145\x74\56\40\74\141\40\x68\162\x65\x66\x3d\42" . $Oy . "\x22\x3e\103\x6c\151\x63\x6b\x20\x68\x65\162\x65\x3c\57\x61\76\x20\164\157\40\165\160\x67\162\141\144\145\x20\x74\157\40\160\162\x65\x6d\151\x75\x6d\x20\x76\145\x72\163\x69\x6f\x6e\x2e");
        $this->mo_saml_show_error_message();
        goto I2;
        tM:
        if (!array_key_exists("\151\163\115\165\x6c\x74\151\123\x69\x74\145\x50\154\165\147\151\x6e\x52\145\x71\165\145\x73\x74\x65\144", $Zg)) {
            goto dj;
        }
        $nj = $Zg["\x69\x73\x4d\165\x6c\x74\x69\x53\151\x74\145\120\x6c\x75\x67\x69\x6e\122\x65\x71\165\145\163\164\x65\144"];
        dj:
        $C_ = count(Utilities::get_sites());
        if ($nj && array_key_exists("\156\157\x4f\x66\123\165\x62\123\151\x74\145\x73", $Zg) && $C_ <= $Zg["\x6e\157\117\x66\x53\165\x62\x53\151\x74\145\163"]) {
            goto bp;
        }
        $Kp = $Zg["\156\x6f\x4f\146\x53\165\142\x53\x69\164\x65\163"];
        $Z1 = get_site_option("\155\x6f\x5f\x73\141\x6d\154\137\x63\165\163\164\157\155\x65\162\137\164\157\x6b\x65\x6e");
        update_site_option("\x6e\x6f\137\x73\x62\163", AESEncryption::encrypt_data($Kp, $Z1));
        update_site_option("\x73\x6d\154\137\x6c\x6b", AESEncryption::encrypt_data($wC, $Z1));
        update_site_option("\163\x69\x74\x65\x5f\143\x6b\137\x6c", AESEncryption::encrypt_data("\164\x72\165\145", $Z1));
        update_site_option("\155\x6f\x5f\x73\x61\155\154\x5f\x6d\x65\163\x73\x61\x67\x65", "\x59\157\165\x20\x68\141\166\145\x20\160\165\162\143\150\141\x73\x65\144\x20\x74\150\145\40\x6c\151\143\x65\156\x73\145\40\x66\x6f\x72\40\74\142\x3e" . $Zg["\156\x6f\117\x66\123\x75\x62\x53\151\164\x65\x73"] . "\x20\x73\151\x74\x65\163\x3c\57\x62\x3e\x2e\40\102\165\164\40\x79\x6f\165\x20\150\x61\x76\145\40\x3c\x62\76" . $C_ . "\40\x3c\57\142\76\x73\151\164\x65\163\x20\x69\156\40\x79\x6f\165\162\40\155\x75\x6c\x74\151\x73\x69\x74\x65\x20\x6e\145\164\x77\x6f\162\x6b\x2e\x20\x3c\141\40\150\162\145\x66\x3d\42\43\42\x20\157\x6e\x63\x6c\x69\143\153\x3d\x22\147\x65\x74\165\160\147\x72\141\144\x65\x6c\x69\143\145\x6e\x73\145\x73\x66\157\162\155\50\51\42\76\103\154\x69\x63\153\40\x68\145\x72\145\x3c\57\141\x3e\40\164\157\40\142\x75\171\40\154\151\x63\145\156\x73\145\40\146\157\x72\x20\155\157\x72\145\40\163\151\164\145\x73\x2e");
        $this->mo_saml_show_error_message();
        goto ex;
        bp:
        $Kp = $Zg["\156\x6f\x4f\146\123\165\142\x53\x69\x74\x65\163"];
        $Z1 = get_site_option("\x6d\x6f\x5f\163\x61\155\154\x5f\143\x75\x73\164\x6f\155\x65\162\x5f\164\x6f\x6b\145\156");
        if (!array_key_exists("\x6e\157\x4f\x66\125\163\x65\162\163", $Zg)) {
            goto Pw;
        }
        $bC = $Zg["\156\157\117\146\125\x73\145\x72\163"];
        Pw:
        $b1 = $EF->mo_saml_verify_license($wC, $this);
        if ($b1) {
            goto Zk;
        }
        return;
        Zk:
        $Zg = json_decode($b1, true);
        if (strcasecmp($Zg["\x73\x74\x61\164\x75\x73"], "\x53\x55\x43\103\105\x53\123") == 0) {
            goto nM;
        }
        if (strcasecmp($Zg["\x73\x74\x61\164\165\x73"], "\106\101\x49\114\x45\104") == 0) {
            goto my;
        }
        update_site_option("\x6d\x6f\x5f\163\x61\x6d\x6c\x5f\x6d\x65\x73\x73\141\x67\x65", "\101\x6e\x20\145\x72\x72\157\x72\x20\157\143\143\165\x72\x65\144\40\167\150\151\x6c\x65\x20\x70\x72\157\143\x65\163\163\151\x6e\147\40\x79\x6f\165\162\x20\x72\145\161\x75\145\163\164\56\x20\120\154\145\141\x73\x65\40\x54\162\x79\40\141\147\141\151\156\56");
        $this->mo_saml_show_error_message();
        goto Ym;
        my:
        if (strcasecmp($Zg["\x6d\145\163\163\141\147\x65"], "\x43\x6f\144\x65\x20\150\x61\x73\40\x45\170\160\151\x72\145\x64") == 0) {
            goto ZZ;
        }
        update_site_option("\155\x6f\x5f\163\x61\x6d\154\x5f\x6d\x65\163\163\x61\x67\145", "\x59\157\x75\x20\150\141\x76\145\x20\145\156\x74\x65\162\145\x64\x20\141\x6e\x20\151\x6e\x76\x61\x6c\x69\x64\x20\x6c\151\143\145\156\x73\x65\x20\153\145\171\x2e\x20\x50\x6c\x65\x61\x73\x65\x20\145\x6e\164\x65\162\x20\141\40\x76\141\154\x69\144\x20\154\151\x63\x65\156\x73\x65\40\153\x65\x79\x2e");
        goto Nr;
        ZZ:
        update_site_option("\155\x6f\x5f\x73\x61\155\154\x5f\155\x65\163\163\141\147\145", "\x4c\151\x63\x65\156\163\145\x20\153\145\171\x20\171\157\165\40\x68\x61\166\x65\x20\x65\x6e\164\145\x72\x65\144\40\x68\141\x73\x20\141\x6c\162\x65\x61\144\x79\40\142\145\145\156\x20\x75\x73\145\x64\56\40\120\x6c\145\141\163\x65\x20\145\x6e\x74\145\162\x20\x61\x20\x6b\x65\x79\40\167\x68\151\x63\150\40\150\141\163\40\x6e\x6f\x74\40\x62\x65\145\x6e\x20\x75\163\145\144\x20\142\x65\146\157\162\x65\x20\x6f\156\x20\141\156\x79\x20\157\164\150\x65\162\40\151\x6e\163\164\141\x6e\143\145\x20\x6f\x72\x20\151\x66\40\171\x6f\x75\40\x68\141\x76\x65\40\x65\170\141\165\163\x74\x65\x64\x20\141\154\154\40\171\157\165\x72\x20\153\x65\171\163\x20\x74\x68\x65\156\x20\74\x61\x20\x68\162\145\x66\75\42\x23\42\x20\157\x6e\x63\x6c\151\x63\x6b\x3d\x22\x67\145\x74\165\160\x67\x72\x61\144\145\154\151\143\x65\x6e\163\145\163\146\157\162\155\50\x29\x22\x20\x3e\x43\154\x69\143\153\40\x68\145\x72\x65\74\57\x61\x3e\x20\x74\x6f\x20\x62\x75\x79\40\x6d\157\162\x65\x2e");
        Nr:
        $this->mo_saml_show_error_message();
        Ym:
        goto m9;
        nM:
        if (!array_key_exists("\x6e\157\117\146\125\163\145\162\163", $Zg)) {
            goto Pt;
        }
        update_site_option("\155\x6f\137\x73\141\155\x6c\x5f\165\163\162\x5f\154\x6d\x74", AESEncryption::encrypt_data($bC, $Z1));
        Pt:
        update_site_option("\x73\x6d\154\137\x6c\x6b", AESEncryption::encrypt_data($wC, $Z1));
        update_site_option("\x6e\157\x5f\163\142\x73", AESEncryption::encrypt_data($Kp, $Z1));
        update_site_option("\x73\x69\x74\x65\137\x63\153\x5f\x6c", AESEncryption::encrypt_data("\x74\x72\x75\145", $Z1));
        update_site_option("\164\x5f\163\x69\x74\145\x5f\x73\164\x61\164\165\163", AESEncryption::encrypt_data("\x66\x61\x6c\x73\145", $Z1));
        update_site_option("\x6d\157\137\163\141\155\154\137\155\145\x73\163\x61\147\x65", "\131\157\x75\x72\x20\154\151\143\x65\156\x73\x65\40\x69\163\x20\166\145\162\151\x66\x69\x65\144\x2e\40\131\157\x75\40\143\x61\156\x20\x6e\x6f\167\x20\x73\145\x74\165\160\x20\164\x68\x65\x20\160\154\x75\x67\x69\x6e\56");
        $Z1 = get_site_option("\155\x6f\137\x73\141\155\x6c\137\143\x75\163\164\x6f\155\145\162\x5f\164\x6f\x6b\x65\x6e");
        update_site_option("\x74\x5f\163\151\164\x65\137\x73\x74\x61\x74\x75\163", AESEncryption::encrypt_data("\146\x61\154\x73\145", $Z1));
        $this->mo_saml_show_success_message();
        m9:
        ex:
        I2:
        kL:
        if (!(isset($_REQUEST["\165\x70\x64\141\x74\x65"]) && "\141\x64\x64\x65\144" == $_REQUEST["\165\x70\x64\141\164\145"] && get_site_option("\x6d\x6f\x5f\163\141\155\x6c\137\x65\x6e\x61\142\154\145\137\x73\163\157\x5f\x6e\145\167\x5f\x73\151\x74\x65") == "\164\162\x75\x65")) {
            goto rZ;
        }
        $lF = isset($_REQUEST["\x62\154\x6f\147\x5b\144\157\155\x61\x69\x6e\x5d"]) ? $_REQUEST["\142\154\157\147\x5b\x64\x6f\x6d\x61\x69\x6e\135"] : '';
        if (!isset($_REQUEST["\x69\x64"])) {
            goto Er;
        }
        $iy = $_REQUEST["\151\x64"];
        Er:
        if (empty($iy)) {
            goto Wd;
        }
        $gn = Utilities::get_active_sites();
        if (in_array($iy, $gn)) {
            goto kd;
        }
        array_push($gn, $iy);
        kd:
        update_site_option("\155\x6f\x5f\x65\156\141\142\x6c\145\x5f\x73\163\x6f\x5f\x73\x69\164\145\x73", $gn);
        Wd:
        rZ:
        Wo:
        if (mo_saml_is_trial_active()) {
            goto sX;
        }
        if (!site_check()) {
            goto wZ;
        }
        goto ZJ;
        sX:
        if (!decryptSamlElement()) {
            goto XV;
        }
        $Z1 = get_site_option("\x6d\x6f\137\163\x61\155\x6c\137\x63\x75\x73\x74\157\155\x65\162\137\164\157\153\145\x6e");
        update_site_option("\164\x5f\x73\151\164\x65\x5f\x73\x74\141\x74\x75\x73", AESEncryption::encrypt_data("\x66\x61\154\163\x65", $Z1));
        XV:
        goto ZJ;
        wZ:
        delete_site_option("\x6d\157\x5f\x73\x61\x6d\154\137\x65\x6e\x61\x62\154\x65\137\x6c\157\x67\151\156\x5f\x72\145\144\x69\x72\x65\143\164");
        delete_site_option("\x6d\x6f\137\163\141\155\154\x5f\141\154\154\x6f\167\x5f\167\160\x5f\x73\x69\x67\x6e\x69\x6e");
        delete_site_option("\155\157\137\163\x61\x6d\x6c\x5f\x72\145\x67\151\x73\x74\145\162\x65\x64\x5f\x6f\156\x6c\x79\137\141\143\143\x65\163\x73");
        delete_site_option("\155\157\137\x73\141\155\154\x5f\x66\x6f\x72\143\145\x5f\141\x75\x74\150\x65\156\x74\x69\143\141\x74\151\157\x6e");
        ZJ:
    }
    function create_customer()
    {
        $EF = new CustomerSaml();
        $Z8 = json_decode($EF->create_customer($this), true);
        if (strcasecmp($Z8["\x73\x74\141\164\165\x73"], "\103\x55\x53\x54\117\x4d\105\x52\x5f\x55\123\x45\x52\x4e\101\x4d\x45\x5f\101\114\122\x45\x41\x44\x59\x5f\105\130\111\123\x54\123") == 0) {
            goto DI;
        }
        if (!(strcasecmp($Z8["\x73\x74\141\x74\x75\x73"], "\x53\125\103\103\x45\x53\x53") == 0)) {
            goto F6;
        }
        update_site_option("\155\157\137\163\141\155\154\137\141\144\x6d\x69\156\137\x63\x75\x73\x74\x6f\155\x65\162\x5f\153\x65\171", $Z8["\151\144"]);
        update_site_option("\x6d\x6f\137\163\x61\155\x6c\x5f\x61\x64\x6d\x69\156\x5f\141\160\x69\137\x6b\x65\x79", $Z8["\x61\x70\x69\113\145\x79"]);
        update_site_option("\155\x6f\x5f\163\141\155\x6c\x5f\143\165\163\164\157\x6d\145\x72\137\x74\x6f\153\x65\x6e", $Z8["\x74\x6f\153\145\x6e"]);
        update_site_option("\x6d\x6f\x5f\163\141\x6d\154\137\x61\x64\155\151\x6e\x5f\160\x61\x73\163\x77\157\162\144", '');
        update_site_option("\x6d\x6f\137\163\141\x6d\x6c\137\155\145\163\x73\x61\x67\x65", "\x54\150\141\156\x6b\40\171\157\x75\x20\146\157\x72\x20\162\145\147\151\163\x74\x65\x72\151\x6e\x67\40\x77\151\164\150\40\x6d\151\156\151\157\162\x61\156\147\145\56");
        update_site_option("\x6d\x6f\137\x73\141\155\154\x5f\162\x65\x67\x69\x73\164\x72\x61\164\x69\157\156\x5f\163\164\141\x74\165\x73", '');
        delete_site_option("\x6d\157\137\163\x61\x6d\x6c\x5f\x76\x65\x72\x69\146\x79\137\x63\x75\163\x74\x6f\x6d\x65\x72");
        delete_site_option("\x6d\157\x5f\163\141\155\x6c\137\x6e\145\167\137\x72\145\147\151\x73\164\x72\x61\x74\151\157\x6e");
        $this->mo_saml_show_success_message();
        F6:
        goto Lz;
        DI:
        $this->get_current_customer();
        Lz:
        update_site_option("\155\x6f\x5f\x73\141\155\154\137\x61\144\155\151\x6e\137\x70\x61\x73\163\167\x6f\162\x64", '');
    }
    function get_current_customer()
    {
        $EF = new CustomerSaml();
        $Zg = $EF->get_customer_key($this);
        if ($Zg) {
            goto oU;
        }
        return;
        oU:
        $Z8 = json_decode($Zg, true);
        if (json_last_error() == JSON_ERROR_NONE) {
            goto MI;
        }
        update_site_option("\155\x6f\x5f\x73\141\x6d\x6c\137\x6d\145\163\x73\x61\x67\145", "\131\157\165\x20\141\154\162\x65\x61\144\171\x20\x68\x61\x76\145\x20\141\x6e\40\x61\x63\x63\157\165\x6e\164\40\x77\151\x74\x68\40\155\151\156\151\117\x72\141\156\x67\x65\x2e\x20\x50\x6c\x65\x61\163\145\40\x65\x6e\164\145\162\40\x61\40\166\141\x6c\151\x64\40\160\141\x73\163\167\x6f\x72\144\x2e");
        update_site_option("\x6d\x6f\137\163\141\155\154\x5f\166\145\162\151\146\x79\137\143\165\163\164\157\155\x65\162", "\x74\162\x75\145");
        delete_site_option("\155\157\137\163\x61\x6d\154\137\156\x65\167\x5f\162\x65\x67\x69\163\164\x72\141\x74\151\x6f\156");
        $this->mo_saml_show_error_message();
        goto N9;
        MI:
        update_site_option("\x6d\157\x5f\x73\141\x6d\154\137\x61\144\155\151\156\137\143\165\x73\164\x6f\155\x65\162\x5f\x6b\145\x79", $Z8["\x69\144"]);
        update_site_option("\155\x6f\x5f\x73\x61\155\x6c\137\141\x64\155\x69\156\137\141\160\x69\x5f\x6b\x65\x79", $Z8["\x61\160\x69\113\145\171"]);
        update_site_option("\x6d\x6f\137\x73\x61\x6d\154\137\x63\165\163\x74\x6f\155\x65\x72\137\x74\x6f\x6b\145\156", $Z8["\164\x6f\x6b\x65\x6e"]);
        update_site_option("\x6d\x6f\x5f\163\141\155\154\x5f\141\144\155\x69\x6e\x5f\160\x61\163\x73\x77\157\162\144", '');
        update_site_option("\155\157\x5f\163\x61\155\x6c\x5f\155\x65\163\x73\141\x67\x65", "\x59\157\165\x72\x20\x61\x63\x63\157\x75\x6e\164\40\x68\x61\x73\x20\142\145\145\x6e\x20\x72\x65\x74\x72\151\145\166\145\x64\40\x73\x75\143\x63\145\163\163\146\x75\154\x6c\171\56");
        delete_site_option("\x6d\157\x5f\x73\141\155\154\137\166\145\x72\151\146\x79\x5f\143\x75\x73\164\x6f\x6d\x65\x72");
        delete_site_option("\155\157\x5f\x73\x61\x6d\x6c\137\156\145\167\x5f\x72\145\x67\151\x73\x74\x72\x61\164\x69\x6f\x6e");
        $this->mo_saml_show_success_message();
        N9:
    }
    public function mo_saml_check_empty_or_null($zF)
    {
        if (!(!isset($zF) || empty($zF))) {
            goto DY;
        }
        return true;
        DY:
        return false;
    }
    function miniorange_sso_menu()
    {
        $yp = add_menu_page("\x4d\x4f\40\x53\101\115\x4c\40\123\x65\x74\x74\151\x6e\147\163\40" . __("\103\x6f\156\x66\x69\147\x75\162\x65\40\x53\101\115\x4c\x20\111\144\x65\x6e\x74\151\164\171\x20\120\x72\x6f\x76\151\x64\x65\x72\x20\146\x6f\x72\x20\123\x53\x4f", "\155\157\x5f\x73\x61\x6d\x6c\137\163\x65\x74\x74\151\x6e\x67\x73"), "\x6d\151\156\151\x4f\x72\141\x6e\x67\x65\x20\x53\101\x4d\114\x20\62\x2e\x30\40\x53\x53\117", "\141\144\x6d\x69\156\x69\163\x74\162\141\164\x6f\x72", "\x6d\157\137\x73\141\x6d\x6c\137\x73\145\164\164\151\156\x67\x73", array($this, "\x6d\157\x5f\x6c\x6f\x67\151\x6e\x5f\167\151\144\x67\145\164\x5f\x73\x61\x6d\154\137\157\x70\x74\151\157\x6e\x73"), plugin_dir_url(__FILE__) . "\x69\x6d\141\x67\145\x73\57\155\x69\x6e\x69\x6f\162\141\156\147\145\x2e\x70\x6e\x67");
    }
    function mo_saml_redirect_for_authentication($Hr)
    {
        if (!mo_saml_is_customer_license_key_verified()) {
            goto uD;
        }
        $pK = get_site_option("\x73\x61\x6d\154\x5f\x73\163\157\137\x73\x65\164\x74\151\156\147\x73");
        $Lm = Utilities::get_active_sites();
        $NU = get_current_blog_id();
        if (in_array($NU, $Lm)) {
            goto f2;
        }
        return;
        f2:
        if (!(empty($pK[$NU]) && !empty($pK["\x44\105\x46\x41\x55\x4c\124"]))) {
            goto Yl;
        }
        $pK[$NU] = $pK["\104\105\x46\x41\125\114\124"];
        Yl:
        if (isset($pK[$NU]["\155\157\x5f\163\141\155\x6c\x5f\162\145\x67\151\x73\164\x65\162\145\x64\137\157\156\x6c\171\x5f\x61\143\143\145\x73\163"]) && $pK[$NU]["\155\x6f\x5f\x73\x61\155\x6c\137\162\145\147\x69\163\164\x65\162\x65\144\x5f\x6f\156\x6c\x79\x5f\141\x63\x63\x65\163\x73"] == "\x74\162\x75\x65" || isset($pK[$NU]["\155\157\137\x73\141\155\x6c\x5f\x65\156\x61\x62\154\x65\137\x6c\157\147\151\x6e\137\162\145\x64\x69\162\x65\143\x74"]) && $pK[$NU]["\x6d\x6f\x5f\x73\141\x6d\154\x5f\x65\x6e\x61\142\x6c\145\137\x6c\157\x67\x69\156\137\162\145\x64\x69\162\145\143\164"] == "\x74\162\x75\145") {
            goto Qw;
        }
        if (!(isset($pK[$NU]["\x6d\157\x5f\x73\x61\155\x6c\x5f\162\145\144\x69\162\145\143\164\137\164\157\137\x77\160\137\154\x6f\x67\x69\x6e"]) and $pK[$NU]["\x6d\157\137\163\x61\x6d\x6c\137\x72\x65\x64\151\162\145\143\164\x5f\x74\x6f\x5f\167\160\x5f\154\157\x67\x69\156"] == "\164\x72\165\145")) {
            goto FJ;
        }
        if (!(mo_saml_is_sp_configured() && !is_user_logged_in())) {
            goto Nm;
        }
        $Oy = get_site_url($NU) . "\57\167\160\55\154\x6f\x67\x69\156\56\x70\x68\x70";
        if (empty($Hr)) {
            goto LG;
        }
        $Oy = $Oy . "\77\x72\x65\x64\151\162\x65\143\x74\x5f\164\157\x3d" . urlencode($Hr) . "\46\162\145\141\165\164\150\75\x31";
        LG:
        header("\x4c\157\x63\x61\164\151\x6f\156\72\x20" . $Oy);
        die;
        Nm:
        FJ:
        goto we;
        Qw:
        if (!(mo_saml_is_sp_configured() && !is_user_logged_in())) {
            goto Db;
        }
        $Wz = get_site_option("\x6d\x6f\137\163\x61\x6d\x6c\x5f\163\x70\137\142\x61\163\x65\137\165\x72\154");
        if (!empty($Wz)) {
            goto AR;
        }
        $Wz = get_network_site_url();
        AR:
        if (empty($pK[$NU]["\155\157\137\163\x61\155\x6c\137\x72\x65\x6c\x61\171\137\163\164\141\164\145"])) {
            goto ri;
        }
        $Hr = $pK[$NU]["\x6d\157\137\163\x61\155\x6c\x5f\162\145\x6c\141\171\x5f\x73\164\141\x74\x65"];
        ri:
        $rX = $Hr;
        $g0 = get_site_option("\x73\141\x6d\x6c\x5f\154\x6f\x67\x69\x6e\x5f\165\x72\x6c");
        $tV = !empty(get_site_option("\163\141\x6d\x6c\x5f\154\157\147\151\156\137\x62\151\156\144\151\156\x67\137\164\171\x70\145")) ? get_site_option("\163\141\x6d\x6c\x5f\154\x6f\x67\x69\156\x5f\x62\x69\156\144\151\156\147\137\164\x79\x70\145") : "\x48\164\x74\160\120\157\163\164";
        $pK = get_site_option("\163\x61\155\154\x5f\163\x73\157\x5f\163\x65\x74\x74\151\156\x67\x73");
        $NU = get_current_blog_id();
        if (!(empty($pK[$NU]) && !empty($pK["\104\105\106\x41\125\114\x54"]))) {
            goto To;
        }
        $pK[$NU] = $pK["\x44\x45\106\101\125\x4c\x54"];
        To:
        if (isset($pK[$NU]["\155\157\137\x73\x61\x6d\154\x5f\x66\157\162\x63\145\137\x61\x75\164\150\x65\156\164\151\143\141\x74\151\157\x6e"])) {
            goto Ck;
        }
        $zC = '';
        goto AS1;
        Ck:
        $zC = $pK[$NU]["\155\x6f\x5f\x73\x61\155\x6c\137\x66\157\x72\143\145\137\x61\165\164\x68\145\156\x74\x69\x63\141\164\x69\x6f\156"];
        AS1:
        $sq = $Wz . "\x2f";
        $iB = get_site_option("\x6d\157\x5f\x73\141\155\x6c\137\163\x70\137\145\156\x74\x69\x74\x79\137\151\x64");
        $Xq = get_site_option("\x73\x61\155\x6c\137\x6e\141\155\145\x69\x64\x5f\x66\157\162\x6d\141\x74");
        if (!empty($Xq)) {
            goto rk;
        }
        $Xq = "\61\x2e\61\72\x6e\141\x6d\x65\151\x64\55\146\x6f\x72\x6d\x61\x74\x3a\x75\156\163\160\x65\x63\151\146\x69\x65\x64";
        rk:
        if (!empty($iB)) {
            goto f0;
        }
        $iB = $Wz . "\57\167\x70\55\x63\157\156\164\145\x6e\164\x2f\x70\154\x75\x67\x69\x6e\x73\57\x6d\x69\156\x69\x6f\162\x61\x6e\x67\145\55\163\141\x6d\x6c\x2d\62\x30\x2d\163\151\x6e\147\154\x65\55\x73\151\x67\156\x2d\x6f\x6e\57";
        f0:
        $GW = Utilities::createAuthnRequest($sq, $iB, $g0, $zC, $tV, $Xq);
        $rX = mo_saml_relaystate_url($rX);
        if ($tV == "\110\x74\164\x70\x52\x65\144\x69\x72\x65\x63\x74") {
            goto Iu;
        }
        if (!(get_site_option("\x73\141\155\x6c\137\162\145\x71\x75\145\x73\164\x5f\x73\151\147\x6e\x65\144") == "\x75\156\143\150\x65\x63\x6b\145\x64")) {
            goto bC;
        }
        $Qv = base64_encode($GW);
        Utilities::postSAMLRequest($g0, $Qv, $rX);
        die;
        bC:
        $wZ = '';
        $z1 = '';
        $Qv = Utilities::signXML($GW, "\116\141\x6d\145\111\x44\x50\x6f\x6c\151\x63\x79");
        Utilities::postSAMLRequest($g0, $Qv, $rX);
        goto s0;
        Iu:
        $cJ = $g0;
        if (strpos($g0, "\77") !== false) {
            goto yR;
        }
        $cJ .= "\x3f";
        goto HM;
        yR:
        $cJ .= "\46";
        HM:
        if (!(get_site_option("\163\x61\155\154\x5f\x72\x65\x71\165\x65\x73\x74\137\x73\151\147\156\145\144") == "\x75\156\143\x68\x65\x63\153\145\144")) {
            goto bJ;
        }
        $cJ .= "\123\101\115\114\122\145\161\165\145\x73\164\x3d" . $GW . "\46\122\145\154\x61\171\x53\x74\x61\164\x65\x3d" . urlencode($rX);
        header("\x4c\157\x63\x61\164\x69\157\x6e\72\40" . $cJ);
        die;
        bJ:
        $GW = "\123\101\115\x4c\x52\x65\x71\x75\x65\163\x74\x3d" . $GW . "\x26\x52\145\x6c\x61\x79\x53\164\x61\x74\145\x3d" . urlencode($rX) . "\x26\x53\151\x67\x41\154\x67\75" . urlencode(XMLSecurityKey::RSA_SHA256);
        $nQ = array("\164\171\x70\145" => "\160\162\x69\x76\141\164\145");
        $Z1 = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, $nQ);
        $od = get_site_option("\x6d\x6f\137\x73\x61\155\x6c\137\x63\x75\162\x72\x65\156\x74\x5f\x63\x65\162\x74\x5f\x70\162\x69\x76\141\x74\145\137\153\x65\171");
        $Z1->loadKey($od, FALSE);
        $b4 = new XMLSecurityDSig();
        $sD = $Z1->signData($GW);
        $sD = base64_encode($sD);
        $cJ .= $GW . "\x26\x53\151\x67\156\141\164\165\162\x65\75" . urlencode($sD);
        header("\x4c\x6f\143\x61\x74\x69\157\x6e\x3a\40" . $cJ);
        die;
        s0:
        Db:
        we:
        uD:
    }
    function mo_saml_authenticate()
    {
        $dI = '';
        if (!isset($_REQUEST["\x72\145\144\151\x72\145\143\x74\137\164\x6f"])) {
            goto PU;
        }
        $dI = $_REQUEST["\x72\145\x64\151\x72\145\143\x74\137\x74\157"];
        PU:
        if (!is_user_logged_in()) {
            goto Xv;
        }
        if (!empty($dI)) {
            goto QB;
        }
        $Oy = saml_get_current_page_url();
        if (!(strpos($Oy, "\167\160\55\154\x6f\147\x69\156\56\x70\x68\160") !== false)) {
            goto kF;
        }
        $Oy = str_replace("\x77\160\x2d\x6c\157\147\151\x6e\56\160\150\x70", '', $Oy);
        if (!filter_var($Oy, FILTER_VALIDATE_URL)) {
            goto wF;
        }
        header("\114\157\x63\141\x74\x69\x6f\x6e\x3a\40" . $Oy);
        die;
        wF:
        kF:
        goto fD;
        QB:
        header("\114\x6f\x63\141\x74\x69\157\156\x3a\x20" . $dI);
        die;
        fD:
        Xv:
        global $blog_id;
        $pK = get_site_option("\163\141\155\154\x5f\163\x73\157\x5f\x73\145\164\164\151\156\x67\x73");
        $NU = get_current_blog_id();
        $Lm = Utilities::get_active_sites();
        if (in_array($NU, $Lm)) {
            goto r3;
        }
        return;
        r3:
        if (!(empty($pK[$NU]) && !empty($pK["\104\105\x46\101\125\x4c\124"]))) {
            goto go;
        }
        $pK[$NU] = $pK["\104\105\x46\x41\x55\x4c\x54"];
        go:
        if (!(isset($pK[$NU]["\155\x6f\137\163\141\155\x6c\137\145\156\141\x62\154\145\137\154\157\147\151\156\x5f\162\145\144\x69\x72\145\x63\164"]) and $pK[$NU]["\x6d\x6f\137\163\141\155\x6c\x5f\145\x6e\x61\x62\x6c\x65\x5f\154\x6f\147\x69\x6e\x5f\162\x65\x64\x69\x72\145\x63\164"] == "\x74\162\165\x65")) {
            goto Vo;
        }
        $Ho = isset($pK[$NU]["\x6d\x6f\137\163\141\x6d\x6c\x5f\142\141\x63\x6b\x64\157\x6f\162\137\x75\x72\x6c"]) ? trim($pK[$NU]["\155\157\137\163\x61\x6d\x6c\137\142\x61\143\153\144\x6f\x6f\x72\x5f\165\162\x6c"]) : "\x66\141\x6c\x73\x65";
        if (isset($_GET["\154\157\x67\147\145\144\x6f\165\164"]) && $_GET["\x6c\x6f\147\147\x65\144\x6f\165\x74"] == "\164\162\x75\x65") {
            goto Vc;
        }
        if (isset($pK[$NU]["\x6d\x6f\137\x73\141\x6d\154\137\x61\x6c\154\157\167\x5f\x77\x70\137\x73\x69\x67\x6e\151\x6e"]) and $pK[$NU]["\x6d\x6f\137\163\x61\x6d\x6c\137\141\154\x6c\157\x77\137\167\x70\x5f\163\x69\147\x6e\x69\x6e"] == "\164\x72\x75\145") {
            goto s2;
        }
        goto KN;
        Vc:
        header("\x4c\157\x63\x61\x74\x69\x6f\156\x3a\40" . get_network_site_url());
        die;
        goto KN;
        s2:
        if (isset($_GET["\x73\x61\155\x6c\137\163\163\157"]) && $_GET["\x73\x61\155\x6c\137\x73\x73\x6f"] == $Ho || isset($_POST["\163\x61\155\x6c\x5f\163\163\157"]) && $_POST["\x73\x61\x6d\x6c\137\x73\x73\157"] == $Ho) {
            goto Xo;
        }
        if (isset($_REQUEST["\x72\145\144\151\162\145\143\x74\x5f\x74\x6f"])) {
            goto Uh;
        }
        goto GJ;
        Xo:
        return;
        goto GJ;
        Uh:
        $dI = $_REQUEST["\162\145\144\x69\162\145\143\164\137\x74\x6f"];
        if (!(strpos($dI, "\x77\160\55\x61\x64\x6d\x69\156") !== false && strpos($dI, "\163\141\155\x6c\x5f\x73\x73\x6f\75" . $Ho) !== false)) {
            goto pi;
        }
        return;
        pi:
        GJ:
        KN:
        if (!empty($dI)) {
            goto eV;
        }
        $dI = saml_get_current_page_url();
        eV:
        $this->mo_saml_redirect_for_authentication($dI);
        Vo:
    }
    function mo_saml_auto_redirect()
    {
        if (!current_user_can("\162\x65\x61\x64")) {
            goto Yz;
        }
        return;
        Yz:
        global $blog_id;
        $pK = get_site_option("\x73\x61\x6d\x6c\137\163\163\x6f\137\163\x65\164\x74\x69\156\x67\x73");
        $NU = get_current_blog_id();
        $Lm = Utilities::get_active_sites();
        if (in_array($NU, $Lm)) {
            goto xi;
        }
        return;
        xi:
        if (!(empty($pK[$NU]) && !empty($pK["\104\105\106\x41\x55\x4c\x54"]))) {
            goto As1;
        }
        $pK[$NU] = $pK["\x44\x45\106\101\x55\114\x54"];
        As1:
        if (is_user_logged_in() && !empty($pK[$NU]["\x6d\x6f\137\163\x61\155\154\x5f\163\x75\x62\163\151\164\145\137\x61\143\x63\x65\163\x73\137\x64\145\156\x69\x65\144"])) {
            goto We;
        }
        if (!((isset($pK[$NU]["\155\x6f\137\163\141\x6d\154\x5f\x72\x65\147\151\x73\x74\x65\162\x65\144\x5f\x6f\x6e\154\171\x5f\141\x63\143\145\163\163"]) and $pK[$NU]["\155\157\x5f\163\141\x6d\x6c\x5f\162\x65\147\151\163\164\x65\x72\145\x64\137\x6f\156\154\x79\137\x61\143\x63\x65\x73\x73"] == "\164\x72\x75\x65") || (isset($pK[$NU]["\x6d\x6f\137\x73\x61\155\x6c\x5f\162\145\x64\x69\162\x65\143\x74\x5f\164\x6f\x5f\167\x70\137\154\x6f\147\151\x6e"]) and $pK[$NU]["\x6d\157\137\x73\x61\155\x6c\137\x72\145\144\x69\x72\145\143\164\137\164\157\x5f\x77\x70\x5f\x6c\x6f\147\151\x6e"] == "\164\162\165\x65"))) {
            goto rU;
        }
        $xM = $_SERVER["\110\124\x54\120\137\x48\x4f\123\x54"];
        if (!(substr($xM, -1) == "\x2f")) {
            goto dD;
        }
        $xM = substr($xM, 0, -1);
        dD:
        $EJ = $_SERVER["\122\105\121\x55\x45\x53\124\x5f\x55\x52\x49"];
        if (!(substr($EJ, 0, 1) == "\57")) {
            goto i9;
        }
        $EJ = substr($EJ, 1);
        i9:
        $Hr = "\x68\x74\x74\x70" . (isset($_SERVER["\110\x54\x54\120\x53"]) ? "\163" : '') . "\x3a\57\x2f" . $xM . "\x2f" . $EJ;
        $this->mo_saml_redirect_for_authentication($Hr);
        rU:
        goto qF;
        We:
        global $current_user;
        if (is_user_member_of_blog($current_user->ID)) {
            goto fX;
        }
        $this->mo_saml_admin_page_access_denied();
        fX:
        qF:
    }
    function mo_saml_modify_login_form()
    {
        $pK = get_site_option("\x73\141\155\x6c\x5f\x73\x73\x6f\137\163\x65\164\x74\x69\156\x67\x73");
        $NU = get_current_blog_id();
        $Lm = Utilities::get_active_sites();
        if (in_array($NU, $Lm)) {
            goto Je;
        }
        return;
        Je:
        if (!(empty($pK[$NU]) && !empty($pK["\104\105\106\x41\125\114\124"]))) {
            goto Vl;
        }
        $pK[$NU] = $pK["\x44\105\x46\x41\125\x4c\124"];
        Vl:
        $Ho = isset($pK[$NU]["\155\157\137\163\141\155\154\x5f\x62\141\143\x6b\x64\x6f\x6f\162\137\x75\x72\154"]) ? trim($pK[$NU]["\155\157\x5f\x73\141\155\x6c\137\x62\141\143\x6b\144\x6f\157\x72\137\x75\162\x6c"]) : "\146\141\x6c\163\145";
        echo "\x3c\151\x6e\160\x75\164\40\x74\x79\160\145\75\x22\x68\151\144\x64\145\x6e\42\x20\156\x61\x6d\145\x3d\42\163\141\155\x6c\x5f\x73\163\157\42\40\x76\x61\154\x75\x65\75\42" . $Ho . "\x22\x3e" . "\xa";
        if (!(isset($pK[$NU]["\x6d\157\x5f\163\x61\x6d\x6c\x5f\x61\x64\144\x5f\x73\163\x6f\137\x62\165\x74\164\157\x6e\x5f\167\x70"]) and $pK[$NU]["\155\x6f\x5f\163\x61\x6d\x6c\x5f\x61\x64\x64\137\163\163\157\x5f\142\165\164\164\157\x6e\137\x77\x70"] == "\x74\162\165\145")) {
            goto iF1;
        }
        $this->mo_saml_add_sso_button();
        iF1:
    }
    function mo_saml_add_sso_button()
    {
        $pK = get_site_option("\163\141\155\154\x5f\x73\x73\x6f\x5f\x73\145\164\164\151\x6e\x67\163");
        $NU = get_current_blog_id();
        $Lm = Utilities::get_active_sites();
        if (in_array($NU, $Lm)) {
            goto L6;
        }
        return;
        L6:
        if (!(empty($pK[$NU]) && !empty($pK["\104\x45\x46\101\x55\x4c\124"]))) {
            goto FA;
        }
        $pK[$NU] = $pK["\104\x45\106\x41\125\114\x54"];
        FA:
        if (is_user_logged_in()) {
            goto Bg;
        }
        $Wz = get_site_option("\x6d\157\137\163\x61\x6d\154\x5f\163\x70\137\142\141\163\x65\137\x75\162\x6c");
        if (!empty($Wz)) {
            goto NL;
        }
        $Wz = home_url();
        NL:
        $WT = isset($pK[$NU]["\155\157\x5f\x73\x61\x6d\154\137\x62\165\164\164\x6f\x6e\x5f\x77\x69\144\x74\x68"]) ? $pK[$NU]["\x6d\x6f\137\x73\141\x6d\154\x5f\142\165\x74\164\157\156\x5f\167\151\x64\x74\150"] : "\x31\x30\60";
        $Nt = isset($pK[$NU]["\155\157\x5f\163\x61\x6d\x6c\137\x62\x75\164\x74\157\x6e\x5f\x68\145\x69\x67\x68\164"]) ? $pK[$NU]["\x6d\x6f\137\163\x61\155\x6c\137\x62\165\164\x74\x6f\x6e\137\x68\145\151\x67\150\164"] : "\x35\60";
        $Kq = isset($pK[$NU]["\155\157\137\163\141\x6d\x6c\x5f\142\x75\x74\x74\157\156\x5f\x73\x69\x7a\x65"]) ? $pK[$NU]["\x6d\157\137\163\x61\155\154\137\x62\x75\x74\x74\x6f\156\x5f\x73\151\x7a\x65"] : "\x35\x30";
        $UZ = isset($pK[$NU]["\x6d\157\137\x73\x61\155\x6c\137\x62\x75\164\164\x6f\x6e\137\143\165\162\x76\145"]) ? $pK[$NU]["\155\157\137\x73\x61\x6d\x6c\137\142\x75\x74\x74\x6f\156\x5f\x63\165\162\166\x65"] : "\65";
        $qD = isset($pK[$NU]["\155\157\x5f\163\x61\155\x6c\x5f\x62\x75\x74\x74\157\x6e\137\x63\x6f\x6c\x6f\x72"]) ? $pK[$NU]["\x6d\x6f\x5f\163\x61\155\154\137\x62\x75\164\164\x6f\x6e\x5f\143\157\154\x6f\x72"] : "\x30\60\x38\65\142\141";
        $fM = isset($pK[$NU]["\155\x6f\137\163\141\155\x6c\137\x62\x75\x74\164\157\x6e\x5f\x74\150\145\155\x65"]) ? $pK[$NU]["\155\x6f\137\163\x61\x6d\154\x5f\x62\x75\x74\164\x6f\x6e\137\x74\150\x65\x6d\x65"] : "\x6c\157\x6e\x67\x62\x75\x74\x74\157\156";
        $D1 = isset($pK[$NU]["\155\157\x5f\x73\x61\x6d\154\137\142\165\x74\x74\157\x6e\137\x74\x65\x78\x74"]) ? $pK[$NU]["\155\x6f\x5f\x73\x61\x6d\154\x5f\142\x75\x74\x74\157\x6e\x5f\164\x65\170\164"] : (get_site_option("\163\141\x6d\x6c\137\151\x64\x65\x6e\164\x69\x74\x79\x5f\x6e\x61\x6d\x65") ? get_site_option("\163\141\155\x6c\137\151\144\x65\156\x74\x69\x74\x79\x5f\x6e\141\155\145") : "\x4c\157\147\x69\x6e");
        $Qn = isset($pK[$NU]["\155\x6f\137\x73\141\x6d\x6c\x5f\146\x6f\x6e\164\137\x63\x6f\x6c\157\162"]) ? $pK[$NU]["\x6d\157\x5f\x73\141\155\154\137\146\157\x6e\164\x5f\143\x6f\x6c\x6f\x72"] : "\146\146\x66\x66\146\146";
        $FY = isset($pK[$NU]["\155\x6f\137\163\x61\x6d\154\x5f\146\x6f\x6e\x74\137\163\151\x7a\x65"]) ? $pK[$NU]["\155\x6f\x5f\163\141\155\x6c\x5f\146\x6f\x6e\164\x5f\x73\151\172\145"] : "\62\60";
        $Qk = isset($pK[$NU]["\x73\163\x6f\x5f\x62\165\x74\x74\x6f\x6e\137\154\157\x67\151\x6e\x5f\x66\157\162\155\x5f\x70\157\163\x69\x74\151\x6f\x6e"]) ? $pK[$NU]["\x73\x73\x6f\x5f\142\x75\164\x74\157\156\137\154\157\147\x69\156\x5f\146\x6f\162\155\x5f\x70\x6f\163\151\x74\x69\x6f\156"] : "\141\x62\157\x76\x65";
        $n2 = "\74\151\156\160\165\164\x20\x74\x79\x70\145\x3d\x22\x62\x75\164\x74\157\156\42\x20\x6e\141\x6d\145\75\x22\155\x6f\137\163\x61\155\x6c\137\x77\x70\137\163\163\157\137\x62\165\x74\x74\157\156\x22\40\166\141\154\165\145\75\x22" . $D1 . "\x22\40\163\x74\x79\154\x65\x3d\x22";
        $OF = '';
        if ($fM == "\x6c\157\x6e\147\x62\x75\164\164\x6f\x6e") {
            goto cw;
        }
        if ($fM == "\x63\x69\162\x63\x6c\x65") {
            goto u0;
        }
        if ($fM == "\157\166\141\x6c") {
            goto NW;
        }
        if ($fM == "\x73\x71\165\141\162\145") {
            goto FM;
        }
        goto Uw;
        u0:
        $OF = $OF . "\x77\x69\x64\x74\x68\x3a" . $Kq . "\x70\x78\73";
        $OF = $OF . "\150\x65\151\x67\x68\x74\72" . $Kq . "\160\170\73";
        $OF = $OF . "\x62\157\162\144\x65\162\55\162\141\x64\x69\165\x73\x3a\x39\71\71\x70\x78\73";
        goto Uw;
        NW:
        $OF = $OF . "\x77\x69\144\164\x68\x3a" . $Kq . "\x70\x78\x3b";
        $OF = $OF . "\x68\145\151\x67\x68\x74\x3a" . $Kq . "\x70\170\x3b";
        $OF = $OF . "\142\x6f\162\x64\x65\x72\x2d\162\x61\144\x69\x75\x73\x3a\65\x70\170\x3b";
        goto Uw;
        FM:
        $OF = $OF . "\x77\x69\x64\164\x68\x3a" . $Kq . "\x70\170\73";
        $OF = $OF . "\150\x65\151\147\150\164\72" . $Kq . "\x70\x78\73";
        $OF = $OF . "\x62\x6f\x72\144\x65\162\x2d\x72\141\144\x69\165\163\x3a\60\160\x78\73";
        $OF = $OF . "\160\141\144\x64\x69\156\147\72\60\x70\x78\x3b";
        Uw:
        goto jI;
        cw:
        $OF = $OF . "\x77\x69\144\164\150\x3a" . $WT . "\160\x78\73";
        $OF = $OF . "\150\145\151\x67\x68\164\72" . $Nt . "\x70\170\x3b";
        $OF = $OF . "\142\157\162\x64\145\x72\55\162\141\x64\x69\x75\163\72" . $UZ . "\x70\x78\73";
        jI:
        $OF = $OF . "\142\x61\143\153\x67\162\157\x75\156\x64\x2d\143\x6f\x6c\x6f\162\x3a\x23" . $qD . "\x3b";
        $OF = $OF . "\x62\x6f\162\x64\145\x72\55\143\157\154\x6f\x72\x3a\164\x72\x61\156\x73\x70\141\x72\x65\156\x74\73";
        $OF = $OF . "\x63\157\x6c\x6f\162\x3a\x23" . $Qn . "\x3b";
        $OF = $OF . "\146\157\156\164\55\x73\151\172\145\72" . $FY . "\x70\x78\x3b";
        $n2 = $n2 . $OF . "\42\x2f\76";
        $dI = '';
        if (!isset($_GET["\x72\145\144\151\x72\145\143\164\137\x74\157"])) {
            goto nI;
        }
        $dI = urlencode($_GET["\x72\145\144\151\x72\145\143\x74\x5f\164\157"]);
        nI:
        $WV = "\x3c\x61\40\x68\162\145\x66\75\x22" . $Wz . "\57\77\x6f\x70\164\x69\157\156\75\163\x61\x6d\x6c\137\165\163\x65\x72\137\154\157\x67\151\156\46\x72\145\144\151\162\x65\x63\164\137\x74\x6f\x3d" . $dI . "\x22\40\163\x74\171\x6c\x65\75\42\x74\x65\170\x74\55\144\x65\x63\x6f\x72\x61\x74\x69\x6f\x6e\72\x6e\157\156\x65\42\76" . $n2 . "\74\57\141\76";
        $WV = "\74\x64\151\166\x20\163\164\171\154\145\75\x22\x70\141\x64\144\151\156\x67\x3a\61\60\160\x78\x3b\42\x3e" . $WV . "\x3c\x2f\x64\x69\166\76";
        if ($Qk == "\x61\x62\x6f\x76\x65") {
            goto FC;
        }
        $WV = "\x3c\144\x69\x76\x20\x69\144\75\42\x73\163\157\x5f\x62\x75\164\164\157\x6e\42\40\x73\164\171\x6c\x65\75\42\164\x65\170\x74\x2d\x61\x6c\151\147\156\x3a\143\x65\156\x74\x65\162\42\x3e\74\144\x69\166\40\x73\164\171\x6c\x65\75\x22\x70\141\144\x64\x69\x6e\x67\x3a\65\x70\x78\73\x66\x6f\x6e\x74\x2d\163\x69\172\x65\72\x31\x34\x70\x78\x3b\42\76\74\x62\76\x4f\x52\74\x2f\x62\x3e\x3c\x2f\144\151\x76\76" . $WV . "\74\x2f\x64\x69\166\76\x3c\142\x72\57\76";
        goto FV;
        FC:
        $WV = "\x3c\x64\151\166\40\151\144\75\42\163\163\157\x5f\x62\165\x74\164\x6f\156\42\40\163\164\x79\154\x65\x3d\x22\164\145\x78\164\x2d\141\x6c\x69\x67\156\x3a\x63\x65\x6e\164\145\162\42\x3e" . $WV . "\x3c\144\x69\x76\40\x73\x74\171\154\145\x3d\x22\x70\x61\144\144\151\156\x67\x3a\65\x70\170\x3b\146\157\156\x74\x2d\x73\x69\172\x65\x3a\61\x34\x70\x78\x3b\x22\x3e\x3c\x62\x3e\117\122\74\57\142\76\x3c\57\144\x69\166\x3e\x3c\x2f\144\x69\x76\76\74\142\x72\x2f\76";
        $WV = $WV . "\x3c\x73\143\162\x69\x70\164\76\15\12\x9\11\11\11\x76\x61\162\x20\44\x65\154\x65\155\145\156\164\x20\x3d\x20\x6a\x51\165\x65\x72\171\50\42\x23\165\x73\x65\162\137\x6c\x6f\x67\x69\x6e\42\x29\73\xd\xa\11\x9\11\11\152\x51\x75\x65\x72\x79\x28\42\x23\163\x73\157\137\x62\x75\164\x74\x6f\x6e\42\x29\x2e\151\x6e\163\x65\162\x74\102\x65\146\x6f\162\145\x28\x6a\x51\x75\x65\162\x79\x28\x22\x6c\141\142\x65\x6c\133\x66\x6f\x72\x3d\x27\x22\x2b\x24\x65\154\145\155\145\x6e\x74\56\x61\164\x74\162\50\x27\151\x64\x27\51\53\x22\47\135\42\x29\x29\73\15\12\x9\x9\x9\x9\x3c\x2f\163\x63\x72\151\160\164\x3e";
        FV:
        echo $WV;
        Bg:
    }
    function mo_get_saml_shortcode()
    {
        $pK = get_site_option("\163\x61\x6d\154\x5f\x73\x73\x6f\x5f\163\x65\x74\x74\151\x6e\147\x73");
        $NU = get_current_blog_id();
        $Lm = Utilities::get_active_sites();
        if (in_array($NU, $Lm)) {
            goto zY;
        }
        return;
        zY:
        if (!(empty($pK[$NU]) && !empty($pK["\x44\105\x46\x41\x55\114\124"]))) {
            goto wN;
        }
        $pK[$NU] = $pK["\x44\105\106\101\125\x4c\x54"];
        wN:
        if (!is_user_logged_in()) {
            goto K3;
        }
        $current_user = wp_get_current_user();
        $e2 = "\110\x65\x6c\154\x6f\x2c";
        if (empty($pK[$NU]["\155\157\x5f\x73\141\155\154\137\x63\165\x73\164\157\x6d\x5f\147\x72\x65\145\x74\151\x6e\147\137\164\145\170\x74"])) {
            goto FbN;
        }
        $e2 = $pK[$NU]["\x6d\x6f\x5f\x73\141\155\x6c\137\143\x75\x73\x74\x6f\155\x5f\x67\162\x65\145\x74\151\156\x67\137\x74\145\x78\x74"];
        FbN:
        $pw = '';
        if (empty($pK[$NU]["\155\x6f\x5f\163\x61\x6d\x6c\x5f\147\x72\145\x65\x74\151\156\x67\137\x6e\141\155\x65"])) {
            goto oO;
        }
        switch ($pK[$NU]["\x6d\x6f\x5f\163\141\x6d\x6c\137\147\162\145\x65\164\151\x6e\147\137\156\141\155\145"]) {
            case "\125\123\x45\122\116\101\x4d\105":
                $pw = $current_user->user_login;
                goto g_;
            case "\105\x4d\x41\x49\x4c":
                $pw = $current_user->user_email;
                goto g_;
            case "\106\116\x41\115\105":
                $pw = $current_user->user_firstname;
                goto g_;
            case "\114\x4e\x41\115\x45":
                $pw = $current_user->user_lastname;
                goto g_;
            case "\106\x4e\101\x4d\105\x5f\114\116\101\x4d\x45":
                $pw = $current_user->user_firstname . "\40" . $current_user->user_lastname;
                goto g_;
            case "\114\116\101\115\x45\x5f\x46\116\x41\115\105":
                $pw = $current_user->user_lastname . "\x20" . $current_user->user_firstname;
                goto g_;
            default:
                $pw = $current_user->user_login;
        }
        dL:
        g_:
        oO:
        if (!empty(trim($pw))) {
            goto hK;
        }
        $pw = $current_user->user_login;
        hK:
        $Vo = $e2 . "\40" . $pw;
        $tD = "\114\157\147\x6f\x75\x74";
        if (empty($pK[$NU]["\x6d\157\x5f\163\x61\155\154\x5f\x63\x75\163\164\157\155\x5f\154\157\147\157\165\164\x5f\164\x65\x78\x74"])) {
            goto uU;
        }
        $tD = $pK[$NU]["\155\x6f\x5f\x73\x61\155\154\x5f\x63\165\x73\164\x6f\155\137\154\157\147\x6f\165\164\x5f\164\x65\x78\x74"];
        uU:
        $WV = $Vo . "\x20\x7c\40\x3c\x61\40\x68\x72\x65\146\75\x22" . wp_logout_url(home_url()) . "\x22\x20\x74\x69\164\x6c\145\75\x22\154\x6f\x67\x6f\165\164\x22\x20\x3e" . $tD . "\x3c\x2f\x61\x3e\x3c\x2f\x6c\151\76";
        goto OJ;
        K3:
        $Wz = get_site_option("\155\x6f\x5f\x73\x61\x6d\154\x5f\163\x70\x5f\142\x61\x73\145\x5f\x75\162\154");
        if (!empty($Wz)) {
            goto U5;
        }
        $Wz = home_url();
        U5:
        if (mo_saml_is_sp_configured() && mo_saml_is_customer_license_key_verified()) {
            goto I7;
        }
        $WV = "\123\x50\40\151\x73\x20\x6e\x6f\x74\x20\143\x6f\156\146\151\147\x75\162\145\x64\x2e";
        goto I9;
        I7:
        $uJ = "\x4c\x6f\147\151\156\40\x77\151\164\150\x20" . get_site_option("\163\x61\x6d\x6c\137\x69\144\145\x6e\164\151\164\x79\137\x6e\141\x6d\145");
        if (empty($pK[$NU]["\x6d\157\137\163\141\x6d\154\137\x63\165\x73\x74\157\x6d\x5f\x6c\x6f\x67\x69\x6e\137\x74\x65\x78\x74"])) {
            goto iY;
        }
        $uJ = $pK[$NU]["\x6d\x6f\x5f\163\141\x6d\154\137\x63\x75\x73\x74\157\155\137\x6c\157\147\x69\x6e\x5f\x74\145\x78\x74"];
        iY:
        $bj = get_site_option("\163\x61\x6d\x6c\x5f\151\x64\145\x6e\x74\x69\164\x79\137\156\x61\x6d\x65");
        $uJ = str_replace("\43\x23\111\104\120\43\x23", $bj, $uJ);
        $HC = false;
        if (!(isset($pK[$NU]["\x6d\x6f\x5f\x73\141\155\x6c\137\165\x73\x65\137\x62\165\164\x74\x6f\156\137\141\x73\x5f\x73\150\x6f\x72\x74\x63\157\x64\x65"]) and $pK[$NU]["\x6d\157\x5f\x73\141\155\x6c\x5f\165\163\x65\137\x62\165\x74\x74\157\156\137\141\x73\x5f\x73\150\157\162\x74\143\157\144\x65"] == "\164\x72\165\145")) {
            goto sb;
        }
        $HC = true;
        sb:
        if (!$HC) {
            goto c1;
        }
        $WT = isset($pK[$NU]["\x6d\157\x5f\163\141\x6d\x6c\137\142\x75\164\x74\x6f\156\137\x77\x69\144\164\150"]) ? $pK[$NU]["\155\157\x5f\x73\141\155\154\x5f\142\x75\x74\164\157\x6e\137\x77\x69\x64\x74\150"] : "\61\x30\x30";
        $Nt = isset($pK[$NU]["\x6d\x6f\137\163\141\x6d\x6c\x5f\x62\165\164\x74\157\x6e\137\x68\145\x69\147\150\x74"]) ? $pK[$NU]["\x6d\157\137\163\x61\155\x6c\137\142\x75\164\x74\157\156\x5f\150\145\151\x67\x68\x74"] : "\65\x30";
        $Kq = isset($pK[$NU]["\x6d\x6f\x5f\x73\141\x6d\154\x5f\x62\165\x74\x74\157\156\137\x73\151\x7a\x65"]) ? $pK[$NU]["\x6d\x6f\137\163\x61\x6d\154\x5f\x62\x75\164\164\157\156\x5f\163\x69\172\x65"] : "\x35\x30";
        $UZ = isset($pK[$NU]["\155\x6f\x5f\163\141\x6d\x6c\137\142\x75\x74\x74\x6f\x6e\137\x63\x75\x72\x76\x65"]) ? $pK[$NU]["\x6d\x6f\x5f\x73\141\x6d\154\137\x62\x75\164\164\157\156\x5f\x63\x75\x72\166\x65"] : "\65";
        $qD = isset($pK[$NU]["\x6d\157\x5f\163\141\x6d\x6c\x5f\142\165\164\164\157\156\137\x63\157\x6c\x6f\162"]) ? $pK[$NU]["\155\157\x5f\x73\x61\x6d\x6c\137\142\165\164\164\x6f\x6e\x5f\143\157\x6c\157\162"] : "\x30\x30\x38\x35\142\x61";
        $fM = isset($pK[$NU]["\x6d\x6f\x5f\163\x61\155\154\x5f\x62\x75\x74\164\x6f\x6e\x5f\164\150\x65\x6d\145"]) ? $pK[$NU]["\155\x6f\x5f\x73\141\155\x6c\137\x62\x75\164\164\157\x6e\x5f\164\150\x65\x6d\x65"] : "\154\x6f\x6e\x67\142\x75\164\x74\157\x6e";
        $D1 = isset($pK[$NU]["\155\x6f\137\x73\141\155\x6c\137\142\x75\x74\164\x6f\156\x5f\x74\145\x78\x74"]) ? $pK[$NU]["\155\157\x5f\x73\141\x6d\154\137\x62\x75\164\164\x6f\156\137\164\145\x78\164"] : (get_site_option("\x73\x61\155\x6c\x5f\x69\x64\145\156\164\151\164\x79\137\x6e\141\x6d\x65") ? get_site_option("\163\x61\155\154\x5f\x69\x64\145\156\164\x69\164\x79\x5f\156\141\x6d\x65") : "\x4c\x6f\147\151\156");
        $Qn = isset($pK[$NU]["\x6d\157\137\x73\x61\x6d\154\137\x66\x6f\156\164\x5f\x63\157\x6c\x6f\x72"]) ? $pK[$NU]["\155\x6f\x5f\x73\141\155\x6c\137\146\157\156\x74\x5f\143\157\x6c\x6f\162"] : "\146\146\146\x66\x66\x66";
        $kf = isset($pK[$NU]["\x6d\x6f\137\x73\x61\x6d\x6c\137\146\157\156\x74\137\x73\x69\x7a\145"]) ? $pK[$NU]["\x6d\157\x5f\163\141\x6d\154\x5f\146\x6f\156\x74\137\163\151\x7a\x65"] : "\x32\60";
        $uJ = "\74\151\156\x70\165\x74\40\x74\x79\x70\x65\75\x22\142\x75\x74\x74\x6f\x6e\42\40\x6e\x61\x6d\145\75\x22\155\157\x5f\163\141\155\154\x5f\x77\x70\x5f\163\163\x6f\137\142\165\164\164\x6f\x6e\x22\x20\x76\x61\154\165\145\x3d\42" . $D1 . "\x22\x20\x73\164\x79\154\145\x3d\x22";
        $OF = '';
        if ($fM == "\x6c\157\156\x67\142\165\x74\164\x6f\156") {
            goto XA;
        }
        if ($fM == "\x63\151\162\x63\x6c\x65") {
            goto E8;
        }
        if ($fM == "\157\x76\141\154") {
            goto J4;
        }
        if ($fM == "\163\161\x75\141\162\145") {
            goto hx;
        }
        goto g4;
        E8:
        $OF = $OF . "\x77\151\x64\164\x68\72" . $Kq . "\160\x78\x3b";
        $OF = $OF . "\150\x65\x69\x67\x68\164\72" . $Kq . "\x70\x78\x3b";
        $OF = $OF . "\x62\x6f\x72\144\145\162\55\x72\141\x64\x69\x75\163\x3a\x39\71\x39\x70\x78\73";
        goto g4;
        J4:
        $OF = $OF . "\167\x69\144\x74\150\x3a" . $Kq . "\160\170\x3b";
        $OF = $OF . "\x68\145\x69\147\x68\164\x3a" . $Kq . "\x70\x78\73";
        $OF = $OF . "\x62\x6f\x72\144\145\162\x2d\162\141\x64\x69\165\x73\x3a\65\x70\170\x3b";
        goto g4;
        hx:
        $OF = $OF . "\x77\151\144\x74\x68\x3a" . $Kq . "\x70\x78\73";
        $OF = $OF . "\150\x65\151\147\x68\x74\72" . $Kq . "\x70\x78\73";
        $OF = $OF . "\x62\157\162\x64\145\162\x2d\162\x61\x64\151\165\x73\72\60\160\x78\73";
        g4:
        goto So;
        XA:
        $OF = $OF . "\x77\151\144\164\x68\72" . $WT . "\160\x78\x3b";
        $OF = $OF . "\x68\145\x69\x67\x68\x74\x3a" . $Nt . "\160\170\73";
        $OF = $OF . "\142\x6f\x72\x64\145\x72\x2d\x72\x61\144\x69\x75\163\72" . $UZ . "\x70\170\73";
        So:
        $OF = $OF . "\142\141\x63\153\x67\162\157\165\x6e\x64\55\x63\x6f\x6c\x6f\162\x3a\x23" . $qD . "\73";
        $OF = $OF . "\x62\157\162\144\x65\x72\55\x63\157\154\157\x72\x3a\164\162\141\x6e\163\x70\141\162\x65\x6e\164\x3b";
        $OF = $OF . "\x63\x6f\154\157\162\x3a\43" . $Qn . "\x3b";
        $OF = $OF . "\x66\157\x6e\164\55\x73\x69\172\x65\72" . $kf . "\160\170\x3b";
        $OF = $OF . "\160\x61\144\144\x69\x6e\x67\x3a\60\x70\170\73";
        $uJ = $uJ . $OF . "\42\x2f\x3e";
        c1:
        $dI = urlencode(saml_get_current_page_url());
        $WV = "\74\141\x20\x68\162\x65\x66\75\x22" . $Wz . "\57\x3f\157\x70\x74\x69\x6f\x6e\x3d\163\141\x6d\x6c\x5f\165\163\145\x72\x5f\x6c\157\x67\151\156\46\162\145\x64\x69\162\x65\143\164\137\x74\157\x3d" . $dI . "\42";
        if (!$HC) {
            goto KM;
        }
        $WV = $WV . "\163\164\x79\x6c\145\75\x22\x74\x65\170\x74\x2d\x64\x65\x63\157\162\x61\164\x69\x6f\x6e\72\156\157\156\145\x3b\42";
        KM:
        $WV = $WV . "\76" . $uJ . "\74\x2f\x61\76";
        I9:
        OJ:
        return $WV;
    }
    function upload_metadata()
    {
        if (!(isset($_FILES["\155\145\x74\x61\x64\141\164\141\137\146\151\154\145"]) || isset($_POST["\x6d\x65\164\x61\x64\141\x74\x61\x5f\165\162\154"]))) {
            goto q0;
        }
        if (!empty($_FILES["\x6d\x65\164\x61\144\x61\x74\141\137\146\151\x6c\145"]["\164\x6d\160\x5f\x6e\141\x6d\145"])) {
            goto kk;
        }
        if (mo_saml_is_extension_installed("\143\165\162\154")) {
            goto NQ;
        }
        update_option("\x6d\x6f\137\x73\141\x6d\154\x5f\155\145\x73\163\141\x67\x65", "\x50\x48\120\x20\x63\x55\122\x4c\x20\x65\170\x74\145\x6e\x73\151\157\x6e\x20\151\x73\x20\x6e\157\x74\40\x69\156\163\x74\x61\154\154\x65\x64\x20\x6f\x72\x20\144\x69\x73\x61\x62\x6c\145\144\56\x20\103\141\x6e\156\x6f\x74\40\146\x65\164\x63\x68\40\x6d\145\x74\x61\144\141\x74\x61\x20\x66\162\x6f\x6d\x20\125\122\x4c\x2e");
        $this->mo_saml_show_error_message();
        return;
        NQ:
        $Oy = filter_var(htmlspecialchars($_POST["\155\x65\x74\141\144\141\164\x61\137\165\x72\x6c"]), FILTER_SANITIZE_URL);
        $iW = Utilities::mo_saml_wp_remote_call($Oy, array("\163\x73\x6c\x76\145\162\x69\x66\x79" => false), $this, true);
        if ($iW) {
            goto eB;
        }
        update_site_option("\x6d\157\x5f\163\141\155\x6c\x5f\155\x65\x73\x73\141\x67\145", "\120\x6c\145\x61\163\145\40\x70\162\x6f\x76\x69\144\x65\40\x61\x20\x76\x61\x6c\x69\x64\40\x6d\145\x74\141\x64\x61\x74\x61\40\x55\122\x4c\x2e");
        return;
        eB:
        if (!is_null($iW)) {
            goto E7;
        }
        $Cz = null;
        goto Qo;
        E7:
        $Cz = $iW;
        Qo:
        if (isset($_POST["\x73\x79\x6e\x63\x5f\x6d\145\164\x61\x64\141\x74\141"])) {
            goto hO;
        }
        delete_site_option("\x73\x61\155\x6c\137\155\x65\x74\x61\144\141\164\141\x5f\x75\162\154\x5f\x66\157\162\x5f\163\x79\156\x63");
        delete_site_option("\x73\141\x6d\x6c\x5f\155\145\164\x61\144\141\164\141\x5f\x73\x79\x6e\x63\137\151\156\164\x65\x72\166\x61\x6c");
        wp_unschedule_event(wp_next_scheduled("\155\145\x74\141\x64\141\164\x61\137\x73\x79\x6e\x63\137\x63\x72\x6f\x6e\137\x61\143\x74\151\157\156"), "\x6d\x65\x74\141\x64\x61\x74\x61\x5f\x73\x79\156\143\137\x63\162\x6f\156\x5f\141\143\164\x69\x6f\x6e");
        goto yY;
        hO:
        update_site_option("\163\x61\x6d\x6c\x5f\155\x65\164\x61\x64\141\164\141\x5f\165\x72\x6c\137\146\157\162\x5f\x73\x79\156\x63", htmlspecialchars($_POST["\155\145\164\x61\144\x61\x74\x61\137\x75\162\154"]));
        update_site_option("\x73\x61\155\x6c\x5f\x6d\x65\x74\x61\x64\x61\164\x61\x5f\163\171\156\x63\137\151\156\164\145\162\x76\x61\154", htmlspecialchars($_POST["\x73\171\156\x63\x5f\x69\156\164\145\162\166\x61\154"]));
        if (wp_next_scheduled("\155\x65\164\141\x64\x61\x74\141\x5f\x73\x79\156\x63\x5f\143\162\157\x6e\137\141\143\164\151\157\x6e")) {
            goto P_;
        }
        wp_schedule_event(time(), htmlspecialchars($_POST["\x73\x79\x6e\143\x5f\151\156\x74\x65\162\x76\141\154"]), "\x6d\145\x74\141\x64\x61\x74\141\137\163\x79\156\x63\137\x63\x72\x6f\156\137\141\x63\164\x69\x6f\x6e");
        P_:
        yY:
        goto fv;
        kk:
        $Cz = @file_get_contents($_FILES["\x6d\145\164\141\144\x61\x74\141\137\x66\151\x6c\x65"]["\x74\x6d\x70\137\x6e\141\155\x65"]);
        fv:
        $NI = set_error_handler(array($this, "\150\141\156\x64\x6c\145\x58\x6d\154\105\x72\162\x6f\x72"));
        $uz = new DOMDocument();
        $uz->loadXML($Cz);
        restore_error_handler();
        if (!empty($uz->firstChild)) {
            goto lF;
        }
        if (isset($_POST["\x75\x70\x6c\157\141\x64\x5f\155\x65\164\141\144\x61\164\x61"])) {
            goto o9;
        }
        update_site_option("\155\157\137\163\141\x6d\x6c\x5f\x6d\145\x73\163\141\x67\145", "\120\x6c\145\141\x73\145\x20\x70\162\x6f\x76\x69\144\145\40\141\40\x76\141\x6c\151\144\40\155\x65\164\141\144\141\164\x61\40\125\122\x4c\x2e");
        goto Z5;
        o9:
        update_site_option("\155\157\x5f\x73\x61\x6d\154\137\155\x65\163\163\141\x67\x65", "\x50\x6c\145\x61\163\145\x20\x70\x72\157\x76\151\x64\x65\40\x61\40\166\141\x6c\x69\144\x20\x6d\x65\164\x61\144\x61\164\x61\40\146\151\x6c\145\56");
        Z5:
        $this->mo_saml_show_error_message();
        goto cO;
        lF:
        $uw = new MetadataReader($uz);
        foreach ($uw->getIdentityProviders() as $Z1 => $d6) {
            $uh = $_POST["\163\141\x6d\x6c\x5f\x69\144\x65\156\x74\151\164\x79\x5f\160\x72\157\166\x69\144\x65\x72\x5f\156\x61\155\145"];
            $O9 = "\x48\x74\x74\160\x52\145\144\x69\162\x65\143\164";
            $Ce = '';
            if (array_key_exists("\x48\124\x54\120\55\x52\x65\144\x69\x72\145\x63\164", $d6->getLoginDetails())) {
                goto e6;
            }
            if (!array_key_exists("\x48\x54\124\x50\55\120\x4f\x53\124", $d6->getLoginDetails())) {
                goto nc;
            }
            $O9 = "\x48\x74\164\x70\120\x6f\163\x74";
            $Ce = $d6->getLoginURL("\x48\124\124\x50\x2d\120\117\x53\124");
            nc:
            goto RE;
            e6:
            $Ce = $d6->getLoginURL("\x48\x54\124\x50\55\122\x65\144\x69\x72\x65\143\x74");
            RE:
            $da = "\x48\164\x74\160\x52\145\x64\151\x72\145\x63\x74";
            $oP = '';
            if (array_key_exists("\x48\x54\124\x50\x2d\122\145\x64\151\162\145\143\164", $d6->getLogoutDetails())) {
                goto Io;
            }
            if (!array_key_exists("\110\x54\x54\x50\55\120\117\123\124", $d6->getLogoutDetails())) {
                goto mz;
            }
            $da = "\110\x74\x74\160\120\x6f\x73\164";
            $oP = $d6->getLogoutURL("\x48\x54\124\x50\x2d\x50\117\x53\124");
            mz:
            goto QO;
            Io:
            $oP = $d6->getLogoutURL("\x48\x54\124\x50\x2d\x52\145\x64\151\x72\x65\x63\164");
            QO:
            $qx = $d6->getEntityID();
            $y3 = $d6->getSigningCertificate();
            update_site_option("\x73\141\x6d\154\137\x69\x64\x65\156\164\x69\164\171\137\156\141\155\x65", $uh);
            update_site_option("\x73\141\x6d\154\137\x6c\157\x67\151\x6e\x5f\x62\x69\156\x64\x69\156\147\x5f\x74\171\160\x65", $O9);
            update_site_option("\x73\x61\x6d\x6c\x5f\154\157\147\x69\156\x5f\x75\162\x6c", $Ce);
            update_site_option("\163\141\155\x6c\x5f\x6c\x6f\147\157\x75\164\137\x62\151\x6e\x64\151\x6e\x67\x5f\164\171\x70\x65", $da);
            update_site_option("\163\141\155\x6c\137\x6c\157\147\157\165\164\x5f\165\162\154", $oP);
            update_site_option("\163\141\x6d\154\137\151\x73\163\x75\145\162", $qx);
            update_site_option("\163\141\x6d\x6c\x5f\156\x61\x6d\145\x69\x64\x5f\x66\157\x72\x6d\141\164", "\x31\56\x31\72\156\x61\x6d\145\x69\144\x2d\x66\x6f\162\155\x61\164\x3a\165\x6e\x73\160\x65\x63\x69\x66\151\x65\x64");
            $y3 = is_array($y3) ? $y3 : array(0 => $y3);
            $y3;
            foreach ($y3 as $Z1 => $zF) {
                $y3[$Z1] = Utilities::sanitize_certificate($zF);
                Y1:
            }
            aD:
            update_site_option("\163\141\x6d\154\137\170\x35\60\x39\x5f\x63\x65\162\x74\x69\146\151\x63\141\164\145", $y3);
            goto nx;
            KT:
        }
        nx:
        update_site_option("\155\157\137\163\141\155\x6c\x5f\x6d\145\x73\163\141\147\x65", "\x49\144\145\156\164\151\x74\171\40\x50\x72\157\166\x69\x64\x65\x72\40\x64\x65\164\x61\151\x6c\x73\40\x73\141\x76\145\144\40\163\x75\143\x63\145\x73\x73\146\x75\x6c\154\x79\x2e");
        $this->mo_saml_show_success_message();
        cO:
        q0:
    }
    function handleXmlError($sR, $tZ, $Ky, $GQ)
    {
        if ($sR == E_WARNING && substr_count($tZ, "\104\x4f\115\104\157\x63\165\155\145\x6e\164\72\x3a\154\157\141\x64\x58\115\x4c\50\51") > 0) {
            goto xo;
        }
        return false;
        goto rj;
        xo:
        return;
        rj:
    }
    function mo_saml_admin_page_access_denied()
    {
        $U0 = get_blogs_of_user(get_current_user_id());
        if (!wp_list_filter($U0, array("\165\x73\x65\162\142\x6c\x6f\x67\137\x69\144" => get_current_blog_id()))) {
            goto Hx;
        }
        return;
        Hx:
        $Hx = get_bloginfo("\156\x61\x6d\x65");
        if (!empty($U0)) {
            goto ep;
        }
        $Cu = "\x59\157\165\x20\x61\x74\164\145\155\160\x74\x65\144\40\164\157\40\141\x63\x63\145\163\163\x20\x74\x68\145\x20" . $Hx . "\40\x73\x69\164\145\x2c\x20\x62\165\x74\x20\171\157\165\40\144\x6f\x20\x6e\157\x74\x20\x63\165\162\x72\x65\x6e\164\x6c\171\x20\150\x61\166\145\x20\x70\x72\151\166\151\x6c\145\147\145\x73\x20\157\156\40\x74\150\x69\x73\x20\163\x69\x74\145\x2e\x20\111\x66\40\x79\157\165\40\142\145\154\x69\x65\166\145\x20\171\157\165\x20\x73\150\x6f\165\x6c\144\40\142\145\40\141\142\x6c\x65\40\164\x6f\40\141\143\143\x65\x73\163\40\164\x68\145\x20" . $Hx . "\40\x73\151\164\145\x2c\40\160\154\x65\141\x73\x65\x20\x63\x6f\x6e\164\141\x63\x74\x20\x79\157\x75\x72\x20\156\x65\x74\x77\x6f\162\153\x20\x61\x64\155\x69\x6e\151\x73\x74\x72\141\x74\x6f\162\x2e";
        mo_saml_subsite_access_denied_page($Cu);
        die;
        ep:
        $Cu = "\x3c\160\x3e\131\x6f\x75\40\x61\164\x74\x65\x6d\160\x74\x65\x64\x20\164\157\40\141\x63\x63\x65\163\163\40\164\150\145\x20" . $Hx . "\x20\x73\x69\164\145\54\40\142\165\x74\x20\171\x6f\165\40\x64\x6f\40\156\x6f\164\x20\x63\x75\162\x72\145\x6e\x74\x6c\x79\40\150\x61\x76\145\x20\x70\x72\151\166\151\x6c\145\x67\145\x73\x20\157\156\40\x74\150\x69\x73\x20\163\x69\164\x65\56\x20\111\146\40\171\x6f\x75\40\x62\x65\154\x69\145\166\x65\x20\171\157\x75\x20\x73\x68\157\x75\x6c\144\40\x62\x65\x20\x61\x62\154\x65\x20\164\157\40\x61\143\x63\145\x73\x73\x20\x74\150\x65\40" . $Hx . "\x20\x73\x69\x74\x65\x2c\x20\160\154\145\x61\x73\145\40\143\x6f\156\164\141\143\x74\40\x79\157\165\x72\40\x6e\145\x74\x77\157\162\x6b\40\141\144\x6d\151\156\x69\163\x74\x72\x61\x74\157\x72\56\x3c\x2f\x70\76";
        $Cu .= "\x3c\160\76\x49\x66\40\x79\157\165\x20\x72\145\x61\143\x68\x65\x64\40\164\x68\151\x73\x20\163\143\162\x65\x65\156\40\142\171\x20\x61\143\x63\151\x64\x65\x6e\164\x20\141\x6e\x64\40\155\x65\x61\x6e\164\x20\164\157\x20\x76\x69\163\151\x74\40\157\x6e\x65\x20\x6f\x66\x20\x79\x6f\x75\162\x20\x6f\167\x6e\40\x73\x69\x74\x65\163\x2c\40\150\x65\162\145\40\x61\x72\145\x20\163\x6f\x6d\145\x20\x73\x68\157\x72\x74\143\x75\164\163\40\164\x6f\x20\x68\x65\x6c\x70\x20\171\x6f\x75\x20\146\x69\156\144\x20\171\157\165\162\40\167\x61\171\x2e\x3c\57\x70\x3e";
        $Cu .= "\74\x68\63\76\131\157\x75\x72\40\123\x69\x74\x65\x73\74\57\x68\63\x3e";
        $Cu .= "\74\x74\141\142\154\x65\76";
        foreach ($U0 as $Pl) {
            $Cu .= "\x3c\x74\x72\76";
            $Cu .= "\x3c\x74\x64\76" . $Pl->blogname . "\x3c\57\164\144\76";
            $Cu .= "\74\x74\144\x3e\74\x61\40\x68\162\x65\146\75\x22" . esc_url(get_admin_url($Pl->userblog_id)) . "\42\76\126\151\163\151\x74\40\104\x61\x73\150\142\157\x61\162\144\74\57\x61\76\x20\x7c\40\x3c\x61\x20\150\x72\x65\146\75\42" . esc_url(get_home_url($Pl->userblog_id)) . "\42\x3e\x56\151\145\x77\40\123\x69\164\145\x3c\57\x61\x3e\74\57\x74\x64\x3e";
            $Cu .= "\74\57\x74\x72\x3e";
            Aq:
        }
        hR:
        $Cu .= "\74\57\x74\x61\x62\154\x65\x3e";
        mo_saml_subsite_access_denied_page($Cu);
        die;
    }
    function mo_saml_plugin_action_links($lS)
    {
        $lS = array_merge(array("\74\x61\x20\x68\x72\x65\146\75\42" . esc_url(network_admin_url("\141\x64\155\151\x6e\56\160\150\160\77\x70\141\147\x65\x3d\155\x6f\x5f\163\x61\155\154\137\163\145\164\164\151\156\147\163")) . "\42\76" . __("\123\145\164\x74\151\156\147\x73", "\x74\x65\x78\164\144\157\x6d\141\151\156") . "\74\x2f\x61\76"), $lS);
        return $lS;
    }
    function checkPasswordPattern($sG)
    {
        $jp = "\x2f\x5e\x5b\x28\x5c\x77\x29\x2a\50\x5c\41\134\100\x5c\43\x5c\x24\134\45\134\x5e\x5c\46\x5c\52\134\x2e\134\55\134\x5f\51\52\135\x2b\x24\57";
        return !preg_match($jp, $sG);
    }
}
new saml_mo_login();
