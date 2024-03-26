<?php

namespace App\Controller\Admin;

use App\Entity\Invoice;
use App\Messenger\Message\PaymentFailed;
use App\Messenger\Message\PaymentConfirmed;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/psp')]
class PspController extends AbstractController
{
    #[Route('/pay/{invoiceId<\d+>}', name: 'app_admin_psp_pay', methods: ['GET'])]
    public function pay(int $invoiceId, MessageBusInterface $bus): Response
    {
        $bus->dispatch(new PaymentConfirmed($invoiceId));

        return $this->render('admin/psp/pay.html.twig', [
            'invoice_id' => $invoiceId,
        ]);
    }

    #[Route('/fail/{invoiceId<\d+>}', name: 'app_admin_psp_fail', methods: ['GET'])]
    public function fail(int $invoiceId, MessageBusInterface $bus): Response
    {
        $bus->dispatch(new PaymentFailed($invoiceId));

        return $this->render('admin/psp/fail.html.twig', [
            'invoice_id' => $invoiceId,
        ]);
    }
}
