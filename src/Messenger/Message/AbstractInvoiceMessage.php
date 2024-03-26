<?php

namespace App\Messenger\Message;

abstract class AbstractInvoiceMessage
{
    public function __construct(protected int $invoiceId)
    {
    }

    public function getInvoiceId(): int
    {
        return $this->invoiceId;
    }
}
