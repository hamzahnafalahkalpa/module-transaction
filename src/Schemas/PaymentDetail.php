<?php

namespace Zahzah\ModuleTransaction\Schemas;

use Zahzah\LaravelSupport\Supports\PackageManagement;
use Zahzah\ModuleTransaction\Contracts\PaymentDetail as ContractsPaymentDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Zahzah\ModuleTransaction\Resources\PaymentDetail\{
    ShowPaymentDetail, ViewPaymentDetail
};

class PaymentDetail extends PackageManagement implements ContractsPaymentDetail{
    protected array $__guard   = ['id','payment_summary_id']; 
    protected array $__add     = ['amount','debt','tax','discount','additional'];
    protected string $__entity = 'PaymentDetail';
    public static $payment_detail_model;

    protected array $__resources = [
        'view' => ViewPaymentDetail::class,
        'show' => ShowPaymentDetail::class    
    ];

    public function prepareStorePaymentDetail(? array $attributes = null): Model {
        $attributes ??= request()->all();

        if (isset($attributes['id'])){
            $guard = ['id' => $attributes['id']];
        }else{
            $guard = [
                'parent_id'           => $attributes['parent_id'] ?? null,
                'payment_summary_id'  => $attributes['payment_summary_id'],
                'transaction_item_id' => $attributes['transaction_item_id']
            ];
        }

        $price      = $attributes['price'] ?? 0;
        $qty        = $attributes['qty'] ?? 1;
        $additional = $attributes['additional'] ?? 0;
        $tax        = $attributes['tax'] ?? 0;
        $amount     = $attributes['amount'] ?? (($price * $qty) + $additional + $tax);
        $debt       = $attributes['debt'] ?? $amount;
        $payment_detail = $this->PaymentDetailModel()->firstOrCreate($guard,[
            'is_loan'    => $attributes['is_loan'] ?? null,
            'qty'        => $attributes['qty'] ?? 1,
            'amount'     => $amount,
            'debt'       => $debt,
            'price'      => $price,
            'paid'       => $attributes['paid'] ?? 0,
            'cogs'       => $attributes['cogs'] ?? 0,
            'tax'        => $tax,
            'additional' => $additional
        ]);
        return static::$payment_detail_model = $payment_detail;
    }

    public function storePaymentDetail(): array{
        return $this->transaction(function(){
            return $this->ShowPaymentDetail($this->prepareStorePaymentDetail());
        });
    }

    public function getPaymentDetail(): mixed{
        return static::$payment_detail_model;
    }

    public function addOrChange(? array $attributes=[]): self{    
        $this->updateOrCreate($attributes);   
        return $this;
    }

    public function paymentDetail(mixed $conditionals = null): Builder{
        return $this->PaymentDetailModel()->conditionals($conditionals);
    }

    public function get(mixed $conditionals = null): Collection{
        return $this->paymentDetail($conditionals)->get();
    }

}