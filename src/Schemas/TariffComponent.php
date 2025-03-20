<?php

namespace Zahzah\ModuleTransaction\Schemas;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Zahzah\LaravelSupport\Supports\PackageManagement;
use Zahzah\ModuleTransaction\Contracts\TariffComponent as ContractsTariffComponent;
use Zahzah\ModuleTransaction\Resources\TariffComponent\ShowTariffComponent;
use Zahzah\ModuleTransaction\Resources\TariffComponent\ViewTariffComponent;

class TariffComponent extends PackageManagement implements ContractsTariffComponent{
    protected array $__guard   = ['id'];
    protected array $__add     = ['name'];
    protected string $__entity = 'TariffComponent';
    public static $tariff_model;

    protected array $__resources = [
        'view' => ViewTariffComponent::class,
        'show' => ShowTariffComponent::class
    ];

    protected array $__cache = [
        'index' => [
            'name'     => 'tariff-component',
            'tags'     => ['tariff-component','tariff-component-index'],
            'forever'  => true
        ]
    ];

    public function addOrChange(? array $attributes=[]): self{
        $this->booting();
        $this->updateOrCreate($attributes);
        return $this;
    }

    public function prepareStoreTariffComponent(? array $attributes = null): Model {
        $attributes ??= request()->all();
        $tariff = $this->TariffComponentModel()->updateOrCreate([
            'id' => $attributes['id'] ?? null
        ],[
            'name' => $attributes['name']
        ]);

        $tariff->componentDetails()->delete();
        if (isset($attributes['component_details']) && count($attributes['component_details']) > 0) {
            foreach ($attributes['component_details'] as $component) {
                $component_model = $tariff->componentDetails()->create(['flag' => $component['flag']]);
                if (isset($component['coa_account_id'])) $component_model->jurnal = ["coa_account_id" => $component['coa_account_id']];
                $component_model->save();
            }

            $this->addSuffixCache($this->__cache['index'], "tariff-component-index", '');
            $this->flushTagsFrom('index');
        }

        $this->forgetTags('tariff-component');
        return static::$tariff_model = $tariff;
    }

    public function storeTariffComponent(): array{
        return $this->transaction(function(){
            return $this->showTariffComponent($this->prepareStoreTariffComponent());
        });
    }

    public function showUsingRelation(): array{
        return [
            'componentDetails'
        ];
    }

    public function prepareShowTariffComponent(? Model $model = null): ?Model{
        $this->booting();

        $model ??= $this->getTariffComponent();
        if (!isset($model)){
            $id = request()->id;
            if (!request()->has('id')) throw new \Exception('No id provided',422);

            $model = $model = $this->tariffComponent()->with($this->showUsingRelation())->find($id);
        }else{
            $model->load($this->showUsingRelation());
        }

        return static::$tariff_model = $model;
    }

    public function showTariffComponent(? Model $model = null): array{
        return $this->transforming($this->__resources['show'],function() use ($model){
            return $this->prepareShowTariffComponent($model);
        });
    }

    public function prepareRemoveTariffComponent(): bool{
        $id = request()->id;
        if (!request()->has('id')) throw new \Exception('No id provided',422);
        $this->tariffComponent()->find($id)->delete();
        $this->flushTagsFrom('index');
        return true;
    }

    public function removeTariffComponentById(): bool{
        return $this->transaction(function(){
            return $this->prepareRemoveTariffComponent();
        });
    }

    public function prepareViewTariffComponentList(string|array $flags): Collection{
        $flags ??= [];
        $flags = $this->mustArray($flags);
        $this->addSuffixCache($this->__cache['index'],"tariff-component-index",implode('-',$flags));
        return static::$tariff_model = $this->cacheWhen(!$this->isSearch(),$this->__cache['index'],function() use ($flags){
            return $this->tariffComponent()->whereHas('componentDetails',fn($query) => $query->flagIn($flags))->orderBy('name','asc')->get();
        });
    }

    public function prepareViewTariffComponentPaginate(int $perPage = 50, array $columns = ['*'], string $pageName = 'page',? int $page = null,? int $total = null): LengthAwarePaginator{
        $paginate_options = compact('perPage', 'columns', 'pageName', 'page', 'total');

        $this->addSuffixCache($this->__cache['index'],"tariff-component-index",'paginate');
        return $this->cacheWhen(!$this->isSearch(),$this->__cache['index'],function() use ($paginate_options){
            return $this->tariffComponent()->paginate(
                ...$this->arrayValues($paginate_options)
            );
        });
    }

    public function viewTariffComponentPaginate(int $perPage = 50, array $columns = ['*'], string $pageName = 'page',? int $page = null,? int $total = null): array{
        $paginate_options = compact('perPage', 'columns', 'pageName', 'page', 'total');
        return $this->transforming($this->__resources['show'],function() use ($paginate_options){
            return $this->prepareViewTariffComponentPaginate(...$this->arrayValues($paginate_options));
        },['rows_per_page' => [50]]);
    }

    public function viewTariffComponentList(string|array $flags): array{
        return $this->transforming($this->__resources['view'], fn()=> $this->prepareViewTariffComponentList($flags));
    }

    public function getTariffComponent(): mixed{
        return static::$tariff_model;
    }

    protected function tariffComponent(mixed $conditionals = null): Builder{
        $this->booting();
        return $this->TariffComponentModel()->with('componentDetails')->withParameters()
                    ->conditionals($this->mergeCondition($conditionals ?? []));
    }
}
