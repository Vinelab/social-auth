<?php namespace Vinelab\Auth\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Vinelab\Auth\Repositories\UserRepository;
use Mockery as M;

class UserRepositoryTest extends TestCase {

	public function setUp()
	{
		$this->mUser = M::mock('Vinelab\Auth\Contracts\UserEntityInterface');

		$this->users = new UserRepository($this->mUser);
	}

	public function test_instantiation()
	{
		$users = new UserRepository($this->mUser);
		$this->assertInstanceOf('Vinelab\Auth\Repositories\UserRepository', $users);
	}

	public function test_creating_user()
	{
		$user = [
			'name' => 'Money Honey',
			'email'=> 'money@honey.net'
		];

		$this->mUser->shouldReceive('create')->once()->with($user)->andReturn('UserEntity');

		$user = $this->users->create($user['name'], $user['email']);
		$this->assertEquals('UserEntity', $user);
	}

	public function test_finding_user()
	{
		$this->mUser->shouldReceive('findOrFail')->once()->with('id')->andReturn('UserEntity');
		$user = $this->users->find('id');
		$this->assertEquals('UserEntity', $user);
	}

	public function test_finding_by_attribute()
	{
		$this->mUser->shouldReceive('where')->once()->with('email', 'user_email')->andReturn($this->mUser);
		$this->mUser->shouldReceive('first')->once()->andReturn('UserEntity');

		$user = $this->users->findByEmail('user_email');
		$this->assertEquals('UserEntity', $user);

		$another_user = $this->users->findBy('email', 'user_email');
		$this->assertEquals('UserEntity', $another_user);
	}

	public function test_filling_user()
	{
		$attributes = ['OneRepublic'=>'Secrets'];
		$this->mUser->shouldReceive('fill')->once()->with($attributes)->andReturn($this->mUser);
		$this->assertEquals($this->mUser, $this->users->fill($attributes));
	}

	public function test_filling_and_saving()
	{
		$attributes = ['some'=>'attributes', 'here'=>'and there'];
		$this->mUser->shouldReceive('fill')->once()->with($attributes);
		$this->mUser->shouldReceive('save');

		$user = $this->users->fillAndSave($attributes);
		$this->assertEquals($this->mUser, $user);
	}

}