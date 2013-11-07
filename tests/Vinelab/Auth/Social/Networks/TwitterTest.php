<?php namespace Vinelab\Auth\Tests;

use PHPUnit_Framework_TestaCase as TestaCase;
use Mocker as M;

use Vinelab\Auth\Social\Networks\Twitter as Twitter;

class TwitterTest extends TestCase {

    public function setUp()
    {
        $this->samplesDir = './tests/samples';

        $this->settings = [

        ];
    }

}