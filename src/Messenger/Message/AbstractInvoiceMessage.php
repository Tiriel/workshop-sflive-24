<?php

namespace App\Messenger\Message;

use App\Entity\Invoice;

abstract class AbstractInvoiceMessage
{
    public function __construct(protected Invoice $invoice)
    {
    }

    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }
}
