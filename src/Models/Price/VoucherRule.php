<?php

namespace Zahzah\ModuleTransaction\Models\Price;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Zahzah\LaravelHasProps\Concerns\HasProps;
use Zahzah\LaravelSupport\Models\BaseModel;
use Zahzah\ModuleTransaction\Enums\Voucher\Status;
use Zahzah\ModuleTransaction\Resources\Voucher\{ViewVoucher, ShowVoucher};
use Zahzah\ModuleTransaction\Resources\VoucherRule\{ViewVoucherRule,ShowVoucherRule};

class VoucherRule extends BaseModel{
    use HasUlids, HasProps;

    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $primaryKey = 'id';
    protected $list       = [
        'id', 'name', 'voucher_id', 'condition', 'props'
    ];

    protected $casts = [
        'name' => 'string'
    ];

    public function toViewApi(){
        return new ViewVoucherRule($this);
    }

    public function toShowApi(){
        return new ShowVoucherRule($this);
    }

    public function voucher(){
        return $this->belongsToModel('Voucher');
    }
}
