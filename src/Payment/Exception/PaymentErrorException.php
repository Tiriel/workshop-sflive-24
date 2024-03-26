<?php

namespace App\Payment\Exception;

use App\Entity\Invoice;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

class PaymentErrorException extends UnrecoverableMessageHandlingException
{
    public function __construct(Invoice $invoice)
    {
        $message = sprintf("Error on payment for invoice %d", $invoice->getId());

        parent::__construct($message);
    }
}
