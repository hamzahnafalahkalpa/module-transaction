<?php

use App\Models\User;
use Hanafalah\ModuleService\Models\Service;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Projects\Klinik\Models\TariffComponent\TariffComponent;
use Hanafalah\ModuleTransaction\{
    Models\Price\PriceComponent,
};

return new class extends Migration
{
    use Hanafalah\LaravelSupport\Concerns\NowYouSeeMe;

    private $__table;

    public function __construct()
    {
        $this->__table = app(config('database.models.PriceComponent', PriceComponent::class));
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
                $tariffComponent = app(config('database.models.TariffComponent', TariffComponent::class));
                $service = app(config('database.models.Service', Service::class));

                $table->id();
                $table->foreignIdFor($service::class)->nullable(true)
                    ->index()->constrained()->restrictOnDelete()->cascadeOnUpdate();

                $table->string('model_type', 50)->nullable(false);
                $table->string('model_id', 36)->nullable(false);

                $table->foreignIdFor($tariffComponent::class)->nullable(false)
                    ->index()->constrained()->cascadeOnUpdate()->restrictOnDelete();
                $table->unsignedBigInteger('price')->nullable(false)->default(0);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['model_type', 'model_id'], 'pc_model');
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
