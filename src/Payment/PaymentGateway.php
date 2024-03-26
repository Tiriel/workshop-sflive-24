<?php

namespace App\Payment;

use App\Entity\Invoice;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class PaymentGateway
{
    public function __construct()
    {
        $this->client = new MockHttpClient(
            new MockResponse(\json_encode([
                'response' => 'ok',
                'payment_id' => 'foo_bar_baz',
            ]),
            [
                'http_code' => 200,
            ])
        );
    }

    public function initiatePayment(Invoice $invoice): array
    {
        return $this->client->request(
            'GET',
            'https://fake.paypementprovider.com',
            [
                'invoice_id' => $invoice->getId(),
            ]
        )->toArray();
    }
}
