<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\User;
use App\Form\MovieType;
use App\Movie\Search\Enum\SearchType;
use App\Movie\Search\Provider\MovieProvider;
use App\Repository\InvoiceRepository;
use App\Repository\MovieRepository;
use App\Security\Voter\MovieVoter;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/movie')]
class MovieController extends AbstractController
{
    #[Route('', name: 'app_movie_index', methods: ['GET'])]
    public function index(Request $request, MovieRepository $repository): Response
    {
        $pager = Pagerfanta::createForCurrentPageWithMaxPerPage(
            new QueryAdapter($repository->getQueryBuilderForPagination()),
            $request->query->get('page', 1),
            6
        );

        return $this->render('movie/index.html.twig', [
            'movies' => $pager,
        ]);
    }

    #[IsGranted(MovieVoter::UNDERAGE, 'movie')]
    #[Route('/{id<\d+>}', name: 'app_movie_show', methods: ['GET'])]
    public function show(?Movie $movie, InvoiceRepository $invoiceRepository): Response
    {
        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
            'invoice' => $invoiceRepository->findRefundable($movie, $this->getUser()),
        ]);
    }

    #[IsGranted(MovieVoter::UNDERAGE, 'movie')]
    #[Route('/omdb/{title}', name: 'app_movie_omdb', methods: ['GET'])]
    public function omdb(#[ValueResolver('movie_title')] ?Movie $movie): Response
    {
        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
        ]);
    }

    #[Route('/new', name: 'app_movie_new', methods: ['GET', 'POST'])]
    #[Route('/{id<\d+>}/edit', name: 'app_movie_edit', methods: ['GET', 'POST'])]
    public function save(?Movie $movie, Request $request, EntityManagerInterface $manager): Response
    {
        if ($movie instanceof Movie) {
            $this->denyAccessUnlessGranted(MovieVoter::CREATOR, $movie);
        }

        $movie ??= new Movie();
        $form = $this->createForm(MovieType::class, $movie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$movie->getId()
                && (($user = $this->getUser()) instanceof User)
                && $this->isGranted(MovieVoter::UNDERAGE, $movie)
            ) {
                $movie->setCreatedBy($user);
            }
            $manager->persist($movie);
            $manager->flush();

            return $this->redirectToRoute(
                'app_movie_show',
                ['id' => $movie->getId()]
            );
        }

        return $this->render('movie/save.html.twig', [
            'form' => $form,
        ]);
    }
}
