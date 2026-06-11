<?php

use App\Models\Brochure;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brochure_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brochure_id')->constrained('brochures')->cascadeOnDelete();
            $table->string('image');
            $table->string('thumbnail');
            $table->integer('order')->default(0);
            $table->boolean('is_cover')->default(false);
            $table->timestamps();
        });

        // Migrate existing preview_image into the new table as the cover.
        // withoutGlobalScopes() so this keeps working after the model later
        // gains the SoftDeletes scope (deleted_at doesn't exist yet here).
        Brochure::withoutGlobalScopes()->whereNotNull('preview_image')->each(function (Brochure $b): void {
            $b->images()->create([
                'image' => $b->preview_image,
                'thumbnail' => $b->preview_image,
                'order' => 0,
                'is_cover' => true,
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brochure_images');
    }
};
