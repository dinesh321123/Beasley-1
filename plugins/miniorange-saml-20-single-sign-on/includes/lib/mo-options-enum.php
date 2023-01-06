<?php


include "\x42\141\x73\x69\143\105\156\x75\155\56\x70\x68\x70";
class mo_options_enum_sso_login extends BasicEnum
{
    const SSO_Settings = "\x73\141\x6d\x6c\x5f\x73\163\x6f\x5f\163\145\x74\164\x69\x6e\x67\163";
    const Enable_SSO_For_sites = "\x6d\x6f\137\145\x6e\x61\142\154\x65\137\x73\x73\x6f\x5f\x73\x69\x74\145\163";
    const Relay_state = "\x6d\157\137\163\x61\x6d\154\137\162\x65\x6c\x61\171\x5f\163\164\x61\x74\145";
    const Redirect_Idp = "\x6d\157\x5f\x73\141\x6d\154\x5f\x72\145\x67\x69\x73\164\x65\162\145\144\137\x6f\x6e\x6c\171\137\x61\x63\x63\x65\163\x73";
    const Force_authentication = "\x6d\157\x5f\163\x61\155\x6c\137\146\x6f\162\x63\145\137\x61\x75\164\x68\145\x6e\164\x69\x63\x61\164\x69\x6f\156";
    const Enable_access_RSS = "\155\x6f\x5f\x73\141\x6d\x6c\137\x65\x6e\141\x62\x6c\x65\137\162\163\x73\137\x61\x63\x63\x65\x73\x73";
    const Auto_redirect = "\x6d\x6f\137\x73\x61\155\154\137\x65\x6e\141\142\154\x65\x5f\x6c\x6f\147\x69\x6e\x5f\162\145\x64\151\x72\145\143\x74";
    const Allow_wp_signin = "\x6d\157\137\x73\141\x6d\x6c\137\x61\154\x6c\x6f\x77\x5f\x77\160\137\x73\x69\147\x6e\151\x6e";
    const Custom_login_button = "\155\x6f\x5f\163\x61\x6d\x6c\x5f\x63\x75\x73\164\157\155\137\154\x6f\147\151\156\137\164\145\170\164";
    const Custom_greeting_text = "\x6d\157\137\163\141\155\x6c\137\x63\x75\x73\x74\x6f\155\137\147\162\145\145\164\151\x6e\x67\x5f\x74\x65\x78\x74";
    const Custom_greeting_name = "\155\157\137\163\141\x6d\x6c\x5f\x67\162\x65\145\x74\x69\x6e\x67\137\x6e\x61\x6d\x65";
    const Custom_logout_button = "\155\x6f\x5f\x73\141\x6d\154\x5f\x63\165\163\164\x6f\155\x5f\x6c\x6f\x67\157\x75\164\x5f\x74\145\170\x74";
    const Keep_Configuration_Intact = "\155\x6f\137\x73\x61\155\x6c\137\153\145\x65\x70\x5f\163\x65\164\x74\151\x6e\147\x73\x5f\x6f\x6e\x5f\144\x65\154\145\164\x69\x6f\x6e";
}
class mo_options_enum_identity_provider extends BasicEnum
{
    const Broker_service = "\155\x6f\x5f\163\x61\x6d\154\137\145\x6e\x61\142\x6c\145\x5f\x63\154\x6f\x75\x64\137\x62\x72\x6f\x6b\145\162";
    const SP_Base_Url = "\x6d\x6f\x5f\x73\141\x6d\x6c\137\163\x70\137\x62\141\x73\x65\x5f\x75\162\x6c";
    const SP_Entity_ID = "\155\x6f\137\x73\141\x6d\x6c\137\163\x70\137\145\156\164\x69\x74\171\x5f\151\144";
}
class mo_options_enum_custom_messages extends BasicEnum
{
    const Custom_Account_Creation_Disabled_message = "\155\157\x5f\163\141\x6d\x6c\x5f\141\143\143\157\x75\x6e\x74\x5f\143\162\x65\141\x74\x69\157\156\137\x64\151\x73\141\142\x6c\x65\x64\137\x6d\x73\147";
    const Custom_Restricted_Domain_message = "\x6d\x6f\x5f\163\141\x6d\x6c\x5f\162\x65\x73\x74\162\x69\143\x74\x65\x64\137\144\157\x6d\141\151\156\137\x65\162\162\157\x72\137\155\163\147";
}
class mo_options_enum_domain_restriction extends BasicEnum
{
    const Email_Domains = "\163\x61\155\x6c\137\141\x6d\137\x65\155\141\151\154\x5f\144\x6f\155\141\151\x6e\163";
    const Enable_Domain_Restriction_Login = "\155\157\137\x73\x61\x6d\x6c\137\145\156\x61\142\154\x65\x5f\144\157\155\141\x69\x6e\137\162\145\x73\x74\x72\x69\x63\x74\x69\x6f\x6e\x5f\154\x6f\147\151\x6e";
    const Allow_deny_user_with_Domain = "\x6d\x6f\137\x73\x61\155\154\137\141\154\x6c\157\x77\x5f\144\x65\156\x79\x5f\x75\x73\x65\x72\137\167\x69\x74\150\x5f\144\x6f\155\141\x69\x6e";
}
class mo_options_enum_service_provider extends BasicEnum
{
    const Identity_name = "\x73\x61\155\x6c\x5f\x69\144\145\156\164\151\x74\x79\137\156\141\x6d\x65";
    const Send_Signed_request = "\163\x61\155\154\137\162\x65\x71\x75\145\x73\164\137\x73\151\147\156\145\144";
    const Login_binding_type = "\x73\141\x6d\154\137\x6c\157\x67\151\x6e\137\x62\x69\x6e\x64\x69\x6e\147\137\x74\171\160\x65";
    const Login_URL = "\x73\141\155\154\137\x6c\157\147\151\156\x5f\x75\x72\154";
    const Logout_binding_type = "\163\141\x6d\x6c\137\154\x6f\x67\157\165\164\137\142\x69\x6e\x64\x69\156\x67\x5f\164\171\x70\145";
    const Logout_URL = "\x73\x61\x6d\154\x5f\x6c\157\147\x6f\165\x74\137\165\x72\x6c";
    const Issuer = "\163\x61\155\154\x5f\151\x73\x73\x75\145\162";
    const X509_certificate = "\x73\x61\x6d\x6c\x5f\x78\x35\60\71\x5f\143\145\x72\x74\x69\x66\151\143\x61\x74\145";
    const Request_signed = "\163\141\155\x6c\x5f\x72\x65\161\x75\145\163\164\x5f\x73\x69\147\x6e\145\144";
    const Name_ID_format = "\163\141\x6d\x6c\x5f\x6e\x61\155\x65\151\144\x5f\x66\157\162\155\x61\x74";
    const Guide_name = "\x73\x61\155\x6c\137\x69\x64\x65\156\x74\x69\164\171\137\x70\162\x6f\166\151\x64\145\162\137\147\165\x69\x64\x65\x5f\x6e\x61\x6d\145";
    const Character_encoding = "\155\157\137\x73\x61\155\x6c\x5f\145\x6e\x63\157\x64\151\156\147\137\x65\x6e\x61\x62\x6c\145\x64";
    const Sync_URL = "\x73\x61\155\154\137\x6d\145\x74\x61\144\x61\164\x61\x5f\x75\162\x6c\137\x66\x6f\x72\137\163\x79\x6e\143";
    const Sync_interval = "\163\141\155\154\137\x6d\x65\x74\141\144\x61\164\141\137\x73\171\x6e\x63\x5f\x69\156\164\145\x72\x76\141\154";
}
class mo_options_enum_attribute_mapping extends BasicEnum
{
    const Attribute_Username = "\163\x61\155\x6c\137\x61\155\x5f\x75\x73\145\162\156\141\155\145";
    const Attribute_Email = "\x73\x61\155\154\x5f\x61\x6d\x5f\145\155\141\151\x6c";
    const Attribute_First_name = "\x73\x61\x6d\154\137\x61\155\x5f\146\151\x72\163\x74\137\x6e\x61\155\x65";
    const Attribute_Last_name = "\163\141\x6d\154\x5f\141\155\x5f\154\x61\163\x74\137\156\141\155\145";
    const Attribute_Group_name = "\x73\x61\155\154\x5f\x61\155\x5f\147\x72\x6f\165\x70\137\156\141\x6d\145";
    const Attribute_Display_name = "\163\x61\x6d\154\x5f\141\155\137\144\x69\163\x70\154\141\171\137\156\141\x6d\x65";
    const Attribute_Custom_mapping = "\155\x6f\137\163\x61\155\154\137\143\165\x73\164\x6f\155\x5f\141\x74\164\x72\x73\137\x6d\141\160\160\151\x6e\x67";
    const Attribute_Account_matcher = "\x73\x61\155\154\x5f\x61\x6d\137\x61\x63\143\157\165\156\164\x5f\155\x61\164\x63\x68\145\x72";
}
class mo_options_enum_role_mapping extends BasicEnum
{
    const Role_mapping_site = "\163\x61\155\154\137\141\x6d\x5f\x72\157\154\x65\137\x6d\x61\160\x70\x69\156\147";
    const Super_Admin_role_mapping = "\155\x6f\x5f\x73\x61\x6d\154\x5f\163\x75\160\145\162\x5f\141\x64\x6d\151\x6e\x5f\x72\x6f\154\145\x5f\x6d\x61\x70\x70\151\156\147";
}
class mo_options_enum_test_configuration extends BasicEnum
{
    const SAML_REQUEST = "\155\157\137\163\141\155\154\137\162\145\x71\x75\x65\x73\164";
    const SAML_RESPONSE = "\x6d\157\137\163\141\155\x6c\x5f\162\x65\x73\160\x6f\156\163\x65";
    const TEST_CONFIG_ERROR_LOG = "\155\157\x5f\x73\x61\x6d\x6c\137\164\x65\163\x74";
    const TEST_CONFIG_ATTRS = "\x6d\x6f\x5f\163\x61\155\154\x5f\x74\145\x73\x74\x5f\x63\x6f\x6e\x66\x69\147\x5f\141\x74\164\x72\x73";
}
class mo_options_enum_custom_certificate extends BasicEnum
{
    const Custom_Public_Certificate = "\155\157\x5f\163\x61\x6d\x6c\137\x63\x75\163\164\x6f\x6d\137\143\145\x72\x74";
    const Custom_Private_Certificate = "\155\157\137\x73\141\x6d\x6c\x5f\143\x75\x73\x74\x6f\155\x5f\143\x65\x72\x74\137\x70\162\151\166\x61\164\145\x5f\153\x65\171";
}
class mo_options_error_constants extends BasicEnum
{
    const Error_no_certificate = "\125\x6e\141\x62\154\145\40\164\x6f\x20\x66\151\156\144\40\x61\x20\x63\x65\x72\x74\x69\x66\151\x63\x61\x74\145\40\56";
    const Cause_no_certificate = "\116\157\x20\x73\x69\147\156\141\164\x75\x72\145\x20\x66\157\x75\x6e\x64\x20\151\x6e\x20\x53\101\115\114\x20\x52\x65\x73\x70\157\x6e\x73\x65\x20\x6f\162\40\101\x73\163\145\x72\164\x69\x6f\156\56\40\120\x6c\x65\141\x73\145\40\163\151\147\x6e\x20\141\x74\40\x6c\x65\x61\163\164\x20\x6f\x6e\145\40\157\x66\40\164\150\x65\x6d\x2e";
    const Error_wrong_certificate = "\x55\x6e\141\142\x6c\145\x20\x74\157\40\146\151\156\x64\x20\141\40\143\145\162\164\x69\146\x69\x63\x61\x74\145\x20\155\141\x74\x63\x68\151\156\147\40\164\x68\x65\x20\143\x6f\x6e\x66\x69\147\x75\x72\x65\144\x20\146\x69\x6e\x67\x65\x72\160\162\151\156\164\56";
    const Cause_wrong_certificate = "\x58\56\x35\60\x39\40\x43\145\162\164\x69\146\x69\143\x61\x74\x65\40\x66\x69\145\154\144\x20\x69\156\x20\160\x6c\x75\x67\x69\156\x20\144\157\x65\163\x20\x6e\x6f\x74\40\155\141\x74\143\150\x20\x74\x68\145\x20\143\145\x72\164\x69\x66\151\143\141\x74\145\40\146\x6f\165\156\x64\x20\151\156\40\x53\x41\x4d\114\40\x52\x65\163\160\x6f\156\x73\x65\56";
    const Error_invalid_audience = "\111\x6e\166\141\154\151\x64\x20\101\165\144\x69\x65\156\143\x65\x20\125\x52\x49\x2e";
    const Cause_invalid_audience = "\x54\x68\x65\40\166\x61\154\x75\x65\40\x6f\146\40\47\x41\165\x64\x69\x65\x6e\x63\x65\x20\125\122\x49\47\x20\x66\x69\x65\x6c\144\40\157\x6e\x20\x49\144\145\x6e\164\151\x74\171\40\x50\162\157\x76\151\144\x65\162\x27\x73\40\x73\151\x64\x65\40\151\163\x20\x69\x6e\143\157\162\162\145\143\164";
    const Error_issuer_not_verfied = "\x49\x73\163\165\145\162\40\143\141\156\156\x6f\x74\x20\x62\x65\x20\x76\145\x72\x69\x66\x69\145\x64\56";
    const Cause_issuer_not_verfied = "\x49\x64\120\40\105\x6e\164\151\164\x79\40\111\104\x20\143\157\156\x66\151\147\165\162\x65\x64\40\141\x6e\144\x20\164\150\145\x20\x6f\x6e\x65\40\146\157\165\156\144\40\x69\156\40\x53\x41\x4d\x4c\40\122\145\163\160\x6f\156\163\x65\x20\x64\x6f\40\x6e\157\x74\x20\155\141\x74\x63\150";
}
class mo_options_enum_nameid_formats extends BasicEnum
{
    const EMAIL = "\x75\162\156\x3a\157\141\x73\x69\163\72\156\141\x6d\145\x73\x3a\164\143\x3a\123\x41\x4d\x4c\x3a\61\x2e\61\72\156\x61\x6d\x65\151\x64\x2d\146\157\162\x6d\141\x74\x3a\x65\155\x61\151\x6c\x41\x64\x64\x72\145\163\163";
    const UNSPECIFIED = "\x75\x72\x6e\72\157\141\x73\x69\x73\72\156\x61\155\x65\x73\72\164\143\x3a\x53\x41\x4d\114\x3a\x31\56\x31\x3a\x6e\141\x6d\x65\151\144\55\146\x6f\162\155\141\164\x3a\x75\x6e\163\x70\145\143\x69\146\151\145\144";
    const TRANSIENT = "\x75\162\x6e\x3a\157\141\163\151\163\72\x6e\141\155\x65\163\x3a\164\x63\72\123\101\115\x4c\x3a\62\x2e\x30\x3a\x6e\141\x6d\x65\x69\144\55\146\x6f\162\x6d\x61\x74\72\164\162\141\x6e\x73\151\145\x6e\x74";
    const PERSISTENT = "\165\162\156\72\157\x61\163\x69\x73\72\x6e\x61\155\145\163\72\164\x63\72\x53\101\x4d\114\72\62\x2e\60\72\156\x61\155\145\x69\x64\x2d\x66\157\x72\x6d\x61\x74\x3a\160\145\x72\x73\151\x73\x74\145\x6e\164";
}
class mo_options_plugin_constants extends BasicEnum
{
    const CMS_Name = "\x57\120";
    const Application_Name = "\127\x50\x20\155\151\x6e\x69\117\x72\141\156\x67\x65\40\123\101\115\x4c\x20\x32\56\x30\x20\123\x53\x4f\x20\120\154\x75\x67\x69\x6e";
    const Application_type = "\123\101\115\114";
    const Version = "\62\60\x2e\60\x2e\60";
    const HOSTNAME = "\x68\164\x74\160\x73\x3a\x2f\x2f\x6c\x6f\x67\151\x6e\x2e\170\x65\x63\x75\x72\151\x66\171\56\143\x6f\155";
    const LICENSE_TYPE = "\x57\x50\137\x53\101\115\114\137\123\120\137\115\125\x4c\x54\x49\123\111\124\x45\137\x50\x4c\x55\107\111\116";
    const LICENSE_PLAN_NAME = "\167\160\x5f\163\141\x6d\x6c\x5f\x73\x73\157\x5f\x6d\165\154\164\x69\x73\151\164\x65\x5f\142\141\x73\151\143\137\x70\x6c\x61\156";
}
class mo_options_plugin_idp extends BasicEnum
{
    public static $IDP_GUIDES = array("\x41\104\x46\x53" => "\141\144\146\163", "\x4f\153\164\141" => "\x6f\x6b\x74\x61", "\123\x61\154\145\163\106\157\162\143\145" => "\x73\141\x6c\x65\x73\x66\157\162\143\145", "\107\157\x6f\147\x6c\145\x20\x41\160\x70\x73" => "\147\157\x6f\147\154\x65\x2d\141\160\x70\x73", "\x41\172\x75\162\145\x20\101\104" => "\x61\x7a\x75\162\x65\x2d\141\144", "\x4f\156\x65\114\157\147\151\156" => "\157\156\145\154\x6f\x67\x69\156", "\113\145\171\x63\154\157\141\153" => "\152\142\x6f\x73\x73\x2d\153\x65\171\x63\154\x6f\x61\x6b", "\x4d\x69\x6e\151\x4f\162\x61\x6e\x67\145" => "\x6d\x69\156\x69\157\x72\141\x6e\147\x65", "\x50\x69\156\x67\106\x65\144\x65\162\141\164\145" => "\160\151\156\x67\146\x65\x64\145\x72\141\164\x65", "\120\151\x6e\147\x4f\156\x65" => "\x70\151\156\147\157\156\x65", "\103\145\x6e\164\162\x69\x66\x79" => "\143\145\156\164\162\x69\x66\171", "\x4f\162\x61\143\154\x65" => "\157\x72\x61\x63\154\x65\55\x65\x6e\164\145\x72\160\162\151\x73\x65\55\x6d\x61\156\x61\147\145\x72", "\102\x69\164\x69\165\155" => "\x62\x69\164\x69\165\155", "\123\x68\151\x62\142\x6f\154\x65\164\150\x20\x32" => "\x73\x68\151\142\142\157\154\x65\x74\150\x32", "\x53\150\151\x62\x62\157\x6c\x65\164\150\x20\x33" => "\x73\x68\x69\142\x62\157\154\145\164\150\x33", "\123\151\x6d\x70\x6c\145\x53\101\x4d\x4c\x70\x68\160" => "\x73\x69\155\x70\x6c\145\x73\141\155\x6c", "\x4f\160\145\x6e\x41\115" => "\x6f\160\145\156\x61\155", "\x41\165\164\150\141\x6e\166\151\x6c" => "\141\165\x74\x68\141\x6e\166\x69\154", "\x41\x75\164\x68\x30" => "\141\165\164\x68\x30", "\103\x41\40\x49\x64\x65\156\x74\x69\164\171" => "\x63\141\x2d\151\x64\145\156\x74\151\x74\171", "\x57\x53\x4f\62" => "\167\x73\157\x32", "\x52\123\x41\40\x53\145\x63\x75\x72\x65\x49\x44" => "\x72\163\141\55\x73\x65\x63\x75\x72\x65\151\144", "\x4f\x74\150\145\x72" => "\x4f\x74\x68\145\x72");
}
