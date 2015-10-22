<?php
namespace app\data;
require_once(CORE.'data/database.php');

use Illuminate\Database\Eloquent\Model;

class Message extends Model{
	protected $primaryKey = 'message_id'; // Anything other than id
    public $timestamps = false;
    // The non-mass-fillable fields
    protected $guarded = ['message_id', 'date'];
    /**
    Currently:
    message_id | serial
    message | text
    date | timestamp
    send_to | foreign key
    send_from | foreign key
    unread | integer default 1
    type | integer default 0
    */

    /**
     * Custom initialization of `date` field, since this model only keeps one
    **/
    public static function boot(){
        static::creating(function($model){
            $model->date = $model->freshTimestamp();
        });
    }


    /**
     * Special case method to get the id regardless of what it's actually called in the database
    **/
    public function id(){
    	return $this->message_id;
    }

}