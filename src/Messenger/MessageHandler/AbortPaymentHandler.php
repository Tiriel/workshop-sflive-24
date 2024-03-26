<?php

namespace App\Messenger\MessageHandler;

use App\Messenger\Message\AbortPayment;
use App\Payment\Exception\InvalidTransitionException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\Exception\TransitionException;
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

        try {
            $this->paymentStateMachine->apply($invoice, 'abort');
        } catch (TransitionException) {
            throw new InvalidTransitionException($invoice);
        }

        $this->manager->persist($invoice);
        $this->manager->flush();
    }
}
