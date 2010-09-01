<?php
require_once(SERVER_ROOT."lib/specific/lib_player.php");


// ************************************
// ********** CLAN FUNCTIONS **********
// ************************************

// ***** The below three functions are from commands.php, refactoring recommended ***

function createClan($p_leaderID, $p_clanName) {
	DatabaseConnection::getInstance();

	$p_clanName = trim($p_clanName);

	$result = DatabaseConnection::$pdo->query("SELECT nextval('clan_clan_id_seq')");
	$newClanID = $result->fetchColumn();

	$statement = DatabaseConnection::$pdo->prepare("INSERT INTO clan (clan_id, clan_name, clan_founder) VALUES (:clanID, :clanName, :leader)");
	$statement->bindValue(':clanID', $newClanID);
	$statement->bindValue(':clanName', $p_clanName);
	$statement->bindValue(':leader', get_username($p_leaderID));
	$statement->execute();

	$statement = DatabaseConnection::$pdo->prepare("INSERT INTO clan_player (_player_id, _clan_id, member_level) VALUES (:leader, :clanID, 2)");
	$statement->bindValue(':clanID', $newClanID);
	$statement->bindValue(':leader', $p_leaderID);
	$statement->execute();

	return new Clan($newClanID, $p_clanName);
}

// Gets the clan object from player_id->clan_id->clan_info->clan_object
function get_clan_by_player_id($p_playerID) {
    $clan_id = clan_id((int) $p_playerID);
    $clan_info = get_clan($clan_id);
	if (!empty($clan_info)) {
		$clan = new Clan($clan_info['clan_id'], $clan_info['clan_name']);
		return $clan;
	} else {
		return null;
	}
}

// ************************************
// ************************************



// Does not check for validity, simply renames the clan to the new name.
function rename_clan($p_clanID, $p_newName) {
	DatabaseConnection::getInstance();

	$statement = DatabaseConnection::$pdo->prepare("UPDATE clan SET clan_name = :name WHERE clan_id = :clan");
	$statement->bindValue(':name', $p_newName);
	$statement->bindValue(':clan', $p_clanID);
	$statement->execute();

	return $p_newName;
}

// Without checking for pre-existing clan and other errors, adds a player into a clan.
function add_player_to_clan($player_id, $clan_id, $member_level=0){
    $member_level = (int) $member_level;
	DatabaseConnection::getInstance();
	$statement = DatabaseConnection::$pdo->prepare("INSERT INTO clan_player (_clan_id, _player_id, member_level) VALUES (:clan, :player_id, :member_level)");
	$statement->bindValue(':clan', $clan_id);
	$statement->bindValue(':player_id', $player_id);
	$statement->bindParam(':member_level', $member_level, PDO::PARAM_INT);
	$statement->execute();
	// Add the player into the clan.

    // Because the confirmation number is used for inviting, change the confirmation number.
    $random      = rand(1001, 9990); // Semi-random confirmation number, and change the players confirmation number.
	$statement = DatabaseConnection::$pdo->prepare("UPDATE players SET confirm = :confirm WHERE player_id = :player_id");
	$statement->bindValue(':confirm', $random);
	$statement->bindValue(':player_id', $player_id);
	$statement->execute();
}

// TODO: Simplify this invite system.

// Send a message and change the status of a player so that they are in an "invited" state.
function inviteChar($char_id, $p_clanID) {
    $failure_reason = null;
	DatabaseConnection::getInstance();
	$target_id = $char_id;
	$target    = new Player($target_id);
	if (!$target_id) {
		return $failure_reason = 'No such ninja.';
	}
	$statement = DatabaseConnection::$pdo->prepare(
	    'SELECT confirmed, _clan_id FROM players LEFT JOIN clan_player ON player_id = _player_id WHERE player_id = :target');
	$statement->bindValue(':target', $target_id);
	$statement->execute();
	$data = $statement->fetch();

	$current_clan        = $data['_clan_id'];
	$player_is_confirmed = $data['confirmed'];

    $leader_info = get_clan_leader_info($p_clanID);
    $clan_name   = $leader_info['clan_name'];
    $clan_id     = $leader_info['clan_id'];
    $leader_id   = $leader_info['player_id'];
    $leader_name = $leader_info['uname'];

	if (!$player_is_confirmed) {
		$failure_error = 'That player name does not exist.';
	} else if (!empty($current_clan)) {
		$failure_error = 'That player is already in a clan.';
	} else if ($target->hasStatus(INVITED)) {
		$failure_error = 'That player has already been Invited into a Clan.';
	} else {
		$invite_msg = "$leader_name has invited you into their clan.
		To accept, choose their clan $clan_name on the "
		.message_url('clan.php?command=join&clan_id='.$p_clanID, 'clan joining page').".";
		send_message($leader_id, $target_id, $invite_msg);
		$target->addStatus(INVITED);
		$failure_error = null;
	}

	return $failure_error;
}

