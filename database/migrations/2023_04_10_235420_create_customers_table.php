<?php

use App\Models\Address;
use App\Models\Gender;
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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('cpf', 11)->unique();
            $table->date('birthdate');
            $table->foreignIdFor(Address::class)
                ->constrained()
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->foreignIdFor(Gender::class)
                ->constrained()
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeignIdFor(Address::class);
            $table->dropForeignIdFor(Gender::class);
        });
        Schema::dropIfExists('customers');
    }
};
