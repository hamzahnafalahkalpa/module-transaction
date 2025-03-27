<?php

namespace Hanafalah\ModuleTransaction\Data;

use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModuleTransaction\Data\PaymentDetailData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class TransactionItemData extends Data{
    public function __construct(
        #[MapInputName('id')]
        #[MapName('id')]
        public mixed $id = null,

        #[MapInputName('parent_id')]
        #[MapName('parent_id')]
        public mixed $parent_id = null,
    
        #[MapInputName('transaction_id')]
        #[MapName('transaction_id')]
        public string $transaction_id,
    
        #[MapInputName('item_type')]
        #[MapName('item_type')]
        public ?string $item_type = null,

        #[MapInputName('item_id')]
        #[MapName('item_id')]
        public mixed $item_id = null,

        #[MapInputName('item_name')]
        #[MapName('item_name')]
        public ?string $item_name = null,

        #[MapInputName('payment_detail')]
        #[MapName('payment_detail')]
        public ?PaymentDetailData $payment_detail = null
    ){}
}