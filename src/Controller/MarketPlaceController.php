<?php

namespace App\Controller;

use App\Entity\MarketPlace;
use App\Form\MarketPlaceType;
use App\Repository\MarketPlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/market-place')]
final class MarketPlaceController extends AbstractController
{
    #[Route(name: 'app_market_place_index', methods: ['GET'])]
    public function index(MarketPlaceRepository $marketPlaceRepository): Response
    {
        return $this->render('market_place/index.html.twig', [
            'market_places' => $marketPlaceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_market_place_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager ,MailerInterface $mailer): Response
    {
        $marketPlace = new MarketPlace();
        $form = $this->createForm(MarketPlaceType::class, $marketPlace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $apiKey = bin2hex(random_bytes(16)); // Clé API de 32 caractères
            $hashedApiKey = hash('sha256', $apiKey);
            // Sauvegarde de la clé API hachée dans l'entité
            $marketPlace->setApiKey($hashedApiKey);

            // Envoi de la clé API par e-mail
            $email = (new Email())
                ->from('student-apps@esdlyon.dev') //
                ->to('miantamag@gmail.com') // Remplacez par l'adresse souhaitée
                ->subject('Votre clé API')
                ->text('Voici votre clé API : ' . $apiKey);

            try{
                $mailer->send($email);


                // Persist et flush de l'entité dans la base de données
                $entityManager->persist($marketPlace);
                $entityManager->flush();
            } catch (TransportExceptionInterface $e) {
                dd($e->getMessage());
            }
            return $this->redirectToRoute('app_market_place_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('market_place/new.html.twig', [
            'market_place' => $marketPlace,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_market_place_show', methods: ['GET'])]
    public function show(MarketPlace $marketPlace): Response
    {
        return $this->render('market_place/show.html.twig', [
            'market_place' => $marketPlace,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_market_place_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MarketPlace $marketPlace, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MarketPlaceType::class, $marketPlace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_market_place_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('market_place/edit.html.twig', [
            'market_place' => $marketPlace,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_market_place_delete', methods: ['POST'])]
    public function delete(Request $request, MarketPlace $marketPlace, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$marketPlace->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($marketPlace);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_market_place_index', [], Response::HTTP_SEE_OTHER);
    }
}
