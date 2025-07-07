<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Hanafalah\ModuleTransaction\Contracts\Schemas\ReportTransaction as SchemasReportTransaction;
use Illuminate\Database\Eloquent\Builder;

class ReportTransaction extends Transaction implements SchemasReportTransaction
{
    protected string $__entity = 'ReportTransaction';
    public static $transaction_report_model;

    public function trx(mixed $conditionals = null): Builder{
        return parent::trx($conditionals)->whereIn('status', [$this->usingEntity()->getTransactionStatus('COMPLETED')]);
    }
}
