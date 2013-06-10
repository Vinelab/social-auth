<?php namespace Vinelab\Auth\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Vinelab\Auth\Social\AccessToken;

Class AccessTokenTest extends TestCase {

	public function testInstantiation()
	{
		$at = new AccessToken(['access_token'=>'123', 'expires'=>'12345']);
		$this->assertInstanceOf('Vinelab\Auth\Social\AccessToken', 	$at);
		$this->assertEquals('123', $at->token);
		$this->assertEquals('12345', $at->expires);
	}

	/**
	 * @expectedException Vinelab\Auth\Exception\AccessTokenException
	 */
	public function testInstantiationWithNull()
	{
		$at = new AccessToken(null);

	}

	/**
	 * @expectedException Vinelab\Auth\Exception\AccessTokenException
	 */
	public function testInstantiationWithString()
	{
		$at = new AccessToken('access_token=123&expires=1234');
	}

	/**
	 * @expectedException Vinelab\Auth\Exception\AccessTokenException
	 */
	public function testInstantiationWithObject()
	{
		$object = new \stdClass;
		$object->access_token = '123';
		$object->expires = '12345';
		$at = new AccessToken($object);
	}
}