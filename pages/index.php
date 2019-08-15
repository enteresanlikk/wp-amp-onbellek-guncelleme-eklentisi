<?php
    $pemKey = get_option("amp_c_u_private_pem");
    //$cdn_url = get_option("amp_w_cdn_url");
?>

<div class="amp-cache-update">
    <h1>AMP Cache Update</h1>
    <?php if (!empty($pemKey)) { ?>
        <div class="amp-row">
            <label class="title" for="private_pem"><b>Private key</b> bilginiz</label>
            <textarea id="private_pem" class="textarea" disabled><?= $pemKey ?></textarea>
        </div>
    <?php } else { ?>
        <div class="amp-row">
            <b>Private key</b> bulunamadı. Ayarlar kısmından ekleyebilirsiniz.
        </div>
    <?php } ?>

    <?php

    /*

    <?php if (!empty($cdn_url)) { ?>
        <div class="amp-row">
            <label class="title" for="cdn_url">AMP için CDN alan adınız</label>
            <input id="cdn_url" class="input" value="<?= $cdn_url ?>" disabled />
        </div>
    <?php } else { ?>
        <div class="amp-row">
            AMP için CDN alan adı bulunamadı. Ayarlar kısmından ekleyebilirsiniz.
        </div>
    <?php } ?>

     */

    ?>
</div>