<?php

namespace Zahzah\ModuleTransaction\Models\Payment;

class PaymentHistory extends PaymentSummary{
    protected $table = 'payment_summaries';

    protected static function booted(): void{
        parent::booted();
        static::addGlobalScope('splitBill',function($query){
            $splitBill = app(config('database.models.SplitBill'));
            $hasModel  = app(config('database.models.PaymentHistoryHasModel'));
            $query->whereIn('reference_type',[$splitBill->getMorphClass(),$hasModel->getMorphClass()]);
        });
    }

    public function splitBill(){return $this->morphTo('reference');}
    public function paymentHistoryDetails(){return $this->hasManyModel('PaymentHistoryDetail','payment_history_id');}
    public function paymentHistoryHasModel(){return $this->hasManyModel('PaymentHistoryHasModel','payment_history_id');}
    public function childHistoryRekursi(){return $this->hasManyModel('PaymentHistory','parent_id')->with(["childHistoryRekursi","paymentHistoryDetails.transactionItem"]);}
}
