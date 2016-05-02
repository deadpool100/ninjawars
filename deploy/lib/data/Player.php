<?php
namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;
use NinjaWars\core\data\Clan;
use NinjaWars\core\Filter;
use NinjaWars\core\data\PlayerDAO;
use NinjaWars\core\data\PlayerVO;
use NinjaWars\core\data\Character;
use NinjaWars\core\data\GameLog;
use NinjaWars\core\data\Account;
use NinjaWars\core\data\Event;
use NinjaWars\core\extensions\SessionFactory;
use \PDO;
use \RuntimeException;

/**
 * Ninja (actually character) behavior object.
 *
 * This file should make use of a private PlayerVO.class.php and PlayerDAO.class.php
 * to propagate and save its data.
 *
 * @package     char
 * @subpackage	player
 * @author      Tchalvak <ninjawarsTchalvak@gmail.com>
 * @link        http://ninjawars.net/player.php?player=tchalvak
 * @property int health
 * @property int kills
 * @property int gold
 * @property int level
 * @property int turns
 * @property int bounty
 * @property int ki
 * @property int karma
 * @property int active
 * @property string identity Identity of the character class
 * @property string goals
 * @property string description
 * @property string messages
 * @property string instincts
 * @property string beliefs
 * @property string traits
 * @property string uname Deprecated in favor of ->name() method
 * @property int status
 */
class Player implements Character {
	public $ip;
	public $avatar_url;
    private $data;
	private $vo;

    /**
     * Creates a new level 1 player object
     */
    public function __construct() {
        $level = 1;

        $this->vo                  = new PlayerVO();
        $this->avatar_url          = null;
        $this->uname               = null;
        $this->health              = self::maxHealthByLevel($level);
        $this->strength            = self::baseStrengthByLevel($level);
        $this->speed               = self::baseSpeedByLevel($level);
        $this->stamina             = self::baseStaminaByLevel($level);
        $this->level               = $level;
        $this->gold                = 100;
        $this->turns               = 180;
        $this->kills               = 0;
        $this->status              = 0;
        $this->member              = 0;
        $this->days                = 0;
        $this->bounty              = 0;
        $this->energy              = 0;
        $this->ki                  = 0;
        $this->karma               = 0;
        $this->avatar_type         = 1;
        $this->messages            = '';
        $this->description         = '';
        $this->instincts           = '';
        $this->traits              = '';
        $this->beliefs             = '';
        $this->goals               = '';
        $this->last_started_attack = '2016-01-01';
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->name();
    }

    /**
     * Magic method to provide accessors for properties
     *
     * @return mixed
     */
    public function __get($member_field) {
        return $this->vo->$member_field;
    }

    /**
     * Magic method to provide mutators for properties
     *
     * @return mixed
     */
    public function __set($member_field, $value) {
        return $this->vo->$member_field = $value;
    }

    /**
     * Magic method to handle isset() and empty() calls against properties
     *
     * @return boolean
     */
    public function __isset($member_field) {
        return isset($this->vo->$member_field);
    }

    /**
     *
     */
    public function __clone() {
        $this->vo = clone $this->vo;
    }

    /**
     * @return string
     */
    public function name() {
        return $this->vo->uname;
    }

    /**
     * @return int
     */
	public function id() {
		return $this->vo->player_id;
	}

    /**
     * Adds a defined numeric status constant to the binary string of statuses
     */
    public function addStatus($p_status) {
        $status = self::validStatus($p_status);

        if ($status && !$this->hasStatus($status)) {
            if (gettype($this->status | $status) !== 'integer') {
                throw new \InvalidArgumentException('invalid type for status');
            }

            $this->status = ($this->status | $status);
        }
    }

    /**
     * Remove a numeric status from the binary string of status toggles.
     */
    public function subtractStatus($p_status) {
        $status = self::validStatus($p_status);

        if ($status && $this->hasStatus($status)) {
            if (gettype($this->status & ~$status) !== 'integer') {
                throw new \InvalidArgumentException('invalid type for status');
            }

            $this->status = ($this->status & ~$status);
        }
    }

    /**
     * Resets the binary status info to 0/none
     */
	public function resetStatus() {
		$this->status = 0;
	}

    /**
     * Determine whether a pc is effected by a certain status
     * @return boolean
     */
	public function hasStatus($p_status) {
        $status = self::validStatus($p_status);

        return ((bool)$status && (bool)($this->status & $status));
	}

    /**
     * Standard damage output from 1 to max
     * @return int
     */
	public function damage(Character $enemy=null){
		return rand(1, $this->maxDamage($enemy));
	}

