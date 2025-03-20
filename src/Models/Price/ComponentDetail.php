<?php

namespace Hanafalah\ModuleTransaction\Models\Price;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\ModuleTransaction\Resources\ComponentDetail\ViewComponentDetail;
use Hanafalah\ModuleTransaction\Resources\TariffComponent\ShowComponentDetail;

class ComponentDetail extends BaseModel
{

    use HasUlids, HasProps;

    public $timestamps    = false;
    public $incrementing  = false;
    protected $primaryKey = 'id';
    protected $keyType    = 'string';

    protected $fillable = ['id', 'reference_type', 'reference_id', 'flag'];

    protected $casts = [
        'name' => 'string'
    ];

    public function toViewApi()
    {
        return new ViewComponentDetail($this);
    }

    public function toShowApi()
    {
        return new ShowComponentDetail($this);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
