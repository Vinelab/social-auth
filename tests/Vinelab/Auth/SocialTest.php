<?php namespace Vinelab\Auth\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Mocker as M;
use Vinelab\Auth\Social;

Class SocialTest extends TestCase {

	/**
	 * @expectedException Vinelab\Auth\Exception\ServiceNotSupportedException
	 */
	public function testInstantiation()
	{
		$social = new Social('facebook');
		$this->assertInstanceOf('Vinelab\Auth\Social', $social);

		$unsupportedSocial = new Social('nothing');
	}
}