<?php namespace Vinelab\Auth\Repositories;

use Vinelab\Auth\Contracts\UserInterface;

use Vinelab\Auth\Models\Entities\UserEntity as User;

class UserRepository implements UserInterface {

	/**
	 * Instance
	 *
	 * @var UserEntity
	 */
	protected $_User;

	public function __construct(User $user)
	{
		$this->_User = $user;
	}

	public function create($name, $email, $avatar = '')
	{
		return $this->_User->create(compact('name', 'email'));
	}

	public function find($id)
	{
		return $this->_User->findOrFail($id);
	}

	public function findBy($attribute, $value)
	{
		return $this->_User->where($attribute, $value)->first();
	}

	public function fill($attributes)
	{
		return $this->_User->fill($attributes);
	}

	public function fillAndSave($attributes)
	{
		$this->_User->fill($attributes);
		$this->_User->save();

		return $this->_User;
	}

	public function __call($method, $arguments)
	{
		/**
		 * findBy convenience calling to be available
		 * through findByName and findByTitle etc.
		 */

		if (preg_match('/^findBy/', $method))
		{
			$attribute = strtolower(substr($method, 6));
			array_unshift($arguments, $attribute);
			return call_user_func_array(array($this, 'findBy'), $arguments);
		}
	}
}