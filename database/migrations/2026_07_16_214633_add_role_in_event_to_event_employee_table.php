<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('event_employee', function (Blueprint $table) {
            $table->string('role_in_event')->nullable()->after('employee_id');
        });
    }

    public function down()
    {
        Schema::table('event_employee', function (Blueprint $table) {
            $table->dropColumn('role_in_event');
        });
    }
};