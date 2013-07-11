<?php namespace Vinelab\Auth\Repositories;

use Vinelab\Auth\Contracts\SocialAccountInterface;

use Vinelab\Auth\Models\Entities\SocialAccountEntity as SocialAccount;

class SocialAccountRepository implements SocialAccountInterface {

	/**
	 * Instance
	 *
	 * @var SocialAccountEntity
	 */
	protected $_SocialAccount;

	public function __construct(SocialAccount $socialAccount)
	{
		$this->_SocialAccount = $socialAccount;
	}

	public function create($network, $account_id, $user_id, $access_token)
	{
		return $this->_SocialAccount->create(compact('network', 'account_id', 'user_id', 'access_token'));
	}

	public function find($id)
	{
		return $this->_SocialAccount->findOrFail($id);
	}

	public function findBy($attribute, $value)
	{
		return $this->_SocialAccount->where($attribute, $value)->first();
	}

	public function forUser($user_id)
	{
		return $this->_SocialAccount->where('user_id', $user_id)->get();
	}

	public function userAccount($user_id, $account_id)
	{
		return $this->_SocialAccount->where('user_id')->first();
	}

	public function exist($user_id, $account_id)
	{
		return !is_null($this->_SocialAccount->where('user_id', $user_id)->where('account_id', $account_id)->first());
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