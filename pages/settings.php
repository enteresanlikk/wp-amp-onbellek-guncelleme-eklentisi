<div class="amp-cache-update">
    <h1>Ayarlar</h1>

    <?php
    if($_POST["action"] == "update") {
        if(!isset($_POST[PLUGIN_FOLDER]) || !wp_verify_nonce($_POST[PLUGIN_FOLDER], PLUGIN_FOLDER)) {
            show_custom_message("Ayarlar kaydedilirken hata oluştu!", "error");
        } else {
            $pem = $_POST["amp_c_u_private_pem"];
            update_option("amp_c_u_private_pem", $pem);

            /*$cdn_url = $_POST["amp_w_cdn_url"];
            update_option("amp_w_cdn_url", $cdn_url);*/

            amp_cu_show_message("Ayarlar kaydedildi.");
        }
    }
    ?>

    <form action="" method="post">
        <?php
            wp_nonce_field(PLUGIN_FOLDER, PLUGIN_FOLDER);
        ?>
        <div class="amp-row">
            <label class="title" for="amp_c_u_private_pem"><b>Private key</b> giriniz</label>
            <textarea name="amp_c_u_private_pem" class="textarea" id="amp_c_u_private_pem"><?= get_option("amp_c_u_private_pem") ?></textarea>
        </div>

        <?php
        /*<div class="amp-row">
            <label class="title" for="amp_w_cdn_url">AMP için CDN alan adınız</label>
            <input name="amp_w_cdn_url" class="input" id="amp_w_cdn_url" value="<?= get_option("amp_w_cdn_url") ?>" />
        </div>*/
        ?>

        <input type="hidden" name="action" value="update">

        <?php submit_button() ?>
    </form>
</div>