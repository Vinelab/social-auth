<?php namespace Vinelab\Auth\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Mockery as M;

use Vinelab\Auth\Repositories\SocialAccountRepository;

class SocialAccountRepositoryTest extends TestCase {

	public function setUp()
	{
		$this->mSocialAccount = M::mock('Vinelab\Auth\Contracts\SocialAccountEntityInterface');

		$this->socialAccounts = new SocialAccountRepository($this->mSocialAccount);
	}

	public function test_instantiation()
	{
		$sa = new SocialAccountRepository($this->mSocialAccount);

		$this->assertInstanceOf('Vinelab\Auth\Repositories\SocialAccountRepository', $sa);
	}

	public function test_creating_social_account()
	{
		$this->mSocialAccount->shouldReceive('create')->once()
								->with([
									'network'=>'network',
									 'account_id'=>'account_id',
									 'user_id'=>'user_id',
									 'access_token'=>'access_token'
								])->andReturn('SocialAccountEntity');

		$sa = $this->socialAccounts->create('network', 'account_id', 'user_id', 'access_token');
		$this->assertEquals('SocialAccountEntity', $sa);
	}

	public function test_finding_social_account()
	{
		$this->mSocialAccount->shouldReceive('findOrFail')->once()
								->with('id')->andReturn('SocialAccountEntity');
		$this->assertEquals('SocialAccountEntity', $this->socialAccounts->find('id'));
	}

	public function test_finding_by_attribute()
	{
		$this->mSocialAccount->shouldReceive('where')->once()
								->with('account_id', 'some_account_id')
								->andReturn($this->mSocialAccount);

		$this->mSocialAccount->shouldReceive('first')->once()->andReturn('SocialAccountEntity');

		$sa = $this->socialAccounts->findByAccount_id('some_account_id');
		$this->assertEquals('SocialAccountEntity', $sa);

		$another_sa = $this->socialAccounts->findBy('account_id', 'some_account_id');
		$this->assertEquals('SocialAccountEntity', $another_sa);
	}

	public function test_finding_social_accounts_for_user()
	{
		$this->mSocialAccount->shouldReceive('where')->with('user_id', 'some_user_id')->once()
								->andReturn($this->mSocialAccount);
		$this->mSocialAccount->shouldReceive('get')->once()->andReturn('Illuminate\Database\Eloquent\Collection');

		$sas = $this->socialAccounts->forUser('some_user_id');
		$this->assertEquals('Illuminate\Database\Eloquent\Collection', $sas);
	}
}