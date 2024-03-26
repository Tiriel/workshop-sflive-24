<?php

namespace App\Messenger\MessageHandler;

use App\Messenger\Message\AbortPayment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class AbortPaymentHandler
{
    public function __construct(
        protected readonly WorkflowInterface $paymentStateMachine,
        protected readonly EntityManagerInterface $manager,
    )
    {
    }

    public function __invoke(AbortPayment $message): void
    {
        $invoice = $message->getInvoice();
        $this->paymentStateMachine->apply($invoice, 'abort');

        $this->manager->persist($invoice);
        $this->manager->flush();
    }
}
