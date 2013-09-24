<?php namespace Vinelab\Auth\Contracts;

interface UserEntityInterface {

    public function create($user);

    public function findOrFail($id);

    public function where($attribute, $value);

    public function first($fields = array());

    public function fill($attributes);

    public function save();
}