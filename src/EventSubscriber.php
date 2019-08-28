<?php
/**
 * @author: RunnerLee
 * @email: runnerleer@gmail.com
 * @time: 2019-08
 */

namespace Runner\LaravelWorkflow;

use Symfony\Component\Workflow\Event\EnterEvent;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\LeaveEvent;
use Symfony\Component\Workflow\Event\EnteredEvent;
use Symfony\Component\Workflow\Event\AnnounceEvent;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Component\Workflow\Event\Event as WorkflowEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventSubscriber implements EventSubscriberInterface
{
    public function guard(GuardEvent $event, $name)
    {
        $this->registerEvents(
            $event,
            'guard',
            $event->getTransition()->getName(),
            [$event, $name]
        );
    }

    public function leave(LeaveEvent $event, $name)
    {
        $this->registerEvents(
            $event,
            'leave',
            $event->getTransition()->getFroms(),
            [$event, $name]
        );
    }

    public function transition(TransitionEvent $event, $name)
    {
        $this->registerEvents(
            $event,
            'transition',
            $event->getTransition()->getName(),
            [$event, $name]
        );
    }

    public function enter(EnterEvent $event, $name)
    {
        $this->registerEvents(
            $event,
            'enter',
            $event->getTransition()->getTos(),
            [$event, $name]
        );
    }

    public function entered(EnteredEvent $event, $name)
    {
        $this->registerEvents(
            $event,
            'entered',
            $event->getTransition()->getTos(),
            [$event, $name]
        );
    }

    public function completed(CompletedEvent $event, $name)
    {
        $this->registerEvents(
            $event,
            'completed',
            $event->getTransition()->getName(),
            [$event, $name]
        );
    }

    public function announce(AnnounceEvent $event, $name)
    {
        $this->registerEvents(
            $event,
            'announce',
            $event->getTransition()->getName(),
            [$event, $name]
        );
    }

    protected function registerEvents(WorkflowEvent $event, $type, $subjects, array $payload)
    {
        $workflow = $event->getWorkflowName();
        event(sprintf('workflow.%s', $type), $payload);
        event(sprintf('workflow.%s.%s', $workflow, $type), $payload);

        foreach ((array) $subjects as $subject) {
            event(sprintf('workflow.%s.%s.%s', $workflow, $type, $subject), $payload);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            // validate
            'workflow.guard' => ['guard'],

            // before marking
            'workflow.leave' => ['leave'],
            'workflow.transition' => ['transition'],
            'workflow.enter' => ['enter'],

            // after marking
            'workflow.entered' => ['entered'],
            'workflow.completed' => ['completed'],
            'workflow.announce' => ['announce'],
        ];
    }
}
