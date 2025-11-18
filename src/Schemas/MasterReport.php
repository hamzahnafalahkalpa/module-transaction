<?php

namespace Hanafalah\ModuleTransaction\Schemas;

use Hanafalah\LaravelSupport\Schemas\Unicode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Hanafalah\ModuleTransaction\Contracts\Schemas\MasterReport as ContractsMasterReport;
use Hanafalah\ModuleTransaction\Contracts\Data\MasterReportData;

class MasterReport extends Unicode implements ContractsMasterReport
{
    protected string $__entity = 'MasterReport';
    public $master_report_model;
    //protected mixed $__order_by_created_at = false; //asc, desc, false

    protected array $__cache = [
        'index' => [
            'name'     => 'master_report',
            'tags'     => ['master_report', 'master_report-index'],
            'duration' => 24 * 60
        ]
    ];

    public function prepareStoreMasterReport(MasterReportData $master_report_dto): Model{
        $master_report = parent::prepareStoreUnicode($master_report_dto);
        return $this->master_report_model = $master_report;
    }

    public function masterReport(mixed $conditionals = null): Builder{
        return $this->unicode($conditionals);
    }
}