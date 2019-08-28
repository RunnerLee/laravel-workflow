<?php
/**
 * @author: RunnerLee
 * @email: runnerleer@gmail.com
 * @time: 2019-08
 */

namespace Runner\LaravelWorkflow\Traits;

use Runner\LaravelWorkflow\Exceptions\MarkingStateException;

trait EloquentStateful
{
    use Workflow;

    public function getPlace()
    {
        $workflow = app('workflow')->get($this)->getName();

        return (int) $this->attributes[config("workflow.{$workflow}.marking_property")];
    }

    public function setPlace($place, array $context)
    {
        $context = array_only($context, $this->fillable);

        $context = array_merge($this->getDirty(), $context);

        $workflow = app('workflow')->get($this)->getName();
        $field = config("workflow.{$workflow}.marking_property");

        $context[$field] = $place;
        $result = $this->newQuery()
            ->where([
                $this->getKeyName() => $this->getKey(),
                $field => $this->attributes[$field],
            ])
            ->update($context);

        if (!$result) {
            throw new MarkingStateException(sprintf(
                'failed to set the "%s" state for model [%s] %s',
                $place,
                get_class($this),
                $this->getKey()
            ));
        }

        $this->original = array_merge($this->original, $context);
        $this->attributes = array_merge($this->attributes, $context);
    }
}
