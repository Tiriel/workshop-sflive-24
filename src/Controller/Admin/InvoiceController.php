<?php

namespace App\Controller\Admin;

use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Messenger\Message\AcceptRefund;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/invoice')]
class InvoiceController extends AbstractController
{
    #[Route('/', name: 'app_admin_invoice_index', methods: ['GET'])]
    public function index(InvoiceRepository $invoiceRepository): Response
    {
        return $this->render('admin/invoice/index.html.twig', [
            'invoices' => $invoiceRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_invoice_show', methods: ['GET'])]
    public function show(Invoice $invoice): Response
    {
        return $this->render('admin/invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('{id<\d+>}/refund/accept', name: 'app_admin_invoice_accept_refund')]
    public function acceptRefund(?Invoice $invoice, MessageBusInterface $bus): Response
    {
        $bus->dispatch(new AcceptRefund($invoice->getId()));

        $this->addFlash('info', 'Refund accepted');

        return $this->redirectToRoute('app_admin_invoice_show', ['id' => $invoice->getId()]);
    }

    #[Route('/{id}', name: 'app_admin_invoice_delete', methods: ['POST'])]
    public function delete(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$invoice->getId(), $request->request->get('_token'))) {
            $entityManager->remove($invoice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_invoice_index', [], Response::HTTP_SEE_OTHER);
    }
}
