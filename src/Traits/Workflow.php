<?php
/**
 * @author: RunnerLee
 * @email: runnerleer@gmail.com
 * @time: 2019-08
 */

namespace Runner\LaravelWorkflow\Traits;

trait Workflow
{
    public function canTransition($transition)
    {
        return app('workflow')->get($this)->can($this, $transition);
    }

    public function applyTransition($transition, array $context = [])
    {
        return app('workflow')->get($this)->apply($this, $transition, $context);
    }
}
