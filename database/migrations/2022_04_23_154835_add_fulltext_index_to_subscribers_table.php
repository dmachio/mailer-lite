<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddFulltextIndexToSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE subscribers ADD FULLTEXT name_search(name)');
        DB::statement('ALTER TABLE subscribers ADD FULLTEXT email_search(email)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE subscribers DROP INDEX name_search');
        DB::statement('ALTER TABLE subscribers DROP INDEX email_search');
    }
}
