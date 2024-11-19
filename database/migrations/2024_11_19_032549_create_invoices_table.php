<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('folio')->nullable();
            $table->string('emisor');
            $table->string('receptor');
            $table->string('moneda');
            $table->decimal('total', 10, 2);
            $table->decimal('tipo_cambio', 10, 4);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
