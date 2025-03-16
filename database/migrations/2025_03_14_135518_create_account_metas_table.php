<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_metas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');

            //Connector
            $table->unsignedInteger('model_id')->nullable();
            $table->string('model_type')->nullable();

            $table->string('key')->index();
            $table->string('type')->nullable()->default('meta')->index();
            $table->string('response')->default('ok')->index();

            $table->json('value')->nullable();
            $table->string('key_value')->index()->nullable();

            $table->date('date')->nullable();
            $table->time('time')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_metas');
    }
};
