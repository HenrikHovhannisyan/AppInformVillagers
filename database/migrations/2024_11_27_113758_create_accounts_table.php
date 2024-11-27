<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('field_size')->nullable();
            $table->integer('tree_count')->nullable();
            $table->string('olive_type')->nullable();
            $table->integer('age_of_trees')->nullable();
            $table->string('location_of_field')->nullable();
            $table->integer('continuous_season_count')->nullable();
            $table->integer('total_harvested_olives')->nullable();
            $table->integer('total_gained_oil')->nullable();
            $table->date('account_creation_date')->nullable();
            $table->boolean('edit_request')->default(false);
            $table->boolean('admin_approval')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
