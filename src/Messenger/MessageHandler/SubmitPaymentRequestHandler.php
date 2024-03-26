<?php

namespace App\Messenger\MessageHandler;

use App\Messenger\Message\SubmitPaymentRequest;
use App\Payment\Exception\InvalidTransitionException;
use App\Payment\Exception\PaymentErrorException;
use App\Payment\PaymentGateway;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;

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

        try {
            $response = $this->gateway->initiatePayment($invoice);
        } catch (HttpExceptionInterface) {
            throw new PaymentErrorException($invoice);
        }

        if (!$this->paymentStateMachine->can($invoice, 'submit_request')) {
            throw new InvalidTransitionException($invoice);
        }

        $this->paymentStateMachine->apply($invoice, 'submit_request');

        $this->manager->persist($invoice);
        $this->manager->flush();
    }
}
