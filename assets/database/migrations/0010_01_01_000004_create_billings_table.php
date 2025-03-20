<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleTransaction\{
    Models\Transaction\Billing,
};
use Hanafalah\ModuleTransaction\Enums\Billing\Status;
use Hanafalah\ModuleTransaction\Models\Transaction\Invoice;
use Hanafalah\ModuleTransaction\Models\Transaction\Transaction;

return new class extends Migration
{
    use Hanafalah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Billing', Billing::class));
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
                $invoice = app(config('database.models.Invoice', Invoice::class));

                $table->ulid('id')->primary();
                $table->string('uuid', 36)->nullable();
                $table->string('billing_code', 100)->nullable();
                $table->foreignIdFor($transaction::class)->nullable()->index()
                    ->constrained()->cascadeOnUpdate()->restrictOnDelete();
                $table->string('author_type', 50)->nullable(false);
                $table->string('author_id', 36)->nullable(false);
                $table->string('cashier_type', 50)->nullable(false);
                $table->string('cashier_id', 36)->nullable(false);
                $table->string('payment_method', 36)->nullable(true);
                $table->string('status', 50)->default(Status::DRAFT->value)->nullable(false);
                $table->timestamp('reported_at')->nullable();
                $table->foreignIdFor($invoice::class)->nullable()->index()
                    ->constrained()->cascadeOnUpdate()->nullOnDelete();
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['author_type', 'author_id']);
                $table->index(['cashier_type', 'cashier_id']);
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