    /**
     * Max damage capability of a character
     *
     * @return int
     */
	public function maxDamage(Character $enemy=null){
        return (int) ($this->getStrength() * 5 + $this->getSpeed());
    }

    /**
     * @return int
     */
	public function getStrength() {
        $str = NEW_PLAYER_INITIAL_STATS + $this->level * LEVEL_UP_STAT_RAISE;
		if ($this->hasStatus(WEAKENED)) {
			return (int) max(1, $str-(ceil($str*.25))); // 75%
		} elseif ($this->hasStatus(STR_UP2)) {
			return (int) ($str+(ceil($str*.50))); // 150%
		} elseif ($this->hasStatus(STR_UP1)) {
			return (int) ($str+(ceil($str*.25))); //125%
		} else {
			return (int) $str;
		}
	}

	public function setStrength($str){
		if($str < 0){
			throw new \InvalidArgumentException('Strength cannot be set as a negative.');
		}
		$this->vo->strength = $str;
	}

	public function getSpeed() {
        $speed = NEW_PLAYER_INITIAL_STATS + $this->level * LEVEL_UP_STAT_RAISE;
		if ($this->hasStatus(SLOW)) {
			return (int) ($speed-(ceil($speed*.25)));
		} else {
			return (int) $speed;
		}
	}

	public function setSpeed($speed){
		if($speed < 0){
			throw new \InvalidArgumentException('Speed cannot be set as a negative.');
		}
		$this->vo->speed = $speed;
	}

	public function getStamina() {
		$stam = NEW_PLAYER_INITIAL_STATS + $this->level * LEVEL_UP_STAT_RAISE;
		if ($this->hasStatus(POISON)) {
			return (int) ($stam-(ceil($stam*.25)));
		} else {
			return (int) $stam;
		}
	}

	public function setStamina($stamina){
		if($stamina < 0){
			throw new \InvalidArgumentException('Stamina cannot be set as a negative.');
		}
		$this->vo->stamina = $stamina;
	}

	public function setKi($ki){
		if($ki < 0){
			throw new \InvalidArgumentException('Ki cannot be negative.');
		}
		return $this->vo->ki = $ki;
	}

	public function setGold($gold) {
		if ($gold < 0) {
			throw new \InvalidArgumentException('Gold cannot be made negative.');
		}

		if (is_numeric($gold) && (int) $gold != $gold) {
			throw new \InvalidArgumentException('Gold must be a whole number [not '.(string)$gold.'].');
		}

		return $this->vo->gold = $gold;
	}

	public function setBounty($bounty) {
		if($bounty < 0){
			throw new \InvalidArgumentException('Bounty cannot be made negative ['.(string)$bounty.'].');
		}
		if((int) $bounty != $bounty){
			throw new \InvalidArgumentException('Bounty must be a whole number [not '.(string)$bounty.'].');
		}
		return $this->vo->bounty = $bounty;
	}

	/**
	 * Checks whether the character is still active.
     *
     * @return boolean
	 */
	public function isActive() {
		return (bool) $this->vo->active;
	}

    /**
     * @return boolean
     * hardcoded hack at the moment
     * @note To be replaced by an in-database account toggle eventually
     */
	public function isAdmin() {
		$name = strtolower($this->name());
		if ($name == 'tchalvak' || $name == 'beagle' || $name == 'suavisimo') {
			return true;
		}

		return false;
	}

    /**
     * Cleanup player to death state
     *
     * @return void
     * @note
     * This method writes the player object to the database
     */
	public function death() {
		$this->resetStatus();
        $this->setHealth(0);
        $this->save();
	}

    /**
     * Changes the turns propety of the player object
     *
     * @param int $turns
     * @return int The number of turns the player object now has
     * @throws InvalidArgumentException $turns cannot be negative
     */
    public function setTurns($turns) {
        if ($turns < 0) {
            throw new \InvalidArgumentException('Turns cannot be made negative.');
        }

        return $this->vo->turns = $turns;
    }

    /**
     * @deprecated
     */
    public function changeTurns($amount) {
        return $this->setTurns($this->turns + (int) $amount);
    }

    /**
     * @return integer
     */
    public function getMaxHealth() {
        return $this->getStamina()*2;
    }

