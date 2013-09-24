<?php namespace Vinelab\Auth\Contracts;

interface SocialAccountEntityInterface {

    public function create($socialAccount);

    public function findOrFail($id);

    public function where($attribute, $value);

    public function first($fields = array());

    public function get($fields = array());
}