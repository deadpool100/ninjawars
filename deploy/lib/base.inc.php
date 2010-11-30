<?php
/**
 * The starting include file for all of NW.
 *
 * @package lib
 * @subpackage base
**/

///TODO - require_once is more expensive than require, and if we're smart about it, we could convert all of these to requires. Let's do that ***

// Cut down on the global includes, use specific includes instead.

require_once(substr(__FILE__, 0, (strpos(__FILE__, 'lib/'))).'resources.php');

if (PROFILE) {
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$__starttime = $mtime;
}

require_once(LIB_ROOT.'environment/global_error_handling.php');
// *** Included first from the index, so has to be on the same level as index.
// Standalone utilities
require_once(LIB_ROOT.'control/assignment_functions.php');
require_once(LIB_ROOT.'control/redirect.php');
require_once(LIB_ROOT.'data/DatabaseConnection.php');
//require_once(OBJ_ROOT.'Sanitize.php');
require_once(LIB_ROOT.'control/Nmail.class.php');
require_once(LIB_ROOT.'control/Cookie.class.php');
require_once(LIB_ROOT.'control/Session.class.php');
require_once(LIB_ROOT.'control/Clan.php');

// *** Include all common function includes here.
require_once(LIB_ROOT.'control/lib_input.php');
require_once(LIB_ROOT.'control/lib_output.php');
require_once(LIB_ROOT.'data/lib_db.php');
require_once(LIB_ROOT.'control/lib_templates.php');

// Development includes
if (DEBUG) {
	require_once(LIB_ROOT.'control/lib_dev.php');
}

// Includes that actually actively modify settings.
require_once(LIB_ROOT.'environment/lib_assert.php');
require_once(LIB_ROOT.'environment/status_defines.php'); // Status constant definitions.
require_once(LIB_ROOT.'environment/lib_error_reporting.php');
require_once(LIB_ROOT.'environment/lib_formulas.php');

// Include all the commands, which eventually should be broken up.
require_once(LIB_ROOT.'control/commands.php');
require_once(LIB_ROOT.'control/lib_message.php');
require_once(LIB_ROOT.'control/lib_events.php');
require_once(LIB_ROOT.'control/lib_auth.php'); // Authentication and activity.
require_once(LIB_ROOT.'control/lib_settings.php'); // The player settings system.
require_once(LIB_ROOT.'control/lib_clan.php'); // Clan functionality.

// Game objects
require_once(LIB_ROOT . 'data/PlayerVO.class.php');
require_once(LIB_ROOT . 'data/PlayerDAO.class.php');
require_once(LIB_ROOT . 'control/Player.class.php');
require_once(LIB_ROOT . 'control/lib_status.php');
require_once(LIB_ROOT . 'control/AttackLegal.class.php');
require_once(LIB_ROOT.'control/lib_attack.php');

// Include the functions abstracted out of the header and footer
require_once(LIB_ROOT.'control/lib_header.php');
?>
