<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Hanafalah\ModuleTransaction\Models\Transaction\Invoice;

return new class extends Migration
{
    use Hanafalah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.Invoice', Invoice::class));
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
                $table->ulid('id')->primary();
                $table->string('invoice_code')->nullable();
                $table->string('author_id')->nullable();
                $table->string('author_type')->nullable();
                $table->string('consument_id')->nullable();
                $table->string('consument_type')->nullable();
                $table->timestamp('generated_at')->nullable()->comment('invoice generated');
                $table->timestamp('billing_at')->nullable()->comment('billing to client');
                $table->timestamp('paid_at')->nullable()->comment('Paid at');
                $table->json('props')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['author_id', 'author_type'], 'author_ref');
                $table->index(['consument_id', 'consument_type'], 'consument_at');
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
