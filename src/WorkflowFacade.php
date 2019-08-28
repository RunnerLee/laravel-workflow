<?php
/**
 * @author: RunnerLee
 * @email: runnerleer@gmail.com
 * @time: 2019-08
 */

namespace Runner\LaravelWorkflow;

use Illuminate\Support\Facades\Facade;

class WorkflowFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'workflow';
    }
}
