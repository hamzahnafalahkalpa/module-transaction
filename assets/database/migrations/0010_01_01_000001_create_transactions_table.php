<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleTransaction\{
    Models\Transaction\Transaction,
    Enums\Transaction\Status
};
use Hanafalah\ModuleTransaction\Models\Transaction\Invoice;

return new class extends Migration
{
    use Hanafalah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Transaction', Transaction::class));
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $table_name = $this->__table->getTble();
        if (!$this->isTableExists()) {
            Schema::create($table_name, function (Blueprint $table) {
                $user = app(config('database.models.User', User::class));
                $invoice = app(config('database.models.Invoice', Invoice::class));

                $table->ulid('id')->primary();
                $table->string('uuid', 36)->nullable(false);
                $table->string('transaction_code', 100)->nullable(false);
                $table->string('reference_type', 50)->nullable(false);
                $table->string('reference_id', 36)->nullable(false);
                $table->unsignedTinyInteger('status')->default(Status::DRAFT->value)->nullable(false);
                $table->foreignIdFor($user::class)->nullable()->index()
                    ->constrained()->cascadeOnUpdate()->restrictOnDelete();
                $table->foreignIdFor($invoice::class)->nullable()->index()
                    ->constrained()->cascadeOnUpdate()->nullOnDelete();
                $table->json('props')->nullable();
                $table->timestamp('reported_at')->nullable();
                $table->timestamp('canceled_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['reference_type', 'reference_id']);
            });

            Schema::table($table_name, function (Blueprint $table) {
                $table->foreignIdFor($this->__table::class, 'parent_id')
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
