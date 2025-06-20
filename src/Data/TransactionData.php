<?php

namespace Hanafalah\ModuleTransaction\Data;

use Carbon\Carbon;
use Hanafalah\LaravelSupport\Supports\Data;
use Hanafalah\ModulePayment\Contracts\Data\ConsumentData;
use Hanafalah\ModulePayment\Contracts\Data\PaymentDetailData;
use Hanafalah\ModuleTransaction\Contracts\Data\TransactionData as DataTransactionData;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;

class TransactionData extends Data implements DataTransactionData{
    #[MapInputName('id')]
    #[MapName('id')]
    public mixed $id = null;

    #[MapInputName('reference_type')]
    #[MapName('reference_type')]
    public ?string $reference_type = null;

    #[MapInputName('reference_id')]
    #[MapName('reference_id')]
    public mixed $reference_id = null;

    #[MapInputName('consument')]
    #[MapName('consument')]
    public ?ConsumentData $consument = null;

    #[MapInputName('reported_at')]
    #[MapName('reported_at')]
    public ?Carbon $reported_at = null;

    #[MapInputName('journal_reported_at')]
    #[MapName('journal_reported_at')]
    public ?Carbon $journal_reported_at = null;

    #[MapInputName('payment_detail')]
    #[MapName('payment_detail')]
    public ?PaymentDetailData $payment_detail = null;
}