<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            // Identitas
            $table->string('name')->default('Nama Anda');
            $table->string('headline')->nullable();      // contoh: Full-Stack Web Developer
            $table->string('role')->nullable();          // contoh: Mahasiswa / Freelancer
            $table->string('avatar')->nullable();        // path foto profil
            $table->text('short_bio')->nullable();       // untuk hero / meta
            $table->longText('about')->nullable();       // paragraf "Tentang Saya"
            $table->string('location')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            // Hero
            $table->string('hero_greeting')->nullable();       // "Halo, saya"
            $table->string('hero_tagline')->nullable();        // teks animasi typing
            $table->string('hero_cta_label')->nullable();
            $table->string('hero_cta_url')->nullable();

            // Status
            $table->boolean('is_available')->default(true);
            $table->string('availability_text')->nullable();

            // File
            $table->string('cv_file')->nullable();

            // Sosial media (json: [{"platform":"github","label":"GitHub","url":"...","icon":"gh"}])
            $table->json('social_links')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
