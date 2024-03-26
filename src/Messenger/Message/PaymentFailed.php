<?php

namespace App\Messenger\Message;

final class PaymentFailed
{
    public function __construct(protected int $invoiceId)
    {
    }

    public function getInvoiceId(): int
    {
        return $this->invoiceId;
    }
}
