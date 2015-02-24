/**
 * @package LavaPlugin, Wordpress Plugin, ect.coop
 * @author Jameel Bokhari
 * @link http://codestag.com/how-to-use-wordpress-3-5-media-uploader-in-theme-options/
 * Source url for this code for reference, thanks Ram
**/
jQuery(document).ready(function($) {
    var radios = $('#pns_use_get_var-container input[type=radio]');
    var useGetBox = radios.filter('input[value="both"], input[value="getvar"]');

    if ( useGetBox.is(":checked") ){
        $("#pns_get_tracking_var-container").show();
    }
    radios.on("click", function(){
        if (useGetBox.is(":checked")){
            $("#pns_get_tracking_var-container").slideDown();
        }
        else{
            $("#pns_get_tracking_var-container").slideUp();
        }
    })
});