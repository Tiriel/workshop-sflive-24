<?php

namespace App\Messenger\MessageHandler;

use App\Entity\Invoice;
use App\Messenger\Message\PaymentConfirmed;
use App\Payment\Exception\InvalidTransitionException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\Exception\TransitionException;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
final class PaymentConfirmedHandler
{
    public function __construct(
        protected readonly WorkflowInterface $paymentWorkflow,
        protected readonly EntityManagerInterface $manager,
    )
    {
    }

    public function __invoke(PaymentConfirmed $message)
    {
        $invoice = $this->manager->getRepository(Invoice::class)->find($message->getInvoiceId());

        try {
            $this->paymentWorkflow->apply($invoice, 'pay');
        } catch (TransitionException) {
            throw new InvalidTransitionException($invoice);
        }

        $this->manager->flush();
    }
}