function send_clan_join_request($username, $clan_id) {
	DatabaseConnection::getInstance();
	$clan        = get_clan($clan_id);
	$leader      = get_clan_leader_info($clan_id);
	$leader_id   = $leader['player_id'];
	$leader_name = $leader['uname'];

	$confirmStatement = DatabaseConnection::$pdo->prepare("SELECT confirm FROM players WHERE uname = :user");
	$confirmStatement->bindValue(':user', $username);
	$confirmStatement->execute();
	$confirm = $confirmStatement->fetchColumn();

	// These ampersands get encoded later.
	$url = message_url("clan_confirm.php?clan_joiner=".get_user_id($username)."&agree=1&confirm=$confirm&clan_id=".urlencode($clan_id), 'Confirm Request');

	$join_request_message = "CLAN JOIN REQUEST: ".htmlentities($username)." has sent a request to join your clan.
		If you wish to allow this ninja into your clan click the following link:
		$url";
	send_message(get_user_id($username), $leader_id, $join_request_message);
}

function render_joinable_clans($clan_id) {
	//Clan Join list of available Clans
	$leaders = get_clan_leaders(($clan_id ? $clan_id : null), ($clan_id ? false : true));

	$res = "";
	foreach ($leaders as $leader) {
		$info = get_player_info($leader['player_id']);
		$res .= "<li><a target='main' class='clan-join' href=\"clan.php?command=join&amp;clan_id={$leader['clan_id']}&amp;process=1\">
				Join ".htmlentities($leader['clan_name'])."</a>.
					Its leader is <a href=\"player.php?player_id=".rawurlencode($leader['player_id'])."\">
				".htmlentities($leader['uname'])."</a>, level {$info['level']}.
				<a target='main' href=\"clan.php?command=view&amp;clan_id={$leader['clan_id']}\">View This Clan</a>
			</li>\n";
		}
	return $res;
}

// Gets the clan_id of a character/player.
function clan_id($char_id=null){
    $info = get_player_info($char_id);
    return $info['clan_id'];
}

/*
 * Clan name requirements:
 * Must be at least 3 characters to a max of 24, can only contain:
 * letters, numbers, spaces, underscores, or dashes.
 */
function is_valid_clan_name($potential) {
	$potential = (string)$potential;
	return preg_match("#^[\da-z_\-][\da-z_\-]{1,24}[\da-z_\-]$#i", $potential);
}


// Wrapper for getting a single clan's info.
function get_clan($clan_id) {
    return clan_info($clan_id);
}

// Better name for the function to get the array of clan info.
function clan_info($clan_id){
    return query_row(
        "SELECT clan_id, clan_name, clan_created_date, clan_founder, clan_avatar_url, description FROM clan WHERE clan_id = :clan",
        array(':clan'=>array($clan_id, PDO::PARAM_INT))
        );
}

