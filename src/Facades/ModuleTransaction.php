<?php

namespace Hanafalah\ModuleTransaction\Facades;

/** 
 * @method static self useSchema(string $className)
 * @method static mixed callCustomMethod()
 * @method static self add(? array $attributes=[])
 * @method static self adds(? array $attributes=[],array $parent_id=[])
 * @method static array outsideFilter(array $attributes, array ...$data)
 * @method static self beforeResolve(array $attributes, array $add, array $guard = [])
 * @method static childSchema($schema,$callback)
 * @method static self change(array $attributes=[])
 * @method static escapingVariables(callable $callback,...$args)
 * @method static self fork(callable $callback)
 * @method static self child(callable $callback)
 * @method static array createInit(array $adds, array $attributes, $guards = []) 
 * @method static object result()
 * @method static array getMessages()
 * @method static self pushMessage(string $message)
 * @method static array getAppModelConfig()
 * @method static self setAppModels(array $models = [])
 * @method static mixed getModel(string $model_name = null)
 * @method static self setModel($model=null)
 * @method static bool isRecentlyCreated($model = null)
 */
class ModuleTransaction extends \Illuminate\Support\Facades\Facade
{
  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor()
  {
    return \Hanafalah\ModuleTransaction\Contracts\ModuleTransaction::class;
  }
}
