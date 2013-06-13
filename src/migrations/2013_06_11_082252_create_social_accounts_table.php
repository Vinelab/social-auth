<?php

use Illuminate\Database\Migrations\Migration;

class CreateSocialAccountsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('social_accounts', function($table){
			$table->increments('id');
			$table->string('network');
			$table->string('account_id')->unique();
			$table->integer('user_id')->unsigned();
			$table->text('access_token');

			$table->timestamps();

			$table->index('account_id');
			$table->foreign('user_id')->references('id')->on('users');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('social_accounts');
	}

}