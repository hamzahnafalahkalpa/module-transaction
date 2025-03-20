<?php

namespace Zahzah\ModuleTransaction\Schemas;

use Zahzah\LaravelSupport\Supports\PackageManagement;
use Zahzah\ModuleTransaction\Contracts\PaymentHistoryDetail as ContractsPaymentHistoryDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PaymentHistoryDetail extends PaymentDetail implements ContractsPaymentHistoryDetail{
    //FOR BILLING HISTORICAL PAYMENT
}