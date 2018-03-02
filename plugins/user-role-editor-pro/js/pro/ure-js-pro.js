/* 
 * User Role Editor WordPress plugin Pro
 * Author: Vladimir Garagulya
 * email: vladimir@shinephp.com
 * 
 */

jQuery(function() {
    jQuery("#ure_update_all_network").button({
        label: ure_data_pro.update_network
    }).click(function() {
        event.preventDefault();
        if (!confirm(ure_data_pro.update_network_warning)) {
            return false;
        }
        var apply_to_all = document.createElement("input");
        apply_to_all.setAttribute("type", "hidden");
        apply_to_all.setAttribute("id", "ure_apply_to_all");
        apply_to_all.setAttribute("name", "ure_apply_to_all");
        apply_to_all.setAttribute("value", '1');
        document.getElementById("ure_form").appendChild(apply_to_all);

        jQuery('#ure_form').submit();
    });
});




