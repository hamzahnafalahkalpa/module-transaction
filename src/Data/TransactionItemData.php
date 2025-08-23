<?php

namespace Hanafalah\ModuleTransaction\Data;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModuleTransaction\Contracts\Data\TransactionItemData as DataTransactionItemData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class TransactionItemData extends Data implements DataTransactionItemData{
    use HasRequestData;

    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('parent_id')]
    #[MapName('parent_id')]
    public mixed $parent_id = null;
    
    #[MapInputName('transaction_id')]
    #[MapName('transaction_id')]
    public mixed $transaction_id = null;
    
    #[MapInputName('item_type')]
    #[MapName('item_type')]
    public ?string $item_type = null;

    #[MapInputName('item_id')]
    #[MapName('item_id')]
    public mixed $item_id = null;

    #[MapInputName('item_name')]
    #[MapName('item_name')]
    public ?string $item_name = null;

    #[MapInputName('payment_detail')]
    #[MapName('payment_detail')]
    public array|object|null $payment_detail = null;

    public static function after(self $data):self{
        $new = static::new();
        
        if (is_array($data->payment_detail)){
            $payment_detail_name = config('module-transaction.payment_detail');
            if (isset($payment_detail_name)){
                $data->payment_detail = $new->requestDTO(
                    config('app.contracts.'.$payment_detail_name.'Data'),
                    $data->payment_detail
                );
            }else{
                $data->payment_detail = null;
            }
        }
        return $data;
    }

}