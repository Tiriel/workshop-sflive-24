<?php

namespace App\EventListener;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Workflow\Event\CompletedEvent;

final class PersistTransitionListener
{
    public function __construct(
        protected readonly EntityManagerInterface $manager,
    )
    {
    }

    #[AsEventListener(event: 'workflow.payment.completed')]
    public function onWorkflowCompleted(CompletedEvent $event): void
    {
        $invoice = $event->getSubject();
        if (!$invoice instanceof Invoice) {
            return;
        }

        $this->manager->persist($invoice);
        $this->manager->flush();
    }
}
