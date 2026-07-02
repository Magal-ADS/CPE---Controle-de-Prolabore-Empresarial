<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('attachment_name')->nullable();
            $table->string('attachment_mime')->nullable();
            $table->unsignedBigInteger('attachment_size')->nullable();
        });

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE transactions ADD attachment_content MEDIUMBLOB NULL');

            return;
        }

        Schema::table('transactions', function (Blueprint $table) {
            $table->binary('attachment_content')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'attachment_name',
                'attachment_mime',
                'attachment_size',
                'attachment_content',
            ]);
        });
    }
};
