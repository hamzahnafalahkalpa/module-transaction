<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Zahzah\ModuleTransaction\Models\Transaction\TransactionHasConsument;
use Zahzah\ModuleTransaction\Models\Consument\Consument;
use Zahzah\ModuleTransaction\Models\Transaction\Transaction;

return new class extends Migration
{
   use Zahzah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.TransactionHasConsument', TransactionHasConsument::class));
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
                $transaction = app(config('database.models.Transaction', Transaction::class));
                $consument   = app(config('database.models.Consument', Consument::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($transaction::class)->nullable()->index()
                    ->constrained()->cascadeOnUpdate()->restrictOnDelete();
                $table->foreignIdFor($consument::class)->nullable()->index()
                    ->constrained()->cascadeOnUpdate()->restrictOnDelete();
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
