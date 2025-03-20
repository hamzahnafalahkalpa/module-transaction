<?php

namespace Zahzah\ModuleTransaction\Concerns;

trait HasVoucher{
    public function vouchers(){
        return $this->belongsToManyModel('Voucher','ModelHasVoucher','model_id','voucher_id')
                    ->where('model_type',$this->getMorphClass());
    }

    public function modelHasVoucher(){return $this->morphOneModel('ModelHasVoucher','model');}
}
