<?php
/**
 * Plugin Name: AMP Önbellek Güncelleme
 * Plugin URI: https://github.com/enteresanlikk/wp-amp-onbellek-guncelleme-eklentisi
 * Description: Wordpress için geliştirilmiş Google AMP Önbellek Güncelleme eklentisi.
 * Version: 0.0.1
 * Author: Bilal Demir
 * Author URI: https://github.com/enteresanlikk
 */

defined( 'ABSPATH' ) or die( 'Script error!' );

include __DIR__."/lib/vars.php";
include __DIR__."/lib/functions.php";
include __DIR__."/lib/system.php";

include __DIR__ . "/classes/AMPCacheUpdate.php";