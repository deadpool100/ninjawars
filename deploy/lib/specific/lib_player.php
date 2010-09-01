<?php
require_once(LIB_ROOT."specific/lib_status.php");
require_once(LIB_ROOT."common/lib_accounts.php");
// lib_player.php

// Defines for avatar options.
define('GRAVATAR', 1);

/***********************   Refactor these class functions from commands.php  ********************************/


// ************************************
// ********** CLASS FUNCTIONS *********
// ************************************

// Wrapper functions for the old usages.
// DEPRECATED
function setClass($who, $new_class){
    $char_id = get_char_id($who);
    return set_class($char_id, $new_class);
}

// Wrapper functions for the old usages.
// DEPRECATED
function getClass($who){
    $char_id = get_char_id($who);
    return char_class_identity($char_id);
    // Note that classes now have identity/name(for display)/theme, so this function should be deprecated.
}


    // ************************************
    // ************************************	


// Centralized holding for the maximum level available in the game.
function maximum_level(){
    return 250;
}

// The number of kills needed to level up to the next level.
function required_kills_to_level($current_level){
    $levelling_cost_multiplier = 5; // 5 more kills in cost for every level you go up.
    $required_kills = ($current_level+1)*$levelling_cost_multiplier;
    return $required_kills;
    
}


// ******** Leveling up Function *************************
// Incorporate this into the kill system to cause auto-levelling.
function level_up_if_possible($char_id){
    // Setup values: 
	$max_level = maximum_level();
    $health_to_add = 100;
    $turns_to_give = 50;
    $strength_to_add = 5;
    
    
        
    $username = get_char_name($char_id);
    $char_level = getLevel($username);
    $char_kills = getKills($username);


    // Check required values:
	$nextLevel  = $char_level + 1;
	$required_kills = required_kills_to_level($char_level);
	// Have to be under the max level and have enough kills.
	$level_up_possible = ( 
	    ($nextLevel < $max_level) && 
	    ($char_kills >= $required_kills) );
	    
	    
	if ($level_up_possible) {
	    // ****** Perform the level up actions ****** //
		$userKills = subtractKills($username, $required_kills);
		$userLevel = addLevel($username, 1);
		addStrength($username, $strength_to_add);
		addHealth($username, $health_to_add);
		addTurns($username, $turns_to_give);
		return true;
	} else {
	    return false;
	}
}



// Check that a class matches against the class identities available in the database.
function is_valid_class($potential_class_identity){
    $sel = "select identity from class";
    $classes = query_array($sel);
    foreach($classes as $l_class){
        if($l_class['identity'] == $potential_class_identity){
            return true;
        }
    }
    return false;
}

// Set the character's class.
function set_class($char_id, $new_class) {
    if(!is_valid_class($new_class)){
        return "That class was not an option to change into.";
    } else {
    	$up = "UPDATE players SET _class_id = (select class_id FROM class WHERE class.identity = :class) WHERE player_id = :char_id";
    	query($up, array(':class'=>$new_class, ':char_id'=>$char_id));

    	return null;
    }
}


// Get the character class information.
function char_class_identity($char_id) {
    return query_item("SELECT class.identity FROM players JOIN class ON class_id = _class_id WHERE player_id = :char_id", 
        array(':char_id'=>$char_id));
}


// Get the character class theme string.
function char_class_theme($char_id) {
    return query_item("SELECT class.theme FROM players JOIN class ON class_id = _class_id WHERE player_id = :char_id", 
        array(':char_id'=>$char_id));
}

/**
 * Pull out the url for the player's avatar
**/
function create_avatar_url($player, $size=null) {
	// If the avatar_type is 0, return '';
    if (!$player->vo || !$player->vo->avatar_type || !$player->vo->email) {
        return '';
    } else {	// Otherwise, user the player info for creating a gravatar.
		$email       = $player->vo->email;
		$avatar_type = $player->vo->avatar_type;
		return create_gravatar_url_from_email($email, $avatar_type, $size);
	}
}

function generate_gravatar_url($player) {
	if (!is_object($player)) {
		$player = new Player($player);
	}

	return (OFFLINE ? IMAGE_ROOT.'default_avatar.png' : create_avatar_url($player));
}

// Use the email information to return the gravatar image url.
function create_gravatar_url_from_email($email, $avatar_type=null, $size=null){
	$def         = 'monsterid'; // Default image or image class.
	// other options: wavatar (polygonal creature) , monsterid, identicon (random shape)
	$base        = "http://www.gravatar.com/avatar/";
	$hash        = md5(trim(strtolower($email)));
	$no_gravatar = "d=".urlencode($def);
	$size        = whichever($size, 80);
	$rating      = "r=x";
	$res         = $base.$hash."?".implode('&', array($no_gravatar, $size, $rating));

	return $res;    
}

