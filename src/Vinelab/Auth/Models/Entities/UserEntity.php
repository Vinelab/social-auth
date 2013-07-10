<?php namespace Vinelab\Auth\Models\Entities;

use Illuminate\Database\Eloquent\Model;

use Vinelab\Auth\Models\Observers\EloquentObserver;

class UserEntity extends Model {

	protected $table = 'users';

	protected $fillable = ['name', 'email'];

	protected $hidden = ['password'];
}

UserEntity::observe(new EloquentObserver);