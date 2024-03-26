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
final class PaymentConfirmedHandler extends AbstractInvoiceMessageHandler
{

    public function getTransition(): string
    {
        return 'pay';
    }

    public function getMessageClassName(): string
    {
        return PaymentConfirmed::class;
    }
}
