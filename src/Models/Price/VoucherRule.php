<?php

namespace Hanafalah\ModuleTransaction\Models\Price;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModuleTransaction\Enums\Voucher\Status;
use Hanafalah\ModuleTransaction\Resources\Voucher\{ViewVoucher, ShowVoucher};
use Hanafalah\ModuleTransaction\Resources\VoucherRule\{ViewVoucherRule, ShowVoucherRule};

class VoucherRule extends BaseModel
{
    use HasUlids, HasProps;

    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $primaryKey = 'id';
    protected $list       = [
        'id',
        'name',
        'voucher_id',
        'condition',
        'props'
    ];

    protected $casts = [
        'name' => 'string'
    ];

    public function toViewApi()
    {
        return new ViewVoucherRule($this);
    }

    public function toShowApi()
    {
        return new ShowVoucherRule($this);
    }

    public function voucher()
    {
        return $this->belongsToModel('Voucher');
    }
}
