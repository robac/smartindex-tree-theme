/*
 * SmartIndex dtree theme event handlers
 */

function si_dtree_handleSubTreeLoad(event, data) {
    var $div = jQuery(event.currentTarget);
    var $li = $div.parent();
    $li.append(data);

    var parentLis = $div.children("a").parents("li.namespace");
    var s = "";
    var cls = "";
    for (var i = parentLis.length; i > 0; i--) {
        if (jQuery(parentLis.get(i-1)).next("li").length > 0) {
            cls = "line";
        } else {
            cls = "noline";
        }
        s += "<span class=\""+cls+"\"></span>"
    }

    var ajaxLis = $div.next("ul").find("li");
    jQuery.each(ajaxLis, function(k, v){
        jQuery(v).children("div").prepend(s);
    });

    $li.removeClass("waiting");
}