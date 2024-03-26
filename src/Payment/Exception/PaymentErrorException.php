<?php

namespace App\Payment\Exception;

use App\Entity\Invoice;

class PaymentErrorException extends AbstractInvoiceException
{
    public function __construct(Invoice $invoice)
    {
        $message = sprintf("Error on payment for invoice %d", $invoice->getId());
        parent::__construct($invoice, $message);
    }
}
