<?php

namespace App\Messenger\MessageHandler;

use App\Messenger\Message\SubmitPaymentRequest;
use App\Payment\PaymentGateway;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class SubmitPaymentRequestHandler
{
    public function __construct(
        protected readonly PaymentGateway $gateway,
        protected readonly WorkflowInterface $paymentStateMachine,
        protected readonly EntityManagerInterface $manager,
    )
    {
    }

    public function __invoke(SubmitPaymentRequest $message): void
    {
        $invoice = $message->getInvoice();
        $response = $this->gateway->initiatePayment($invoice);

        if ($this->paymentStateMachine->can($invoice, 'submit_request')) {
            $this->paymentStateMachine->apply($invoice, 'submit_request');

            $this->manager->persist($invoice);
            $this->manager->flush();
        }
    }
}