    /**
     * Returns the state of the player from the database,
     *
     * @return array
     */
    public function data() {
		if (!$this->data) {
            $this->data = (array) $this->vo;
            $this->data['next_level']    = $this->killsRequiredForNextLevel();
            $this->data['max_health']    = $this->getMaxHealth();
            $this->data['hp_percent']    = $this->health_percent();
            $this->data['max_turns']     = 100;
            $this->data['turns_percent'] = min(100, round($this->data['turns']/$this->data['max_turns']*100));
            $this->data['exp_percent']   = min(100, round(($this->data['kills']/$this->data['next_level'])*100));
            $this->data['status_list']   = implode(', ', self::getStatusList($this->id()));
            $this->data['hash']          = md5(implode($this->data));
            $this->data['class_name']    = $this->data['identity'];
            $this->data['clan_id']       = ($this->getClan() ? $this->getClan()->id : null);

            unset($this->data['pname']);
        }

        return $this->data;
    }

    /**
     * Return the data that should be publicly readable to javascript or the api while the player is logged in.
     *
     * @return array
     */
    public function publicData() {
        $char_info = $this->data();
        unset($char_info['ip'], $char_info['member'], $char_info['pname'], $char_info['verification_number'], $char_info['confirmed']);

        return $char_info;
    }

    /**
     * @return Clan
     */
    public function getClan() {
        return Clan::findByMember($this);
    }

	/**
	 * Heal the char with in the limits of their max
     *
     * @return int
	 */
	public function heal($amount) {
		// do not heal above max health
        $heal = min($this->is_hurt_by(), $amount);
        return $this->setHealth($this->health + $heal);
	}

	/**
	 * Do some damage to the character
     *
     * @param int $damage
     * @return int
	 */
	public function harm($damage) {
		// Do not allow negative health
		$actual_damage = min($this->health, (int) $damage);
		return $this->setHealth($this->health - $actual_damage);
	}

    /**
     * @return int
     */
	public function getHealth() {
        return $this->health;
	}

    /**
     * @return int
     */
	public function setHealth($health) {
		if ($health < 0) {
			throw new \InvalidArgumentException('Health cannot be made negative.');
		}

		if ((int) $health != $health) {
			throw new \InvalidArgumentException('Health must be a whole number.');
		}

		return $this->vo->health = (int) max(0, $health);
	}

	/**
	 * Return the amount below the max health (or zero).
	 * @return int
	 */
	public function is_hurt_by() {
		return max(0,
			(int) ($this->getMaxHealth() - $this->health)
		);
	}

    /**
     * Return the current percentage of the maximum health that a character could have.
     * @return int
     */
	public function health_percent() {
        return min(100, round(($this->health/$this->getMaxHealth())*100));
	}

    /**
     * @return int difficulty rating
     */
	public function difficulty(){
		return (int) ( 10 + $this->getStrength() * 2 + $this->maxDamage());
	}

    /**
     * @return int random private number unique to character
     */
	public function getVerificationNumber(){
		return $this->vo->verification_number;
	}

    /**
     * @return string url for the gravatar of pc
     */
    public function avatarUrl() {
        if (!isset($this->avatar_url) || $this->avatar_url === null) {
            $this->avatar_url = $this->generateGravatarUrl();
        }

        return $this->avatar_url;
    }

    private function generateGravatarUrl() {
        $account = Account::findByChar($this);

        if (OFFLINE) {
            return IMAGE_ROOT.'default_avatar.png';
        } else if (!$this->vo || !$this->vo->avatar_type || !$account || !$account->email()) {
            return '';
        } else {
            $email       = $account->email();

            $def         = 'monsterid'; // Default image or image class.
            // other options: wavatar (polygonal creature) , monsterid, identicon (random shape)
            $base        = "http://www.gravatar.com/avatar/";
            $hash        = md5(trim(strtolower($email)));
            $no_gravatar = "d=".urlencode($def);
            $size        = 80;
            $rating      = "r=x";
            $res         = $base.$hash."?".implode('&', [$no_gravatar, $size, $rating]);

            return $res;
        }
	}

	/**
	 * Persist object to database
     *
	 * @return Player
	 */
	public function save() {
		$factory = new PlayerDAO();
		$factory->save($this->vo);

		return $this;
	}

     /**
     * Check whether the player is the leader of their clan.
     * @return boolean
     */
    public function isClanLeader() {
        return (($clan = Clan::findByMember($this)) && $this->id() == $clan->getLeaderID());
    }

    /**
     * Set the character's class, using the identity.
     * @return string|null error string if fails
     */
    public function setClass($new_class) {
        if (!$this->isValidClass(strtolower($new_class))) {
            return "That class was not an option to change into.";
        } else {
            $class_id = query_item(
                "SELECT class_id FROM class WHERE class.identity = :class",
                [':class' => strtolower($new_class)]
            );

            $up = "UPDATE players SET _class_id = :class_id WHERE player_id = :char_id";

            query($up, [
                ':class_id' => $class_id,
                ':char_id'  => $this->id(),
            ]);

            $this->vo->identity  = $new_class;
            $this->vo->_class_id = $class_id;

            return null;
        }
    }

