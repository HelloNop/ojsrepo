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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained()->onDelete('cascade');
            $table->foreignId('issue_id')->constrained()->onDelete('cascade');
            $table->text('title');
            $table->string('slug');
            $table->longText('abstract');
            $table->text('keywords');
            $table->string('source_url');
            $table->string('pdf_url');
            $table->string('published_date');
            $table->string('doi');
            $table->string('oai_id');
            $table->string('pages')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
