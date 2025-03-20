<?php

namespace Hanafalah\ModuleTransaction\Models\Consument;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModuleTransaction\Concerns\HasConsumentInvoice;
use Hanafalah\ModuleTransaction\Resources\Consument\{
    ViewConsument,
    ShowConsument
};

class Consument extends BaseModel
{
    use HasUlids, HasProps, HasConsumentInvoice;

    public $incrementing  = false;
    protected $primaryKey = 'id';
    protected $keyType    = 'string';

    protected $list = [
        'id',
        'uuid',
        'name',
        'phone',
        'reference_id',
        'reference_type',
        'props'
    ];
    protected $show = [];

    protected $casts = [
        'name' => 'string'
    ];

    protected $getPropsQuery = [
        'name' => 'name'
    ];

    public function toViewApi()
    {
        return new ViewConsument($this);
    }

    public function toShowApi()
    {
        return new ShowConsument($this);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
