<?php

namespace Hanafalah\ModuleTransaction\Models\Price;

use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModuleTransaction\Enums\TariffComponent\Flag;
use Hanafalah\ModuleTransaction\Resources\TariffComponent\ShowTariffComponent;
use Hanafalah\ModuleTransaction\Resources\TariffComponent\ViewTariffComponent;

class TariffComponent extends BaseModel
{
    protected $fillable = ['id', 'name'];

    protected $casts = [
        'name' => 'string'
    ];

    public function toViewApi()
    {
        return new ViewTariffComponent($this);
    }

    public function toShowApi()
    {
        return new ShowTariffComponent($this);
    }

    public function getFlags()
    {
        return array_column(Flag::cases(), 'value');
    }

    public function componentDetails()
    {
        return $this->morphManyModel('ComponentDetail', 'reference');
    }
}
