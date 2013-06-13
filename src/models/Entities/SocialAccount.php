<?php namespace Vinelab\Auth\Models\Entities;

use Eloquent;

Class SocialAccount extends Eloquent {

	protected $table = 'social_accounts';
	protected $primaryKey = 'account_id';

	public function user()
	{
		return $this->belongsTo('Vinelab\Auth\Models\Entities\User', 'user_id');
	}
}