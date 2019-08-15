<?php

//#region Menu
    add_action("admin_menu", "amp_c_u_add_menu");
    function amp_c_u_add_menu() {
        $config = array(
            "page_title" => "AMP Önbellek Güncelleme",
            "menu_title" => "AMP Önbellek Güncelleme",
            "capability" => "manage_options",
            "menu_slug" => AMP_C_U_PLUGIN_FOLDER."/pages/index.php",
            "function" => "",
            "icon_url" => plugins_url(AMP_C_U_PLUGIN_FOLDER."/assets/img/icon.png" ),
            "position" => 65,

            "submenus" => array(
                array(
                    "page_title" => "Ayarlar - AMP Önbellek Güncelleme",
                    "menu_title" => "Ayarlar",
                    "capability" => "manage_options",
                    "menu_slug" => AMP_C_U_PLUGIN_FOLDER."/pages/settings.php",
                    "function" => "",
                )
            )
        );
        add_menu_page($config["page_title"], $config["menu_title"], $config["capability"], $config["menu_slug"], $config["function"], $config["icon_url"], $config["position"]);

        if(isset($config["submenus"]) && count($config["submenus"]) > 0) {
            foreach ($config["submenus"] as $key => $menu) {
                add_submenu_page($config["menu_slug"], $menu["page_title"], $menu["menu_title"], $menu["capability"], $menu["menu_slug"], $menu["function"]);
            }
        }
    }
//#endregion

//#region Settings
    add_action("admin_init", "amp_c_u_plugin_settings");
    function amp_c_u_plugin_settings() {
        register_setting(AMP_C_U_PLUGIN_FOLDER, "amp_c_u_private_pem");
        register_setting(AMP_C_U_PLUGIN_FOLDER, "amp_w_cdn_url");

        //CSS
        wp_enqueue_style(AMP_C_U_PLUGIN_FOLDER, plugins_url(AMP_C_U_PLUGIN_FOLDER."/assets/css/style.css" ));

        //JS
        wp_enqueue_script(AMP_C_U_PLUGIN_FOLDER, plugins_url(AMP_C_U_PLUGIN_FOLDER."/assets/js/metabox/index.js" ));
    }
//#endregion

//#region Meta Boxes
    if(!empty(get_option("amp_c_u_private_pem"))) {
        add_action('add_meta_boxes', 'amp_cu_page_detail_metabox');
    }
    function amp_cu_page_detail_metabox() {
        global $pagenow;

        if( 'post-new.php' == $pagenow )
            return;
        $screens = ['post', 'page'];
        foreach ($screens as $screen) {
            add_meta_box(
                "amp_cache_update_meta_box_".$screen, // Unique ID
                _("AMP Önbellek Güncelleme"), // Box title
                "amp_cache_update_meta_box", // Content callback, must be of type callable
                $screen // Post type
            );
        }
    }

    function amp_cache_update_meta_box() {
        include AMP_C_U_PLUGIN_PATH."/metabox/index.php" ;
    }
//#endregion

//#region Ajax
    add_action( 'wp_ajax_amp_c_u_update_cache', 'amp_c_u_update_cache' );
    function amp_c_u_update_cache() {
        $ret_val = array(
            "status" => 500,
            "message" => "Ajax error!",
            "data" => ""
        );
        if ($_POST) {
            $pemKey = get_option("amp_c_u_private_pem");
            $urls = explode(",", $_POST["url"]);

            if(!empty($pemKey) && count($urls) > 0) {
                foreach($urls as $i => $url) {
                    $urls[$i] = $url."amp/";
                }

                $domain = get_site_url();
                $cache = new Classes\AMPCacheUpdate(
                    $domain,
                    $urls,
                    $pemKey);
                $list = $cache->update();

                $ret_val = array(
                    "status" => 200,
                    "message" => "",
                    "data" => ""
                );

                foreach($list as $url) {
                    $ret_val["data"] = amp_c_u_update_cache_curl($url);
                }
            }
        }
        echo json_encode($ret_val);
        wp_die();
    }
//#endregion

//#region Filters
//add_filter( "wp_calculate_image_srcset", 'amp_c_u_image_srcset' );

function amp_c_u_image_srcset($sources) {
    foreach($sources as &$source)
    {
        $source['url'] = change_url($source['url']);
    }
    return $sources;
}

function change_url($url) {
    global $wp;
    $req_url = home_url( $wp->request );
    if(strrpos($req_url, "/amp/") == true) {
        if(!empty($amp_cdn_url)) {
            return str_replace(get_option("siteurl"), "", $url);
        }
    }
    return $url;
}
//#endregion