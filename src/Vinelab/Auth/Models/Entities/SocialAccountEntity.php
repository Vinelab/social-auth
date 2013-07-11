<?php namespace Vinelab\Auth\Models\Entities;

use Illuminate\Database\Eloquent\Model;

use Vinelab\Auth\Models\Observers\EloquentObserver;

class SocialAccountEntity extends Model {

	protected $table = 'social_accounts';

	protected $fillable = ['network', 'account_id', 'user_id', 'access_token'];

	public $incrementing = false;
}

SocialAccountEntity::observe(new EloquentObserver);