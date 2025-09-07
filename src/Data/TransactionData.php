<?php

namespace Hanafalah\ModuleTransaction\Data;

use Carbon\Carbon;
use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePayment\Contracts\Data\ConsumentData;
use Hanafalah\ModulePayment\Contracts\Data\PaymentDetailData;
use Hanafalah\ModuleTransaction\Contracts\Data\TransactionData as DataTransactionData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class TransactionData extends Data implements DataTransactionData{
    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('parent_id')]
    #[MapName('parent_id')]
    public mixed $parent_id = null;

    #[MapInputName('reference_type')]
    #[MapName('reference_type')]
    public ?string $reference_type = null;

    #[MapInputName('reference_id')]
    #[MapName('reference_id')]
    public mixed $reference_id = null;

    #[MapInputName('reference')]
    #[MapName('reference')]
    public array|object|null $reference = null;

    #[MapInputName('reference_model')]
    #[MapName('reference_model')]
    public ?object $reference_model = null;

    #[MapInputName('consument')]
    #[MapName('consument')]
    public ?ConsumentData $consument = null;

    #[MapInputName('reported_at')]
    #[MapName('reported_at')]
    public ?Carbon $reported_at = null;

    #[MapInputName('journal_reported_at')]
    #[MapName('journal_reported_at')]
    public ?Carbon $journal_reported_at = null;

    #[MapInputName('transaction_items')]
    #[MapName('transaction_items')]
    #[DataCollectionOf(TransactionItemData::class)]
    public ?array $transaction_items = null;

    #[MapInputName('payment_details')]
    #[MapName('payment_details')]
    public ?array $payment_details = null;

    #[MapInputName('props')]
    #[MapName('props')]
    public ?array $props = null;

    public static function before(array &$attributes){
        $new = static::new();
        if (!isset($attributes['reference'])){
            if (!isset($attributes['reference_type']) && !isset($attributes['reference_id'])){
                if (isset($attributes['id'])){
                    $transaction_model   = $new->TransactionModel()->with('reference')->findOrFail($attributes['id']);
                    $attributes['reference_id']   = $reference['id'] = $transaction_model->reference_id;
                    $attributes['reference_type'] = $transaction_model->reference_type;
                }else{
                    $config_keys = array_keys(config('module-transaction.transaction_types'));
                    $keys        = array_intersect(array_keys($attributes),$config_keys);
                    $key         = array_shift($keys);
                    $attributes['reference_type'] ??=  $key;
                }
            }
            $attributes['reference_type'] = Str::studly($attributes['reference_type']);
        }
    }

    public static function after(self $data): self{
        $new = static::new();
        if (isset($data->reference)){
            $reference = &$data->reference;
            $data->reference_type = Str::studly($data->reference_type);
            $reference = self::transformToData($data->reference_type, $reference);
        }

        if (is_array($data->payment_details)){
            $payment_detail_name = config('module-transaction.payment_detail');
            if (isset($payment_detail_name)){
                foreach ($data->payment_details as &$payment_detail) {
                    $payment_detail = $new->requestDTO(
                        config('app.contracts.'.$payment_detail_name.'Data'),
                        $payment_detail
                    );
                }
            }else{
                $data->payment_details = null;
            }
        }
        return $data;
    }

    private static function transformToData(string $entity,array $attributes){
        $new = static::new();
        return $new->requestDTO(config('app.contracts.'.$entity.'Data'),$attributes);
    }
}