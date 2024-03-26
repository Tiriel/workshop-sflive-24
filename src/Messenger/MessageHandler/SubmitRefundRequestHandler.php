<?php

namespace App\Messenger\MessageHandler;

use App\Entity\Invoice;
use App\Messenger\Message\SubmitRefundRequest;
use App\Payment\Exception\InvalidTransitionException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\Exception\TransitionException;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
final class SubmitRefundRequestHandler
{
    public function __construct(
        protected readonly WorkflowInterface $paymentWorkflow,
        protected readonly EntityManagerInterface $manager,
    )
    {
    }

    public function __invoke(SubmitRefundRequest $message)
    {
        $invoice = $this->manager->find(Invoice::class, $message->getInvoiceId());

        try {
            $this->paymentWorkflow->apply($invoice, 'submit_refund_request');
        } catch (TransitionException) {
            throw new InvalidTransitionException($invoice);
        }

        $this->manager->flush();
    }
}
