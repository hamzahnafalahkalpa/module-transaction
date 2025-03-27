<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Hanafalah\LaravelSupport\Data\PaginateData;
use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleTransaction\Contracts\Schemas\Consument as ContractsConsument;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class Consument extends PackageManagement implements ContractsConsument
{
    protected array $__guard      = [];
    protected array $__add        = [];
    protected string $__entity    = 'Consument';
    public static $consument_model;

    protected array $__cache = [
        'show' => [
            'name'     => 'consument',
            'tags'     => ['consument', 'consument-show'],
            'duration' => 60
        ]
    ];

    public function viewUsingRelation(): array{
        return [];
    }

    public function showUsingRelation(): array{
        return ['reference'];
    }

    public function getConsument(): mixed{
        return static::$consument_model;
    }

    public function prepareStoreConsument(?array $attributes = null): Model{
        $attributes ??= request()->all();

        $consument = $this->consument()->updateOrCreate([], []);
        return static::$consument_model = $consument;
    }

    public function prepareShowConsument(?Model $model = null, ?array $attributes = null): Model{
        $attributes ??= request()->all();

        $model ??= $this->getConsument();
        if (!isset($model)) {
            $id = $attributes['id'] ?? null;
            if (!isset($id)) throw new \Exception('Consument not found.', 422);

            $model = $this->consument()->with($this->showUsingRelation())->findOrFail($id);
        } else {
            $model->load($this->showUsingRelation());
        }
        return static::$consument_model = $model;
    }

    public function showConsument(?Model $model = null): array{
        return $this->showEntityResource(function() use ($model){
            return $this->prepareShowConsument($model);
        });
    }

    public function prepareViewConsumentPaginate(PaginateData $paginate_dto): LengthAwarePaginator{
        $consument_model = $this->consument()->orderBy('created_at', 'desc')
            ->paginate(...$paginate_dto->toArray())
            ->appends(request()->all());

        return static::$consument_model = $consument_model;
    }

    public function viewConsumentPaginate(?PaginateData $paginate_dto = null): array{
        return $this->viewEntityResource(function() use ($paginate_dto){
            return $this->prepareViewConsumentPaginate($paginate_dto ?? PaginateData::from(request()->all()));
        });
    }

    public function prepareDeleteConsument(?array $attributes = null): bool{
        $attributes ??= request()->all();
        if (!isset($attributes['id'])) throw new \Exception('Consument not found.', 422);

        $consument = $this->ConsumentModel()->findOrFail($attributes['id']);
        return $consument->delete();
    }

    public function deleteConsument(): bool{
        return $this->transaction(function () {
            return $this->prepareDeleteConsument();
        });
    }

    public function consument(mixed $conditionals = null): Builder{
        $this->booting();
        return $this->ConsumentModel()->conditionals($this->mergeCondition($conditionals))->withParameters()->orderBy('name','asc');
    }
}
