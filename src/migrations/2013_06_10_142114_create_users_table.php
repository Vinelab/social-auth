<?php

use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($table){

			$table->increments('id');

			$table->string('guid')->unique();
			$table->string('name');
			$table->string('email')->unique();
			$table->string('password')->nullable();

			$table->timestamps();

			$table->index('guid');
			$table->index('email');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}