<?php namespace Vinelab\Auth\Models\Observers;

use Vinelab\Assistant\Generator;

class EloquentObserver {

	public function creating($model)
	{
		$model->id = (new Generator)->uid();
	}
}