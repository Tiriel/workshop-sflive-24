<?php

namespace App\Payment\Exception;

use App\Entity\Invoice;

class InvalidTransitionException extends AbstractInvoiceException
{
    public function __construct(Invoice $invoice)
    {
        $message = sprintf("Invalid transition for invoice %d", $invoice->getId());
        parent::__construct($invoice, $message);
    }
}