// Gets the clan founder, though they may be dead and unconfirmed now.
function get_clan_founders() {
	DatabaseConnection::getInstance();
	$founders_statement = DatabaseConnection::$pdo->query(
	    "SELECT clan_founder, clan_name, uname, player_id, confirmed
		FROM clan LEFT JOIN players ON lower(clan_founder) = lower(uname)");
	return $founder_statement;
}

// Return only the single clan leader and their information.
function get_clan_leader_info($clan_id) {
	$clans = get_clan_leaders($clan_id, false);

	return $clans->fetch();
}

// Checks that a char is the leader of a clan, and optionally that that character is leader of a specific clan.
// @return array clan_info_array
// @return null if not leader of anything.
function clan_char_is_leader_of($char_id, $clan_id=null) {
    $sel = 'SELECT clan_id
        from clan join clan_player on clan_id = _clan_id
        where _player_id = :char_id and member_level > 0 order by clan_id limit 1';

    $id = query_item($sel, array(':char_id'=>array($char_id, PDO::PARAM_INT)));

    if ($id) {
        return get_clan($id);
    } else {
        return null;
    }
}

// Return just the clan leader id for a clan.
function get_clan_leader_id($clan_id) {
	$leader_info = get_clan_leader_info($clan_id);
	return $leader_info['player_id'];
}

// Get the current clan leader or leaders.
function get_clan_leaders($clan_id=null, $all=false) {
	$limit = ($all ? '' : ' LIMIT 1');
	$clan_or_clans = ($clan_id ? " AND clan_id = :clan ORDER BY member_level DESC, level DESC" : ' ORDER BY clan_id, member_level DESC, level DESC');
	DatabaseConnection::getInstance();
	$clans = DatabaseConnection::$pdo->prepare("SELECT clan_id, clan_name, clan_founder, player_id, uname
		FROM clan JOIN clan_player ON clan_id = _clan_id JOIN players ON player_id=_player_id
		WHERE confirmed = 1 AND member_level > 0 $clan_or_clans $limit");
	if ($clan_id) {
		$clans->bindValue(':clan', $clan_id);
	}
	$clans->execute();
	return $clans;
}

// Save the url of the clan avatar to the database.
function save_clan_avatar_url($url, $clan_id){
    $update = 'update clan set clan_avatar_url = :url where clan_id = :clan_id';
    query_resultset($update, array(':url'=>$url, ':clan_id'=>$clan_id));
}


// Get the clan info from the database.
function clan_data($clan_id){
    $sel = 'select * from clan where clan_id = :clan_id';
    return query_row($sel, array(':clan_id'=>$clan_id));
}

// Save the clan description to the database.
function save_clan_description($desc, $clan_id){
    $update = 'update clan set description = :desc where clan_id = :clan_id';
    query_resultset($update, array(':desc'=>$desc, ':clan_id'=>$clan_id));
}


// return boolean, checks that an avatar is valid.
function clan_avatar_is_valid($dirty_url) {
    if ($dirty_url === "" || $dirty_url === null) {
        return true;  // Allows for no clan avatar.
    }

    $is_url = ($dirty_url == filter_var($dirty_url, FILTER_VALIDATE_URL));

    if (!$is_url) {
        return false;
    } else {
        $parts = @parse_url($dirty_url);
        return !!preg_match('#[\w\d]*\.imageshack\.[\w\d]*#i', $parts['host']);
    }
}

// Functions for creating the clan ranking tag cloud.

/**
 * This determines the criteria for how the clans get ranked and tagged, and shows only non-empty clans.
**/
function clans_ranked() {
	$res = array();

	// sum the levels of the players (minus days of inactivity) for each clan
	$counts = query("SELECT sum(round(((level+4)/5+8)-(days/3))) AS sum, clan_name, clan_id
	    FROM clan JOIN clan_player ON clan_id = _clan_id JOIN players ON _player_id = player_id
	    WHERE confirmed = 1 GROUP BY clan_id, clan_name ORDER BY sum DESC");

	if ($counts->rowCount() > 0) {
		$clan_info = $counts->fetch();
		$max       = $clan_info['sum'];

		do {
			// *** make percentage of highest, multiply by 10 and round to give a 1-10 size ***
			$res[$clan_info['clan_id']]['name'] = $clan_info['clan_name'];
			$res[$clan_info['clan_id']]['score'] = floor(( (($clan_info['sum'] - 1 < 1 ? 0 : $clan_info['sum'] - 1)) / $max) * 10) + 1;
		} while ($clan_info = $counts->fetch());
	}

	return $res;
}

/* Display the clan info */
function render_clan_view($p_clan_id) {
	if (!$p_clan_id) {
		return ''; // No viewing criteria available.
	}

    $members_array = query_array("SELECT uname, email, clan_name, level, days, clan_founder, player_id, member_level
			FROM clan
			JOIN clan_player ON _clan_id = :clan_id AND clan_id = _clan_id
			JOIN players ON player_id = _player_id AND confirmed = 1 ORDER BY level, health DESC",
			array(':clan_id'=>$p_clan_id));

	$max = query_item("SELECT max(level) AS max
		FROM clan
		JOIN clan_player ON _clan_id = :clan_id AND clan_id = _clan_id
		JOIN players ON player_id = _player_id AND confirmed = 1",
		array(":clan_id"=>$p_clan_id));

	$clan = get_clan($p_clan_id); // Clan data array.
	$clan_name = $clan['clan_name'];

    $count = 0;

    // Modify the members by reference
	foreach ($members_array as &$member) {
		$member['size'] = floor( ( ($member['level'] - $member['days'] < 1 ? 0 : $member['level'] - $member['days']) / $max) * 2) + 1;

        $current_leader_class = null;
        // Calc the member display size based on their level relative to the max.
		if ($member['member_level'] == 1) {
		    $current_leader_class = 'original-creator';
			$member['size'] = $member['size'] + 2;
			$member['size'] = ($member['size'] > 3 ? 3 : $member['size']);
		}

        $member['gravatar_url'] = generate_gravatar_url($member['player_id']);
        $count++;
	}

	return render_template('clan.info.tpl',
	    array(
	        'members_array'=>$members_array,
	        'clan_name'=>$clan_name,
	        'count'=>$count,
            'avatar_url'=>$clan['clan_avatar_url'],
            'clan_name'=>$clan['clan_name'],
            'clan_description'=>$clan['description']
		)
	);
}

// Get clan member names & ids other than self, useful for lists & messaging
function clan_member_names_and_ids($clan_id, $self_char_id){
    $member_select = "select uname, player_id from players join clan_player on player_id = _player_id
        where _clan_id = :clan_id and player_id != :player_id";
    $members_and_ids = query_array($member_select, array(':clan_id'=>$clan_id, ':player_id'=>$self_char_id));
    return $members_and_ids;
}
?>
