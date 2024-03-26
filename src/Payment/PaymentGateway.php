<?php

namespace App\Payment;

use App\Entity\Invoice;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class PaymentGateway
{
    protected ?MockHttpClient $client = null;

    public function __construct()
    {
        $this->client = new MockHttpClient(function ($method, $url, $options) {
            return new MockResponse(\json_encode([
                    'response' => 'ok',
                    'payment_id' => 'foo_bar_baz',
                    'invoice_id' => $options['invoice_id'],
                ]),
                [
                    'http_code' => 200,
                ]);
        });
    }

    public function initiatePayment(Invoice $invoice): array
    {
        return $this->client->request(
            'GET',
            'https://fake.paymentprovider.com',
            [
                'invoice_id' => $invoice->getId(),
            ]
        )->toArray();
    }
}
