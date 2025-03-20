<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleTransaction\Models\Transaction\Billing;
use Hanafalah\ModuleTransaction\Models\Transaction\Refund;

return new class extends Migration
{
    use Hanafalah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Refund', Refund::class));
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
                $billing = app(config('database.modales.Billing', Billing::class));

                $table->ulid('id')->primary();
                $table->foreignIdFor($billing::class)->nullable()
                    ->cascadeOnUpdate()->restrictOnDelete();
                $table->string('author_id', 50)->nullable();
                $table->string('author_type', 36)->nullable();
                $table->datetime('withdrawal_at')->nullable();
                $table->integer('total')->nullable();
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();
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
