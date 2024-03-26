<?php

namespace App\Messenger\MessageHandler;

use App\Entity\Invoice;
use App\Messenger\Message\PaymentFailed;
use App\Payment\Exception\InvalidTransitionException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\Exception\TransitionException;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
final class PaymentFailedHandler extends AbstractInvoiceMessageHandler
{

    public function getTransition(): string
    {
        return 'fail';
    }

    public function getMessageClassName(): string
    {
        return PaymentFailed::class;
    }
}
