<?php namespace Vinelab\Auth\Contracts;

interface SocialAccountInterface {

	/**
	 * Create a new social account record
	 *
	 * @param  string $network
	 * @param  string $account_id
	 * @param  string $access_token
	 * @return SocialAccountEntity
	 */
	public function create($network, $account_id, $user_id, $access_token);

	/**
	 * Find a social account by id
	 *
	 * @throws Illuminate\Database\Eloquent\ModelNotFoundException
	 *
	 * @param  string $id
	 * @return SocialAccountEntity
	 */
	public function find($id);

	/**
	 * Find a social account by an attribute
	 *
	 * @param  string $attribute
	 * @param  string $value
	 * @return SocialAccountEntity
	 */
	public function findBy($attribute, $value);

	/**
	 * Get all the social accounts of a user
	 *
	 * @param  string $user_id
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function forUser($user_id);

}