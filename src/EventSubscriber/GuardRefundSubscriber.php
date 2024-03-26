<?php

namespace App\EventSubscriber;

use App\Entity\Invoice;
use App\Payment\Exception\InvalidTransitionException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\TransitionBlocker;

class GuardRefundSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected readonly AuthorizationCheckerInterface $checker
    )
    {
    }

    public function onWorkflowGuard(GuardEvent $event): void
    {
        $invoice = $event->getSubject();
        if (!$invoice instanceof Invoice) {
            return;
        }

        if (!$this->checker->isGranted('ROLE_ADMIN')) {
            $event->addTransitionBlocker(new TransitionBlocker('Only admins can accept or reject refunds', 0));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.payment.guard.accept_refund' => 'onWorkflowGuard',
            'workflow.payment.guard.reject_refund' => 'onWorkflowGuard',
        ];
    }
}
