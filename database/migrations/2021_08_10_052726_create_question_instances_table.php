<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicator_id')->nullable()->constrained('indicators')->onDelete('set null');
            $table->json('question');
            $table->json('answer');
            $table->json('solution');
            $table->foreignId('generator_script_id')->nullable()->constrained('scripts')->onDelete('set null');
            $table->integer('initial_level')->nullable();
            $table->double('rating')->default(0);
            $table->integer('upvotes')->default(0);
            $table->integer('downvotes')->default(0);
            $table->integer('total_attempts')->default(0);
            $table->integer('correct_attempts')->default(0);
            $table->double('average_time_used')->nullable();
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('question_instances');
    }
}
