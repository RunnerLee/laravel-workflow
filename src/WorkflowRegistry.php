<?php
/**
 * @author: RunnerLee
 * @email: runnerleer@gmail.com
 * @time: 2019-08
 */

namespace Runner\LaravelWorkflow;

use SplObjectStorage;
use Illuminate\Support\Arr;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\StateMachine;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Workflow\Metadata\InMemoryMetadataStore;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\SupportStrategy\InstanceOfSupportStrategy;

class WorkflowRegistry
{
    protected $registry;

    protected $dispatcher;

    public function __construct(array $config)
    {
        $this->registry = new Registry();
        $this->dispatcher = new EventDispatcher();

        $this->initial($config);
    }

    protected function initial(array $config)
    {
        foreach ($config as $workflowName => $workflowConfig) {
            $builder = new DefinitionBuilder();

            $placesMetadata = [];
            $transitionsMetadata = new SplObjectStorage();

            // configure places
            $places = $workflowConfig['places'] ?? [];
            if (!Arr::isAssoc($places)) {
                $builder->addPlaces($places);
            } else {
                foreach ($places as $name => $placeConfig) {
                    $builder->addPlace($name);
                    if (isset($placeConfig['metadata'])) {
                        $placesMetadata[$name] = $placeConfig['metadata'];
                    }
                }
            }

            // configure transitions
            $transitions = $workflowConfig['transitions'] ?? [];
            foreach ($transitions as $name => $transitionConfig) {
                $builder->addTransition($transition = new Transition($name, $transitionConfig['from'], $transitionConfig['to']));
                if (isset($transitionConfig['metadata'])) {
                    $transitionsMetadata[$transition] = (array) $transitionConfig['metadata'];
                }
            }

            $builder->setMetadataStore(new InMemoryMetadataStore(
                $workflowConfig['metadata'] ?? [],
                $placesMetadata,
                $transitionsMetadata
            ));

            $workflow = new StateMachine(
                $builder->build(),
                new MethodMarkingStore($workflowConfig['single_status'], 'place'),
                $this->dispatcher,
                $workflowName
            );

            foreach ((array) $workflowConfig['supports'] as $support) {
                $this->registry->addWorkflow($workflow, new InstanceOfSupportStrategy($support));
            }
        }

        $this->dispatcher->addSubscriber(new EventSubscriber());
    }

    public function __call($name, $arguments)
    {
        return $this->registry->$name(...$arguments);
    }
}
