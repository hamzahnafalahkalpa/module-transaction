<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleTransaction\Contracts\PaymentSummary as ContractsPaymentSummary;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Hanafalah\ModuleTransaction\Resources\PaymentSummary\{
    ShowPaymentSummary,
    ViewPaymentSummary
};

class PaymentSummary extends PackageManagement implements ContractsPaymentSummary
{
    protected array $__guard   = [
        'id',
        'parent_id',
        'transaction_id',
        'reference_id',
        'reference_type',
        'payment_history_id'
    ];
    protected array $__add     = [
        'total_amount',
        'total_debt',
        'total_discount',
        'total_tax',
        'total_additional'
    ];
    protected string $__entity = 'PaymentSummary';
    public static $payment_summary_model;

    protected array $__resources        = [
        'view' => ViewPaymentSummary::class,
        'show' => ShowPaymentSummary::class
    ];

    public function prepareViewPaymentSummaryList(mixed $flags = null): Collection
    {
        return static::$payment_summary_model = $this->preparePaymentSummary($flags)->get();
    }

    public function preparePaymentSummary(mixed $flags = null)
    {
        return $this->paymentSummary()->when(isset($flags), function ($query) use ($flags) {
            $flags = $this->mustArray($flags);
            $query->whereIn('reference_type', $flags);
        });
    }

    public function prepareViewPaymentSummaryPaginate(mixed $flags = null, int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): LengthAwarePaginator
    {
        $paginate_options = compact('perPage', 'columns', 'pageName', 'page', 'total');
        return $this->preparePaymentSummary($flags)
            ->paginate(...$this->arrayValues($paginate_options))
            ->appends(request()->all());
    }

    public function viewPaymentSummaryPaginate(mixed $flags = null, int $perPage = 50, array $columns = ['*'], string $pageName = 'page', ?int $page = null, ?int $total = null): array
    {
        $paginate_options = compact('perPage', 'columns', 'pageName', 'page', 'total');
        return $this->transforming($this->__resources['show'], function () use ($flags, $paginate_options) {
            return $this->prepareViewPaymentSummaryPaginate($flags, ...$this->arrayValues($paginate_options));
        }, ['rows_per_page' => [50]]);
    }

    public function viewPaymentSummaryList(mixed $flags = null): array
    {
        return $this->transforming($this->__resources['view'], fn() => $this->prepareViewPaymentSummaryList($flags));
    }

    public function getPaymentSummary(): mixed
    {
        return static::$payment_summary_model;
    }

    public function addOrChange(?array $attributes = []): self
    {
        $this->updateOrCreate($attributes);
        return $this;
    }

    protected function paymentSummary(mixed $conditionals = null): Builder
    {
        return $this->PaymentSummaryModel()->conditionals($conditionals);
    }

    public function get(mixed $conditionals = null): Collection
    {
        return $this->paymentSummary($conditionals)->get();
    }
}