// *** Get list of clan members from clan ***
/// TODO - Should be moved to clan stuff
function get_clan_members($p_clanID, $p_limit = 30) {
	if ((int)$p_clanID == $p_clanID && $p_clanID > 0) {
		$sel = "SELECT uname, player_id, health FROM clan_player JOIN players ON player_id = _player_id AND _clan_id = :clanID AND confirmed = 1 ORDER BY health DESC, level DESC ".(!is_null($p_limit) && $p_limit > 0 ? "LIMIT :limit" : '');
		DatabaseConnection::getInstance();
		$statement = DatabaseConnection::$pdo->prepare($sel);
		$statement->bindValue(':clanID', $p_clanID);

		if (!is_null($p_limit) && $p_limit > 0) {
			$statement->bindValue(':limit', $p_limit);
		}

		$statement->execute();

		return $statement;
	} else {
		return null;
	}
}

/**
 * Create the item options for the inventory dropdown.
**/
function render_inventory_options($username) {
	$char_id = get_char_id($username);
	
	if(!$char_id){
	    return '';
	}
	
	$res = '';
	$selected = "selected='selected'";// Mark first option as selected.

    
    $sel = "SELECT owner, item_internal_name, item_display_name, item.item_id, amount
        FROM inventory join item on inventory.item_type = item.item_id 
        WHERE owner = :owner_id
        AND amount > 0 ORDER BY item_display_name";
    $loop_items = query($sel, array(':owner_id'=>array((int)$char_id, PDO::PARAM_INT)));
    
    if(!$loop_items->rowCount()){
		$res = "          <option value='' selected='selected'>You Have No Items</option>";
    } else {
    
        $items_indexed = array();
        foreach($loop_items as $litem){
            // Index by internal name.
            $items_indexed[$litem['item_internal_name']] = $litem;
        }    
    
        // Custom multidimensional inventory array sorting function.
        function sort_alpha_with_shuriken_first($item, $item_2){
            if($item['item_internal_name'] == 'shuriken'){
                return -1;
            }
            if($item_2['item_internal_name'] == 'shuriken'){
                return 1;
            }
            if ($item['item_internal_name'] == $item_2['item_internal_name']) {
                return 0;
            }
            return ($item < $item_2) ? -1 : 1;
        }

        usort($items_indexed, "sort_alpha_with_shuriken_first");
        
        foreach($items_indexed as $sorted_item){
    			$res .= "      <option $selected value='{$sorted_item['item_id']}'>"
    			    .htmlentities($sorted_item['item_display_name']).
    			    " ({$sorted_item['amount']})</option>";
    			$selected = '';// Further items will not be marked as selected.
        }
    }
	return $res;
}

/**
 * Display the full form for item use/dropdowns/give/
**/
function render_item_use_on_another($target) {
	$username = get_username();
	$res = "<form id=\"inventory_form\" action=\"inventory_mod.php\" method=\"post\" name=\"inventory_form\">\n
    <input id=\"target\" type=\"hidden\" name=\"target\" value=\"$target\">
    <input type=\"submit\" value=\"Use\" class=\"formButton\">\n
    <select id=\"item\" name=\"item_type\">\n";

	$res .= render_inventory_options($username);
	$res .= "</select>";

	$target_id   = get_user_id($target);
	$target_clan = get_clan_by_player_id($target_id);

	if ($target_clan && ($user_clan = get_clan_by_player_id(get_user_id($username))) && $target_clan->getID() == $user_clan->getID()) {
		// Only allow giving items within the same clan.
		$res .= "<input id=\"give\" type=\"submit\" value=\"Give\" name=\"give\" class=\"formButton\">\n";
	}

	$res .= "</form>\n";
	return $res;
}

// Check whether the player is the leader of their clan.
function is_clan_leader($player_id) {
	return (($clan = get_clan_by_player_id($player_id)) && $player_id == get_clan_leader_id($clan->getID()));
}

function get_rank($username) {
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("SELECT rank_id FROM rankings WHERE uname = :player");
	$statement->bindValue(':player', $username);
	$statement->execute();

	$rank = $statement->fetchColumn();

	return ($rank > 0 ? $rank : 1); // Make rank default to 1 if no valid ones are found.
}
?>
