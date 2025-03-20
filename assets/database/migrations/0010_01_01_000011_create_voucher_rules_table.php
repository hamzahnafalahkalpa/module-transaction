<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use Hanafalah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.VoucherRule', \Hanafalah\ModuleTransaction\Models\Price\VoucherRule::class));
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
                $voucher = app(config('database.models.Voucher', \Projects\Klinik\Models\Transaction\Voucher::class));

                $table->ulid('id')->primary();
                $table->string('name')->nullable(false);
                $table->foreignIdFor($voucher::class)->nullable()->index()
                    ->constrained()->cascadeOnUpdate()->nullOnDelete();
                $table->string('condition', 36)->nullable(true);
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
