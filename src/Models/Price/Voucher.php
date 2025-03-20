<?php

namespace Hanafalah\ModuleTransaction\Models\Price;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hanafalah\LaravelHasProps\Concerns\HasProps;
use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModuleTransaction\Enums\Voucher\Status;
use Hanafalah\ModuleTransaction\Resources\Voucher\{ViewVoucher, ShowVoucher};

class Voucher extends BaseModel
{
    use HasUlids, HasProps, SoftDeletes;

    const STATUS_ACTIVE             = "ACTIVE";
    const STATUS_INACTIVE           = "INACTIVE";
    const BENEFIT_FORMAT_PERCENTAGE = 'PERCENTAGE';
    const BENEFIT_FORMAT_AMOUNT     = 'AMOUNT';

    const BENEFIT_TYPE_MARKDOWN     = 'MARKDOWN';
    const BENEFIT_TYPE_MARKUP       = 'MARKUP';
    const BENEFIT_TYPE_DISCOUNT     = 'DISCOUNT';
    const BENEFIT_TYPE_REPLACEMENT  = 'REPLACEMENT';

    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $primaryKey = 'id';
    protected $list       = [
        'id',
        'name',
        'status',
        'benefit_target',
        'benefit_format',
        'benefit_value',
        'benefit_type_value',
        'max_benefit_value',
        'is_auto_implement',
        'author_type',
        'author_id',
        'props'
    ];

    protected $casts = [
        'name'            => 'string',
        'benefit_target'  => 'string',
        'benefit_format'  => 'string'
    ];

    protected static function booted(): void
    {
        parent::booted();
        static::addGlobalScope('voucher-status', function ($query) {
            $query->where('status', Status::ACTIVE->value);
        });
        static::creating(function ($query) {
            if (!isset($query->status)) $query->status = Status::ACTIVE->value;
        });
    }

    public function toViewApi()
    {
        return new ViewVoucher($this);
    }

    public function toShowApi()
    {
        return new ShowVoucher($this);
    }

    public function voucherRules()
    {
        return $this->hasManyModel("VoucherRule");
    }
    public function author()
    {
        return $this->morphTo();
    }
    public function voucherTransaction()
    {
        return $this->hasOneModel('VoucherTransaction');
    }
    public function voucherTransactions()
    {
        return $this->hasManyModel('VoucherTransaction');
    }
}
