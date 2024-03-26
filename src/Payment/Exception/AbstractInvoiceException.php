<?php

namespace App\Payment\Exception;

use App\Entity\Invoice;

abstract class AbstractInvoiceException extends \Exception
{
    public function __construct(protected Invoice $invoice, string $message = '')
    {
        parent::__construct($message);
    }
}
