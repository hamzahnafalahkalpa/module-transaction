<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleTransaction\{
    Models\Payment\PaymentDetail
};
use Hanafalah\ModuleTransaction\Models\Payment\PaymentHistory;
use Hanafalah\ModuleTransaction\Models\Payment\PaymentSummary;
use Hanafalah\ModuleTransaction\Models\Transaction\TransactionItem;

return new class extends Migration
{
    use Hanafalah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
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
        if (!Schema::hasColumn($table_name, 'refund')) {
            Schema::create($table_name, function (Blueprint $table) {
                $table->unsignedInteger('refund')->nullable()->default(0);
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
