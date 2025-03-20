<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Zahzah\ModuleTransaction\{
    Models\Payment\PaymentDetail
};
use Zahzah\ModuleTransaction\Models\Payment\PaymentHistory;
use Zahzah\ModuleTransaction\Models\Payment\PaymentSummary;
use Zahzah\ModuleTransaction\Models\Transaction\TransactionItem;

return new class extends Migration
{
   use Zahzah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct(){
        $this->__table = app(config('database.models.PaymentDetail', PaymentDetail::class));
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $table_name = $this->__table->getTable();
        if (!$this->isTableExists()){
            Schema::create($table_name, function (Blueprint $table) {
                $payment_summary        = app(config('database.models.PaymentSummary',PaymentSummary::class));
                $transaction_item       = app(config('database.models.TransactionItem',TransactionItem::class));
                $payment_history        = app(config('database.models.PaymentHistory',PaymentHistory::class));
                $payment_detail         = app(config('database.models.PaymentDetail',PaymentDetail::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($payment_summary::class)->nullable()->index()
                      ->constrained()->cascadeOnUpdate()->restrictOnDelete();

                $table->foreignIdFor($payment_history::class)->nullable()->index()
                      ->constrained()->cascadeOnUpdate()->restrictOnDelete();

                $table->foreignIdFor($transaction_item::class)->nullable()->index()
                      ->constrained()->cascadeOnUpdate()->restrictOnDelete();

                $table->foreignIdFor($payment_detail::class)->nullable()->index()
                      ->constrained()->cascadeOnUpdate()->restrictOnDelete();

                $table->integer('amount')->nullable()->default(0);
                $table->integer('qty')->nullable()->default(0);
                $table->integer('cogs')->nullable()->default(0);
                $table->integer('debt')->nullable()->default(0);
                $table->integer('price')->nullable()->default(0);
                $table->integer('paid')->nullable()->default(0);
                $table->integer('discount')->nullable()->default(0);
                $table->integer('tax')->nullable()->default(0);
                $table->integer('additional')->nullable()->default(0);
                $table->boolean('is_loan')->nullable()->default(0);

                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::table($table_name,function (Blueprint $table){
                $table->foreignIdFor($this->__table::class,'parent_id')
                      ->nullable()->after('id')
                      ->index()->constrained()
                      ->cascadeOnUpdate()->restrictOnDelete();
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
