var DOM = {
  hide: "hide",
  success: "status-success",
  error: "status-error"
};
jQuery(document).ready( function($) {
    var message_box = $("#message");
    var links_box = $("#amp_c_u_links");

    $("[name='clear_cache']").on("click", function () {
        var this_el = $(this);
        var old_btn_text = this_el.text();
        var loading_text = this_el.attr("data-loading-text");

        message_box.html("");
        links_box.html("");

        this_el.text(loading_text);
        this_el.attr("disabled", "disabled");

        $.ajax({
            url: ajaxurl,
            type: "POST",
            data: {
                action: "amp_c_u_update_cache",
                url: $("[name='url']").val()
            },
            dataType: "json",
            success: function (data) {
                if(data.status == 200) {
                    if(data.data.status == 200) {
                        show_message("success", data.data.message);
                    } else {
                        show_message("error", data.data.message);

                        //$("#amp_c_u_links").html("<li><a href='"+data.data.info.url+"' target='_blank'>AMP Cache Update Link</a></li>");
                    }
                } else {
                    show_message("error", data.message);
                }
                this_el.text(old_btn_text);
                this_el.removeAttr("disabled");
            },
            error: function (err) {
                show_message("error", err.responseText);
                this_el.text(old_btn_text);
                this_el.removeAttr("disabled");
            }
        });
    });

    function show_message(type, message) {
        message_box.html("");
        var html = "<div class='notice notice-"+type+" is-dismissible'><p>"+message+"</p></div>";
        message_box.html(html);
    }
});