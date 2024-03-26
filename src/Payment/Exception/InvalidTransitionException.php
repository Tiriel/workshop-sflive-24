<?php

namespace App\Payment\Exception;

use App\Entity\Invoice;
use Symfony\Component\Messenger\Exception\RecoverableMessageHandlingException;

class InvalidTransitionException extends RecoverableMessageHandlingException
{
    public function __construct(Invoice $invoice)
    {
        $message = sprintf("Invalid transition for invoice %d", $invoice->getId());

        parent::__construct($message);
    }
}
