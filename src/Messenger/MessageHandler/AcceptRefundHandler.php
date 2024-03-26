<?php

namespace App\Messenger\MessageHandler;

use App\Entity\Invoice;
use App\Messenger\Message\AcceptRefund;
use App\Payment\Exception\InvalidTransitionException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\Exception\TransitionException;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
final class AcceptRefundHandler extends AbstractInvoiceMessageHandler
{

    public function getTransition(): string
    {
        return 'accept_refund';
    }

    public function getMessageClassName(): string
    {
        return AcceptRefund::class;
    }
}
