<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleTransaction\Models\Transaction\Refund;
use Hanafalah\ModuleTransaction\Models\Transaction\RefundItem;

return new class extends Migration
{
    use Hanafalah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.RefundItem', RefundItem::class));
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $table_name = $this->__table->getTable();
        if (!$this->isTableExists()) {
            Schema::create($table_name, function (Blueprint $table) {
                $refund = app(config('database.models.Refund', Refund::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($refund::class)->nullable()->index()
                    ->constrained()->cascadeOnUpdate()->nullOnDelete();
                $table->string('item_id', 50)->nullable(true);
                $table->string('item_type', 36)->nullable(true);
                $table->json('props')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->__table->getTable());
    }
};
