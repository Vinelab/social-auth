<?php namespace Vinelab\Auth\Controllers;

use Response;
use Session;

Class ProfileController extends BaseController {

	public function basic()
	{
		Session::put('something', 'somewhere');
		return Response::json(['user'=>Session::get('something')]);
	}

	public function full()
	{

	}
}