<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Zahzah\ModuleTransaction\{
    Models\Payment\PaymentHistoryHasModel,
};
use Zahzah\ModuleTransaction\Models\Payment\PaymentHistory;

return new class extends Migration
{
   use Zahzah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct(){
        $this->__table = app(config('database.models.PaymentHistoryHasModel', PaymentHistoryHasModel::class));
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
                $payment_history = app(config('database.models.PaymentHistory',PaymentHistory::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($payment_history::class,'payment_history_id')
                      ->index('ph_phhm')->constrained()
                      ->cascadeOnUpdate()->restrictOnDelete();
                $table->string('model_type',50)->nullable(false);
                $table->string('model_id',36)->nullable(false);
                $table->timestamps();

                $table->index(['model_type','model_id'],'model_phhm');
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
