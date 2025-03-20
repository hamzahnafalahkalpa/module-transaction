<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Zahzah\ModuleTransaction\Models\Transaction\Billing;
use Zahzah\ModuleTransaction\Models\Transaction\Invoice;
use Zahzah\ModuleTransaction\Models\Transaction\SplitBill;

return new class extends Migration
{
   use Zahzah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct(){
        $this->__table = app(config('database.models.SplitBill', SplitBill::class));
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
                $billing = app(config('database.models.Billing', Billing::class));
                $invoice = app(config('database.models.Invoice', Invoice::class));

                $table->ulid('id')->primary();
                $table->string('payment_method',36)->nullable(true);
                $table->foreignIdFor($billing::class)->nullable()->index()
                      ->constrained()->cascadeOnUpdate()->nullOnDelete();
                $table->foreignIdFor($invoice::class)->nullable()->index()
                      ->constrained()->cascadeOnUpdate()->nullOnDelete();
                $table->string('payer_id', 50)->nullable(true);
                $table->string('payer_type', 36)->nullable(true);
                $table->unsignedBigInteger('total_paid')->nullable(false)->default(0);
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['payer_id','payer_type'],'payer_split');
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
