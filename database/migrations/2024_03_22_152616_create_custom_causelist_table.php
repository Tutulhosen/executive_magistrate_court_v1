<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomCauselistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_causelist', function (Blueprint $table) {
            $table->id();
            $table->string('causeTitle');
            $table->string('applicantName');
            $table->string('caseNo')->unique();
            $table->string('applicantMobile', 11);
            $table->string('defaulterName');
            $table->string('lawSection');
            $table->string('court_id');
            $table->date('caseDate');
            $table->string('dis_section');
            $table->string('div_section');
            $table->string('upa_section');
            $table->date('next_date');
            $table->date('lastorderDate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_causelist');
    }
}