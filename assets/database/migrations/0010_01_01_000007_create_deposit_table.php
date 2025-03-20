<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Zahzah\ModuleTransaction\Models\Transaction\Deposit;

return new class extends Migration
{
   use Zahzah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct(){
        $this->__table = app(config('database.models.Deposit', Deposit::class));
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
                $table->ulid('id')->primary();
                $table->string('reference_type',50)->nullable(false);
                $table->string('reference_id',36)->nullable(false);
                $table->integer('total')->nullable()->default(0);
                $table->json('props')->nullable();
                $table->timestamp('reported_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['reference_type','reference_id'],'deposit_ref');
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
