<?php

namespace Hanafalah\ModuleTransaction\Contracts\Schemas;

use Hanafalah\LaravelSupport\Contracts\Schemas\Unicode;
use Hanafalah\ModuleTransaction\Contracts\Data\MasterReportData;
//use Hanafalah\ModuleTransaction\Contracts\Data\MasterReportUpdateData;
use Hanafalah\LaravelSupport\Contracts\Supports\DataManagement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @see \Hanafalah\ModuleTransaction\Schemas\MasterReport
 * @method mixed export(string $type)
 * @method self conditionals(mixed $conditionals)
 * @method array updateMasterReport(?MasterReportData $master_report_dto = null)
 * @method Model prepareUpdateMasterReport(MasterReportData $master_report_dto)
 * @method bool deleteMasterReport()
 * @method bool prepareDeleteMasterReport(? array $attributes = null)
 * @method mixed getMasterReport()
 * @method ?Model prepareShowMasterReport(?Model $model = null, ?array $attributes = null)
 * @method array showMasterReport(?Model $model = null)
 * @method Collection prepareViewMasterReportList()
 * @method array viewMasterReportList()
 * @method LengthAwarePaginator prepareViewMasterReportPaginate(PaginateData $paginate_dto)
 * @method array viewMasterReportPaginate(?PaginateData $paginate_dto = null)
 * @method array storeMasterReport(?MasterReportData $master_report_dto = null)
 * @method Collection prepareStoreMultipleMasterReport(array $datas)
 * @method array storeMultipleMasterReport(array $datas)
 */

interface MasterReport extends Unicode
{
    public function prepareStoreMasterReport(MasterReportData $master_report_dto): Model;
    public function masterReport(mixed $conditionals = null): Builder;
}