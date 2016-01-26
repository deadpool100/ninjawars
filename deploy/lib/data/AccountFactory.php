<?php
require_once(ROOT . "core/control/Character.php");
require_once(ROOT . "core/data/Account.php");

/**
 * Create account objects via Factory(ok, actually Repository) object
 *
 *
**/
class AccountFactory{

	public static function make($account_id){
		return new Account($account_id);
	}

	public static function first(){
		$account_id = query_item('select account_id from accounts where operational limit 1');
		return new Account($account_id);
	}

	/*
	public static function create($email, $type){
		// Create a new account.
		$account_id = create_account($ninja_id, $email, $password_to_hash, $confirm, $type=0, $active=1)
	}*/

	public static function find($email_identity){
		$account_info = self::account_info_by_identity($email_identity);
		return new Account($account_info['account_id']);
	}

    /**
     * Get the account object by id, or false
     *
     * @param int $id
     * @return Account|false
     */
    public static function findById($id) {
        $account = new Account($id);

        if (!$account->getIdentity()) {
            return false;
        } else {
            return $account;
        }
    }

    /**
     * Get an account for a character
     *
     * @param Character $char
     * @return Account
     */
    public static function findByChar(Character $char) {
        $query = 'SELECT account_id FROM accounts
            JOIN account_players ON _account_id = account_id
            JOIN players ON _player_id = player_id
            WHERE players.player_id = :pid';

        return new Account(query_item($query, [':pid' => $char->id()]));
    }

    /**
     * Account identity, so whatever email was ORIGINALLY signed up with
     *
     * @param String $identity_email
     * @return Account
     */
    public static function findByIdentity($identity_email) {
        $info = self::account_info_by_identity($identity_email);
        return new Account($info['account_id']);
    }

    /**
     * Find account by active_email (as opposed to identity)
     *
     * @param String $email
     * @return Account|false
     */
    public static function findByEmail($email) {
        $normalized_email = strtolower(trim($email));

        if ($normalized_email === '') {
            return false;
        }

        $query = 'SELECT account_id FROM accounts WHERE lower(active_email) = lower(:email) LIMIT 1';

        return new Account(query_item($query, [':email' => $normalized_email]));
    }

    /**
     * Get the Account by a ninja name (aka player.uname).
     *
     * @param String $ninja_name
     * @return Account
     */
    public static function findByNinjaName($ninja_name){
        $query = 'SELECT account_id FROM accounts
            JOIN account_players ON account_id = _account_id
            JOIN players ON player_id = _player_id
            WHERE lower(uname) = lower(:ninja_name) LIMIT 1';

        return new Account(query_item($query, [':ninja_name'=>$ninja_name]));
    }

    /**
     * Get the account that matches an oauth id.
     *
     * @param int $id
     * @param String $provider (optional) Defaults to facebook
     * @return Account|false
     */
	public static function findAccountByOauthId($id, $provider='facebook'){
		$account_info = self::find_account_info_by_oauth($id, $provider);
		if(!$account_info['account_id']){
			return false;
		}
		return new Account($account_info['account_id']);
	}

	/**
	 * A partial save of account information.
	**/ 
	public static function save($account){
		$params = [':identity'=>$account->getIdentity(), ':active_email'=>$account->getActiveEmail(), ':type'=>$account->getType(),
			':oauth_provider'=>$account->getOauthProvider(), ':oauth_id'=>(string)$account->getOauthId($account->getOauthProvider()), 
			':account_id'=>$account->getId()
			];
		$updated = update_query('update accounts set 
				account_identity = :identity, active_email = :active_email, type = :type, oauth_provider = :oauth_provider,
				oauth_id = :oauth_id				
				where account_id = :account_id', $params);
		return $updated;
	}

	// Get the account linked with an identity email.
	public static function account_info_by_identity($identity_email) {
		return query_row('select * from accounts where account_identity = :identity_email',
			array(':identity_email'=>$identity_email));
	}

	/**
	 * Get the account that matches an oauth provider.
	 */
	public static function find_account_info_by_oauth($accountId, $provider='facebook') {
		$accountId = positive_int($accountId);
		$account_info = query_row('select * from accounts where ( oauth_id = :id and oauth_provider = :provider )
			order by operational, type, created_date asc limit 1', array(':id'=>$accountId, ':provider'=>$provider));

		if (empty($account_info) || !$account_info['account_id']) {
			return false;
		} else {
			return $account_info;
		}
	}
}