    /**
     * Get the ninja's class's name.
     */
    public function getClassName() {
        return $this->vo->identity;
    }

    /**
     * Check that a class matches against the class identities available in the database.
     *
     * @return boolean
     */
    private function isValidClass($candidate_identity) {
        return (boolean) query_item(
            "SELECT identity FROM class WHERE identity = :candidate",
            [':candidate' => $candidate_identity]
        );
    }

    /**
     * The number of kills needed to level up to the next level.
     *
     * 5 more kills in cost for every level you go up.
     * @return int
     */
    public function killsRequiredForNextLevel() {
       return $this->level*5;
    }

    /**
     * Takes in a Character and adds kills to that character.
     * @return int
     */
    public function addKills($amount) {
        return $this->changeKills((int)abs($amount));
    }

    /**
     * Takes in a Character and removes kills from that character.
     * @return int
     */
    public function subtractKills($amount) {
        return $this->changeKills(-1*((int)abs($amount)));
    }

    /**
     * Change the kills amount of a char, and levels them up when necessary.
     * @return int
     */
    private function changeKills($amount) {
        $amount = (int)$amount;

        GameLog::updateLevellingLog($this->id(), $amount);

        if ($amount !== 0) { // Ignore changes that amount to zero.
            if ($amount > 0) { // when adding kills, check if levelling occurs
                $this->levelUp();
            }

            query(
                "UPDATE players SET kills = kills + CASE WHEN kills + :amount1 < 0 THEN kills*(-1) ELSE :amount2 END WHERE player_id = :player_id",
                [
                    ':amount1'   => [$amount, PDO::PARAM_INT],
                    ':amount2'   => [$amount, PDO::PARAM_INT],
                    ':player_id' => $this->id(),
                ]
            );
        }

        return $this->vo->kills = query_item(
            "SELECT kills FROM players WHERE player_id = :player_id",
            [
                ':player_id' => [$this->id(), PDO::PARAM_INT],
            ]
        );
    }

    /**
     * Leveling up Function
     *
     * @return boolean
     */
    public function levelUp() {
        $health_to_add     = 100;
        $turns_to_give     = 50;
        $ki_to_give        = 50;
        $stat_value_to_add = 5;
        $karma_to_give     = 1;

        if ($this->isAdmin()) { // If the character is an admin, do not auto-level
            return false;
        } else { // For normal characters, do auto-level
            // Have to be under the max level and have enough kills.
            $level_up_possible = (
                ($this->level + 1 <= MAX_PLAYER_LEVEL) &&
                ($this->kills >= $this->killsRequiredForNextLevel())
            );

            if ($level_up_possible) { // Perform the level up actions
                $this->setHealth($this->health + $health_to_add);
                $this->setTurns($this->turns   + $turns_to_give);
                $this->setKi($this->ki         + $ki_to_give);

                // Must read from VO for these as accessors return modified values
                $this->setStamina($this->vo->stamina   + $stat_value_to_add);
                $this->setStrength($this->vo->strength + $stat_value_to_add);
                $this->setSpeed($this->vo->speed       + $stat_value_to_add);

                // no mutator for these yet
                $this->vo->kills = max(0, $this->kills - $this->killsRequiredForNextLevel());
                $this->vo->karma = ($this->karma + $karma_to_give);
                $this->vo->level = ($this->level + 1);

                $this->save();

                GameLog::recordLevelUp($this->id());

                $account = Account::findByChar($this);
                $account->setKarmaTotal($account->getKarmaTotal() + $karma_to_give);
                $account->save();

                // Send a level-up message, for those times when auto-levelling happens.
                Event::create($this->id(), $this->id(),
                    "You levelled up! Your strength raised by $stat_value_to_add, speed by $stat_value_to_add, stamina by $stat_value_to_add, Karma by $karma_to_give, and your Ki raised $ki_to_give! You gained some health and turns, as well! You are now a level {$this->level} ninja! Go kill some stuff.");
                return true;
            } else {
                return false;
            }
        }
    }

	/**
	 * Find a player by primary key
     * @param int|null $id
	 * @return Player|null
	 */
	public static function find($id){
		if(!is_numeric($id) || !(int) $id){
			return null;
		}
		$id = (int) $id;
		$dao = new PlayerDAO();
		$data = $dao->get($id);
		if(!isset($data->player_id) || !$data->player_id){
			return null;
		}
		$player = new Player();
		$player->vo = $data;
		return $player;
	}

