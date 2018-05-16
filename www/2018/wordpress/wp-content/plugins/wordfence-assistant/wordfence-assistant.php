<?php
/*
Plugin Name: Wordfence Assistant
Plugin URI: http://www.wordfence.com/
Description: Wordfence Assistant - Helps Wordfence users with miscellaneous Wordfence data management tasks.  
Author: Mark Maunder
Version: 1.0.5
Author URI: http://www.wordfence.com/
*/

define('WORDFENCE_ASSISTANT_FCPATH', __FILE__);

define('WORDFENCE_ASSISTANT_VERSION', '1.0.5');

require_once('lib/wordfenceAssistantClass.php');
register_activation_hook(WP_PLUGIN_DIR . '/wordfence-assistant/wordfence-assistant.php', 'wordfenceAssistant::installPlugin');
register_deactivation_hook(WP_PLUGIN_DIR . '/wordfence-assistant/wordfence-assistant.php', 'wordfenceAssistant::uninstallPlugin');
wordfenceAssistant::install_actions();

?>
