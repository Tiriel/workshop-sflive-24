<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\Movie;
use App\Messenger\Message\AbortPayment;
use App\Messenger\Message\SubmitPaymentRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Workflow\WorkflowInterface;

class InvoiceController extends AbstractController
{
    #[Route('/checkout/{id<\d+>}', name: 'app_invoice_checkout', methods: ['GET'])]
    public function checkout(?Movie $movie, EntityManagerInterface $manager, WorkflowInterface $paymentWorkflow): Response
    {
        $invoice = (new Invoice())
            ->setMovie($movie)
            ->setUser($this->getUser())
        ;
        $paymentWorkflow->getMarking($invoice);
        dump($invoice);
        $manager->persist($invoice);
        $manager->flush();

        return $this->render('invoice/index.html.twig', [
            'invoice' => $invoice,
            'movie' => $movie,
        ]);
    }

    #[Route('/pay/{id<\d+>}', name: 'app_invoice_pay', methods: ['GET'])]
    public function pay(?Invoice $invoice, MessageBusInterface $bus): Response
    {
        $bus->dispatch(new SubmitPaymentRequest($invoice->getId()));

        $this->addFlash('success', 'Your order has been confirmed!');

        return $this->redirectToRoute('app_movie_show', ['id' => $invoice->getMovie()->getId()]);
    }

    #[Route('/abort/{id<\d+>}', name: 'app_invoice_abort', methods: ['GET'])]
    public function abort(?Invoice $invoice, MessageBusInterface $bus): Response
    {
        $bus->dispatch(new AbortPayment($invoice->getId()));

        $this->addFlash('danger', 'Your order has been aborted.');

        return $this->redirectToRoute('app_movie_show', ['id' => $invoice->getMovie()->getId()]);
    }
}
