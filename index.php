<?php
/**
 * Plugin Name: Digital Ocean Spaces Sync
 * Plugin URI: https://github.com/real-coder-pty-ltd/do-spaces-sync
 * Description: Updated version of the DO Spaces Sync WordPress plugin.
 * Version: 1.0.0
 * Author: MattNeal
 * Author URI: https://github.com/matt-nealstafflink
 * License: MIT
 * Text Domain: dos
 * Domain Path: /languages
 */

define('DOS_PLUGIN_FOLDER', __DIR__);
define('DOS_PLUGIN_FOLDER_RELATIVE_PATH', dirname(plugin_basename(__FILE__)));
define('DOS_PLUGIN_URL', plugins_url('', __FILE__));

$plugin = new \frappacchio\DOSWordpress\DOSWordpressPlugin();
