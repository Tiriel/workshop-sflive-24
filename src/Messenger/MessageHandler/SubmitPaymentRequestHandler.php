<?php

namespace App\Messenger\MessageHandler;

use App\Entity\Invoice;
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
        protected readonly WorkflowInterface $paymentWorkflow,
        protected readonly EntityManagerInterface $manager,
    )
    {
    }

    public function __invoke(SubmitPaymentRequest $message): void
    {
        $invoice = $this->manager->find(Invoice::class, $message->getInvoiceId());

        try {
            $response = $this->gateway->initiatePayment($invoice);
        } catch (HttpExceptionInterface) {
            throw new PaymentErrorException($invoice);
        }

        if (!$this->paymentWorkflow->can($invoice, 'submit_request')) {
            throw new InvalidTransitionException($invoice);
        }

        $this->paymentWorkflow->apply($invoice, 'submit_request');

        $this->manager->persist($invoice);
        $this->manager->flush();
    }
}
