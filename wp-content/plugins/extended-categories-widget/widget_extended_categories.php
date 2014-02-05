<?php
/**
 * Plugin Name: AVH Extended Categories Widgets
 * Plugin URI: http://blog.avirtualhome.com/wordpress-plugins
 * Description: Replacement of the category widget to allow for greater customization of the category widget.
 * Version: 3.9.3
 * Author: Peter van der Does
 * Author URI: http://blog.avirtualhome.com/
 *
 * Copyright 2012-2013  Peter van der Does  (email : peter@avirtualhome.com)

 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
if (!defined('AVH_FRAMEWORK')) {
    define('AVH_FRAMEWORK', true);
}
require (ABSPATH . WPINC . '/version.php');
$_avhec_version = (float) $wp_version;

if ($_avhec_version >= 2.8) {
    $_avhec_abs_dir = dirname(__FILE__);

    require_once ($_avhec_abs_dir . '/libs/avh-registry.php');
    require_once ($_avhec_abs_dir . '/libs/avh-common.php');
    require_once ($_avhec_abs_dir . '/libs/avh-security.php');
    require_once ($_avhec_abs_dir . '/libs/avh-visitor.php');
    require_once ($_avhec_abs_dir . '/libs/avh-db.php');

    switch ($_avhec_version) {
        case ($_avhec_version >= 3.6):
            $_avhec_version_dir = '/3.6';
            break;
        case ($_avhec_version >= 3.3):
            $_avhec_version_dir = '/3.3';
            break;
        case ($_avhec_version >= 2.8):
            $_avhec_version_dir = '/2.8';
            break;
    }

    $_avhec_dir = basename($_avhec_abs_dir);
    $_avhec_url = plugins_url() . '/' . $_avhec_dir;

    define('AVHEC_PLUGIN_DIR', $_avhec_abs_dir);
    define('AVHEC_RELATIVE_PLUGIN_DIR', $_avhec_dir);
    define('AVHEC_PLUGIN_URL', $_avhec_url . $_avhec_version_dir);
    define('AVHEC_ABSOLUTE_WORKING_DIR', AVHEC_PLUGIN_DIR . $_avhec_version_dir);
    define('AVHEC_RELATIVE_WORKING_DIR', $_avhec_dir . $_avhec_version_dir);

    unset($_avhec_version);
    unset($_avhec_dir);
    unset($_avhec_abs_dir);
    unset($_avhec_version_dir);
    unset($_avhec_url);
    require (AVHEC_ABSOLUTE_WORKING_DIR . '/avh-ec.client.php');
} else {
    require_once 'widget-pre2.8.php';
}
