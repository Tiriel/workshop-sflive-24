<?php

namespace App\Messenger\Message;

use App\Messenger\Message\AbstractInvoiceMessage;

final class PaymentConfirmed
{
    public function __construct(protected int $invoiceId)
    {
    }

    public function getInvoiceId(): int
    {
        return $this->invoiceId;
    }
}
