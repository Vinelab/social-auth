<?php namespace Vinelab\Auth\Contracts;

interface UserInterface {

	/**
	 * Create a new User record
	 *
	 * @param  string $name
	 * @param  string $email
	 * @return UserEntity
	 */
	public function create($name, $email, $avatar);

	/**
	 * Find a user record by id
	 *
	 * @throws  Illuminate\Database\Eloquent\ModelNotFoundException
	 *
	 * @param  string $id
	 * @return UserEntity
	 */
	public function find($id);

	/**
	 * Find a user record by an attribute
	 *
	 * @param  string $attribute
	 * @param  string $value
	 * @return UserEntity
	 */
	public function findBy($attribute, $value);

	/**
	 * Fill an entity with attributes and return it
	 *
	 * @param  array $attributes
	 * @return UserEntity
	 */
	public function fill($attributes);

	/**
	 * Fill an entity with attributes and save it afterwards
	 *
	 * @param  array $attributes
	 * @return UserEntity
	 */
	public function fillAndSave($attributes);

}