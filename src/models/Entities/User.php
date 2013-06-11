<?php namespace Vinelab\Auth\Models\Entities;

use Eloquent;

use Vinelab\Auth\Models\Observers\User as UserObserver;

Class User extends Eloquent {

	protected $table      = 'users';
	protected $primaryKey = 'guid';
	protected $hidden	  = ['password', 'id'];
	protected $fillable   = ['name', 'email'];
	protected $guarded	  = ['id', 'guid', 'password'];

}

User::observe(new UserObserver);