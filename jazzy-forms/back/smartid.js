function jzzf_smartid(id_helper) {
    var $ = jQuery;

    var handle_source_change = function() {
        var smarttitle = $(this);
        var smartid = $('.jzzf_smartid');
        if(smartid.hasClass('jzzf_smartid_clean')) {
            var name = id_helper.suggest_name(smarttitle.val());
            smartid.val(name);
            smartid.trigger("jzzf_smartid_change");
        }
        return true;
    }

    var handle_id_change = function() {
        var elem = $(this);
        elem.toggleClass('jzzf_smartid_clean', elem.val() == '');
    }


    this.bind = function() {
        $('.jzzf_smartid').bind('change keyup', handle_id_change);
        $('.jzzf_smartid_source').bind("keyup change", handle_source_change);
    }

    this.init = function() {
        $('.jzzf_smartid').each(handle_id_change);
        $('.jzzf_smartid_source').each(handle_source_change);
    }
}


