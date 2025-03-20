<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Hanafalah\ModuleTransaction\Contracts\ReportTransaction as ContractsReportTransaction;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportTransaction extends Transaction implements ContractsReportTransaction
{
    protected function showUsingRelation(): array
    {
        return [
            "reference",
            "transactionItems",
            "paymentSummary"
        ];
    }

    protected function getTransactionBuilder($morphs)
    {
        $status = $this->getTransactionStatus();
        return $this->trx(function ($query) use ($morphs) {
            $query->when(isset($morphs), function ($query) use ($morphs) {
                $morphs = $this->mustArray($morphs);
                $query->whereIn('reference_type', $morphs);
            });
        })->with($this->showUsingRelation())
            ->whereIn('status', [$status['COMPLETED']]);
    }

    public function prepareViewTransactionReportPaginate(mixed $cache_reference_type = null, ?array $morphs = null, int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): LengthAwarePaginator
    {
        $morphs ??= $cache_reference_type;
        $paginate_options = compact('perPage', 'columns', 'pageName', 'page', 'total');
        $cache_reference_type ??= 'all';
        $cache_reference_type .= '-paginate';
        $this->localAddSuffixCache($cache_reference_type);
        // return $this->cacheWhen(!$this->isSearch(),$this->__cache['index'],function() use ($morphs,$paginate_options){
        return $this->getTransactionBuilder($morphs)
            ->paginate(...$this->arrayValues($paginate_options))
            ->appends(request()->all());
        // });
    }

    public function viewTransactionReportPaginate(mixed $cache_reference_type = null, ?array $morphs = null, int $perPage = 10, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): array
    {
        $paginate_options = compact('perPage', 'columns', 'pageName', 'page', 'total');
        return $this->transforming($this->__resources['view'], function () use ($cache_reference_type, $morphs, $paginate_options) {
            return $this->prepareViewTransactionReportPaginate($cache_reference_type, $morphs, ...$this->arrayValues($paginate_options));
        });
    }
}
