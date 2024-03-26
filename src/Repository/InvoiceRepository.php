<?php

namespace App\Repository;

use App\Entity\Invoice;
use App\Entity\Movie;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * @extends ServiceEntityRepository<Invoice>
 *
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, protected WorkflowInterface $paymentWorkflow)
    {
        parent::__construct($registry, Invoice::class);
    }

    public function findRefundable(Movie $movie, User $user): ?Invoice
    {
        $invoices = $this->createQueryBuilder('i')
            ->andWhere('i.movie = :movie')
            ->andWhere('i.user = :user')
            ->setParameter('movie', $movie)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        foreach ($invoices as $invoice) {
            dump($invoice);
            if ($this->paymentWorkflow->can($invoice, 'submit_refund_request')) {
                return $invoice;
            }
        }

        return null;
    }

    //    /**
    //     * @return Invoice[] Returns an array of Invoice objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Invoice
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
