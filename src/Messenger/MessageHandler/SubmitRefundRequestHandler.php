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
final class SubmitRefundRequestHandler extends AbstractInvoiceMessageHandler
{

    public function getTransition(): string
    {
        return 'submit_refund_request';
    }

    public function getMessageClassName(): string
    {
        return SubmitRefundRequest::class;
    }
}