    /**
     * Find a char by playable for account
     * @param int|null $account_id
     * @return Player|null
     */
    public static function findPlayable($account_id){
        // Two db calls for now
        $pid = query_item('select player_id from players p 
            join account_players ap on p.player_id = ap._player_id
            join accounts a on a.account_id = ap._account_id
            where account_id = :aid
            order by p.created_date asc, a.last_login desc
            limit 1', [':aid'=>[$account_id, PDO::PARAM_INT]]);
        return self::find($pid);
    }

    /**
     * Find player by name
     * @return Player|null
     */
    public static function findByName($name){
        $id = query_item('select player_id from players where lower(uname) = lower(:name) limit 1', [':name'=>$name]);
        if(!$id){
            return null;
        } else {
            $dao = new PlayerDAO();
            $data = $dao->get($id);
            if(!isset($data->player_id) || !$data->player_id){
                return null;
            }
            $player = new Player();
            $player->vo = $data;
            return $player;
        }
    }

    /**
     * query the recently active players
     * @return array Array of data not of player objects
     */
    public static function findActive($limit=5, $alive_only=true) {
        $where_cond = ($alive_only ? ' AND health > 0' : '');
        $sel = "SELECT uname, player_id FROM players WHERE active = 1 $where_cond ORDER BY last_started_attack DESC LIMIT :limit";
        $active_ninjas = query_array($sel, array(':limit'=>array($limit, PDO::PARAM_INT)));
        return $active_ninjas;
    }

    /**
     * @return integer|null
     * @note this needs review overall, as nonexistent high int statuses will false positive
     */
    public static function validStatus($dirty) {
        if (is_numeric($dirty) && (int)$dirty == $dirty) {
            return (int) $dirty;
        } elseif (is_string($dirty)) {
            $status = strtoupper($dirty);

            if (defined($status)) {
                return (int) constant($status);
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Returns a comma-seperated string of states based on the statuses of the target.
     *
     * @param array $statuses status array
     * @param string $target the target, username if self targetting.
     * @return string
     *
     */
    public static function getStatusList($target=null) {
        $states = array();
        $target = (isset($target) && (int)$target == $target ? $target : SessionFactory::getSession()->get('player_id'));

        // Default to showing own status.
        $target = self::find($target);

        if (!$target || $target->health < 1) {
            $states[] = 'Dead';
        } else { // *** Other statuses only display if not dead.
            if ($target->health < 80) {
                $states[] = 'Injured';
            } else {
                $states[] = 'Healthy';
            }

            // The visibly viewable statuses.
            if ($target->hasStatus(STEALTH)) { $states[] = 'Stealthed'; }
            if ($target->hasStatus(POISON)) { $states[] = 'Poisoned'; }
            if ($target->hasStatus(WEAKENED)) { $states[] = 'Weakened'; }
            if ($target->hasStatus(FROZEN)) { $states[] = 'Frozen'; }
            if ($target->hasStatus(STR_UP1)) { $states[] = 'Buff'; }
            if ($target->hasStatus(STR_UP2)) { $states[] = 'Strength+'; }

            // If any of the shield skills are up, show a single status state for any.
            if ($target->hasStatus(FIRE_RESISTING) || $target->hasStatus(INSULATED) || $target->hasStatus(GROUNDED)
                || $target->hasStatus(BLESSED) || $target->hasStatus(IMMUNIZED)
                || $target->hasStatus(ACID_RESISTING)) {
                $states[] = 'Shielded';
            }
        }

        return $states;
    }

    /**
     * Calculate a max health by a level
     * @return integer
     */
    public static function maxHealthByLevel($level) {
        return (int) (NEW_PLAYER_INITIAL_HEALTH + round(LEVEL_UP_HP_RAISE*($level-1)));
    }

    /**
     * Calculate a base str by level
     */
    public static function baseStrengthByLevel($level) {
        return NEW_PLAYER_INITIAL_STATS + (LEVEL_UP_STAT_RAISE * ($level-1));
    }

    /**
     * Calculate a base speed by level
     */
    public static function baseSpeedByLevel($level) {
        return NEW_PLAYER_INITIAL_STATS + (LEVEL_UP_STAT_RAISE * ($level-1));
    }

    /**
     * Calculate a base stamina by level
     */
    public static function baseStaminaByLevel($level) {
        return NEW_PLAYER_INITIAL_STATS + (LEVEL_UP_STAT_RAISE * ($level-1));
    }
}
