<?php

namespace App\Messenger\MessageHandler;

use App\Entity\Invoice;
use App\Messenger\Message\AbortPayment;
use App\Payment\Exception\InvalidTransitionException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\Exception\TransitionException;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class AbortPaymentHandler extends AbstractInvoiceMessageHandler
{
    public function getTransition(): string
    {
        return 'abort';
    }

    public function getMessageClassName(): string
    {
        return AbortPayment::class;
    }
}
