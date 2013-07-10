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
			$table->string('id', 30)->primary;
			$table->string('network');
			$table->string('account_id')->unique();
			$table->string('user_id');
			$table->text('access_token');

			$table->timestamps();

			$table->index('account_id');
			$table->foreign('user_id')
					->references('id')->on('users')
					->onDelete('cascade')
					->onUpdate('cascade');
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