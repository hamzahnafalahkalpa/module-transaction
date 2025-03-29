<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Hanafalah\LaravelSupport\Contracts\Data\PaginateData;
use Hanafalah\ModuleTransaction\Contracts\Schemas\ReportTransaction as ContractsReportTransaction;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportTransaction extends Transaction implements ContractsReportTransaction
{
    protected string $__entity = 'ReportTransaction';
    public static $transaction_report_model;

    protected function viewUsingRelation(): array{
        return [];
    }

    protected function showUsingRelation(): array{
        return [
            "reference",
            "transactionItems",
            "paymentSummary"
        ];
    }

    protected function commonTransaction($morphs){
        return $this->trx()->when(isset($morphs), function ($query) use ($morphs) {
                    $query->whereIn('reference_type', $this->mustArray($morphs));
                })->with($this->showUsingRelation())
                ->whereIn('status', [$this->getTransactionStatus()['COMPLETED']]);
    }

    public function prepareViewTransactionReportPaginate(mixed $cache_reference_type = null, ?array $morphs = null, PaginateData $paginate_dto): LengthAwarePaginator{
        $morphs ??= $cache_reference_type;
        $cache_reference_type ??= 'all';
        $cache_reference_type .= '-paginate';
        $this->localAddSuffixCache($cache_reference_type);
        return $this->commonTransaction($morphs)->paginate(...$paginate_dto->toArray())
                    ->appends(request()->all());
    }

    public function viewTransactionReportPaginate(mixed $cache_reference_type = null, ?array $morphs = null, ? PaginateData $paginate_dto = null): array{
        return $this->viewEntityResource(function() use ($cache_reference_type, $morphs, $paginate_dto){
            return $this->prepareViewTransactionReportPaginate($cache_reference_type, $morphs, $paginate_dto ?? $this->requestDTO(PaginateData::class));
        });
    }
}
