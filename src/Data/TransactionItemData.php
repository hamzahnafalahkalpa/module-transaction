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

    #[MapInputName('transaction_model')]
    #[MapName('transaction_model')]
    public ?object $transaction_model = null;
    
    #[MapInputName('reference_type')]
    #[MapName('reference_type')]
    public ?string $reference_type = null;

    #[MapInputName('reference_id')]
    #[MapName('reference_id')]
    public mixed $reference_id = null;

    #[MapInputName('reference_model')]
    #[MapName('reference_model')]
    public ?object $reference_model = null;

    #[MapInputName('item_type')]
    #[MapName('item_type')]
    public ?string $item_type = null;

    #[MapInputName('item_id')]
    #[MapName('item_id')]
    public mixed $item_id = null;

    #[MapInputName('item')]
    #[MapName('item_')]
    public array|object|null $item = null;

    #[MapInputName('name')]
    #[MapName('name')]
    public ?string $name = null;

    #[MapInputName('payment_detail')]
    #[MapName('payment_detail')]
    public array|object|null $payment_detail = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;

    public static function before(array &$attributes){
        $new = self::new();
        if (isset($attributes['item']) && isset($attributes['item_type'])){
            $attributes['item'] = $new->requestDTO(config('app.contracts.'.$attributes['item_type'].'Data'), $attributes['item']);
        }
    }

    public static function after(self $data):self{
        $new = static::new();
        
        if (!isset($data->name) && isset($data->item_id)){
            $entity = $new->{$data->item_type.'Model'}()->findOrFail($data->item_id);
            $data->name ??= $entity->name ?? null;
            // $data->name ??= $entity->exam->name ?? null;
        }

        if (isset($data->item) && isset($data->item->name)){
            $data->name ??= $data->item->name;
        }
        
        if (isset($data->reference_model)){
            $reference_model = $data->reference_model;
            $data->reference_type ??= $reference_model->getMorphClass();
            $data->reference_id ??= $reference_model->getKey();
        }

        if (is_array($data->payment_detail)){
            $payment_detail_name = config('module-transaction.payment_detail');
            if (isset($payment_detail_name)){
                $data->payment_detail['name'] = $data->name;
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