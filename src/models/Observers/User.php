<?php namespace Vinelab\Auth\Models\Observers;

use Hash;

Class User {

	public function creating($user)
	{
		if(isset($user->password))
		{
			$user->password = Hash::make($user->password);
		}

		$user->guid = uniqid();
	}

}