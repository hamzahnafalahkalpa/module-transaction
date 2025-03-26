<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleTransaction\Contracts\Schemas\PaymentMethod as ContractsPaymentMethod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Hanafalah\ModuleTransaction\Resources\PaymentMethod\{
    ShowPaymentMethod,
    ViewPaymentMethod
};

class PaymentMethod extends PackageManagement implements ContractsPaymentMethod
{
    protected array $__guard   = ['id'];
    protected array $__add     = ['name'];
    protected string $__entity = 'PaymentMethod';
    public static $payment_method_model;

    protected array $__resources        = [
        'view' => ViewPaymentMethod::class,
        'show' => ShowPaymentMethod::class
    ];

    public function prepareViewPaymentMethodList(mixed $flags = null): Collection
    {
        return static::$payment_method_model = $this->preparePaymentMethod($flags)->get();
    }

    public function preparePaymentMethod(mixed $flags = null)
    {
        return $this->PaymentMethod();
    }

    // public function prepareViewPaymentMethodPaginate(mixed $flags = null, int $perPage = 50, array $columns = ['*'], string $pageName = 'page',? int $page = null,? int $total = null): LengthAwarePaginator{
    //     $paginate_options = compact('perPage', 'columns', 'pageName', 'page', 'total');
    //     return $this->preparePaymentMethod($flags)
    //                 ->paginate(...$this->arrayValues($paginate_options))
    //                 ->appends(request()->all());
    // }

    // public function viewPaymentMethodPaginate(mixed $flags = null,int $perPage = 50, array $columns = ['*'], string $pageName = 'page',? int $page = null,? int $total = null): array{
    //     $paginate_options = compact('perPage', 'columns', 'pageName', 'page', 'total');
    //     return $this->transforming($this->__resources['show'],function() use ($flags, $paginate_options){
    //         return $this->prepareViewPaymentMethodPaginate($flags, ...$this->arrayValues($paginate_options));
    //     },['rows_per_page' => [50]]);
    // }

    public function viewPaymentMethodList(mixed $flags = null): array
    {
        return $this->transforming($this->__resources['view'], function () use ($flags) {
            return $this->prepareViewPaymentMethodList($flags);
        });
    }

    public function getPaymentMethod(): mixed
    {
        return static::$payment_method_model;
    }

    public function addOrChange(?array $attributes = []): self
    {
        $this->updateOrCreate($attributes);
        return $this;
    }

    protected function PaymentMethod(mixed $conditionals = null): Builder
    {
        return $this->PaymentMethodModel()->conditionals($conditionals);
    }

    public function store()
    {
        return $this->transaction(function () {
            return $this->transforming($this->__resources['show'], function () {
                return $this->prepareStorePaymentMethod();
            });
        });
    }

    public function prepareStorePaymentMethod(mixed $attributes = null)
    {
        $attributes ??= request()->all();

        $payment_method = isset(request()->id) ? $this->PaymentMethod()->find(request()->id) : $this->PaymentMethodModel();

        if (!$payment_method) {
            abort(404, 'Payment method not found.');
        }

        $isUpdatingName = !$payment_method->exists || $payment_method->name !== $attributes['name'];
        if ($isUpdatingName && $this->PaymentMethod()->where('name', $attributes['name'])->exists()) {
            abort(422, 'Payment method with this name already exists.');
        }

        $payment_method->name = $attributes['name'];
        $payment_method->jurnal = $attributes['jurnal'];
        $payment_method->save();
        return $payment_method;
    }
}
