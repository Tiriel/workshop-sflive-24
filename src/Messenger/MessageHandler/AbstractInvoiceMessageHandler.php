<?php

namespace App\Messenger\MessageHandler;

use App\Entity\Invoice;
use App\Messenger\Message\AbstractInvoiceMessage;
use App\Payment\Exception\InvalidTransitionException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Exception\TransitionException;
use Symfony\Component\Workflow\WorkflowInterface;

abstract class AbstractInvoiceMessageHandler
{
    public function __construct(
        protected readonly WorkflowInterface $paymentWorkflow,
        protected readonly EntityManagerInterface $manager,
    )
    {
    }

    public function __invoke(AbstractInvoiceMessage $message): void
    {
        $classname = $this->getMessageClassName();
        if (!$message instanceof $classname) {
            return;
        }

        $invoice = $this->manager->find(Invoice::class, $message->getInvoiceId());

        try {
            $this->paymentWorkflow->apply($invoice, $this->getTransition());
        } catch (TransitionException) {
            throw new InvalidTransitionException($invoice);
        }
    }


    abstract public function getTransition(): string;
    abstract public function getMessageClassName(): string;
}
