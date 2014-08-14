<?php namespace Najem\Tests\Api\Social;

/**
 * @author  Abed Halawi <abed.halawi@vinelab.com>
 */

use PHPUnit_Framework_TestCase as TestCase;
use Mockery as M;

use Vinelab\Auth\Social\Profile as SocialProfile;

class ProfileTest extends TestCase {

    public function setUp()
    {
        $this->config = M::mock('Illuminate\Config\Repository');

        $this->profile = new SocialProfile;

        $this->fb_sample = (object) array(
            'id'         => 'some-id',
            'name'       => 'Mickey Mouse',
            'first_name' => 'Mouse',
            'last_name'  => 'Mouse',
            'email'      => 'mickey@mouse.dis',
            'birthday'   => '12/03/1928',
            'gender'     => 'male'
        );

        $this->hup_sample = new \stdClass;
        $this->hup_sample->identifier    = 'some-id';
        $this->hup_sample->profileURL    = 'http://my.profile.url';
        $this->hup_sample->webSiteURL    = 'http://my.website.url';
        $this->hup_sample->photoURL      = 'http://my.photo.url';
        $this->hup_sample->displayName   = 'ghabi';
        $this->hup_sample->description   = 'fat-ass mothafucker';
        $this->hup_sample->firstName     = 'Goofy';
        $this->hup_sample->lastName      = 'Troop';
        $this->hup_sample->gender        = 'male';
        $this->hup_sample->language      = 'en';
        $this->hup_sample->age           = '81';
        $this->hup_sample->birthYear     = '1932';
        $this->hup_sample->birthMonth    = '4';
        $this->hup_sample->birthDay      = '16';
        $this->hup_sample->email         = '';
        $this->hup_sample->emailVerified = true;
        $this->hup_sample->phone         = '';
        $this->hup_sample->addres        = '';
        $this->hup_sample->region        = '';
        $this->hup_sample->city          = '';
        $this->hup_sample->zip           = '';

    }

    public function test_fb_instantiation()
    {
        $this->config->shouldReceive('get')->once()->andReturn('http://avatar.url/%s');

        $profile = $this->profile->instantiate($this->fb_sample, 'facebook');

        $this->assertInstanceOf('Vinelab\Auth\Contracts\ProfileInterface', $profile);
        $this->assertInstanceOf('Vinelab\Auth\Social\Profile', $profile);
    }

    public function test_assigns_avatar()
    {
        $this->config->shouldReceive('get')->once()->andReturn('http://avatar.url/%s');

        $fb = $this->profile->instantiate($this->fb_sample, 'facebook');

        $this->assertNotNull($fb->avatar);
    }

    public function test_generates_birthday()
    {
        $this->config->shouldReceive('get')->once()->andReturn('http://avatar.url/%s');

        $fb = $this->profile->instantiate($this->fb_sample, 'facebook');

        $this->assertNotNull($fb->birthday);
    }

    public function test_ability_to_update_profile_email()
    {
        $this->config->shouldReceive('get')->once()->andReturn('http://avatar.url/%s');

        $fb = $this->profile->instantiate($this->fb_sample, 'facebook');

        $fb->email = 'some@mail.com';

        $info = $fb->info();

        $this->assertEquals('some@mail.com', $fb->email);
        $this->assertEquals('some@mail.com', $info['email']);
    }

    public function test_fn_provider()
    {
        $this->config->shouldReceive('get')->once();

        $fb = $this->profile->instantiate($this->fb_sample, 'facebook');

        $this->assertEquals('facebook', $fb->provider());
    }
}
