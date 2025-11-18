<?php

namespace Hanafalah\ModuleTransaction\Data;

use Hanafalah\LaravelSupport\Data\UnicodeData;
use Hanafalah\ModuleTransaction\Contracts\Data\MasterReportData as DataMasterReportData;

class MasterReportData extends UnicodeData implements DataMasterReportData
{
    public static function before(array &$attributes){
        $attributes['flag'] ??= 'MasterReport';
        parent::before($attributes);
    }
}