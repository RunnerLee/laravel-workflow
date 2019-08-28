<?php
/**
 * @author: RunnerLee
 * @email: runnerleer@gmail.com
 * @time: 2019-08
 */

namespace Runner\LaravelWorkflow;

use Illuminate\Support\ServiceProvider;

class WorkflowServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes(
                [
                    __DIR__ . '/../config/workflow.php' => config_path('workflow.php'),
                ],
                'config'
            );
        }
    }

    public function register()
    {
        $this->app->singleton('workflow', function () {
            return new WorkflowRegistry($this->app->get('config')->get('workflow'));
        });
    }
}
