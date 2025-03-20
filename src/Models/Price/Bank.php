<?php

namespace Hanafalah\ModuleTransaction\Models\Price;

use Hanafalah\LaravelSupport\Models\BaseModel;
use Hanafalah\ModuleTransaction\Enums\Bank\Status;
use Hanafalah\ModuleTransaction\Resources\Bank\{ViewBank, ShowBank};

class Bank extends BaseModel
{
    protected $list = ['id', 'name', 'account_number', 'account_name', 'status'];

    protected $casts = [
        'name'            => 'string',
        'account_name'    => 'string',
        'account_number'  => 'string'
    ];

    protected static function booted(): void
    {
        parent::booted();
        static::addGlobalScope('bank-status', function ($query) {
            $query->where('status', Status::ACTIVE->value);
        });
        static::creating(function ($query) {
            if (!isset($query->status)) $query->status = Status::ACTIVE->value;
        });
    }

    public function toViewApi()
    {
        return new ViewBank($this);
    }

    public function toShowApi()
    {
        return new ShowBank($this);
    }
}
