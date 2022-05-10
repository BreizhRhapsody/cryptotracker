<?php

namespace App\Controller;

use App\Entity\Crypto;
use App\Entity\SaveOfJourney;
use App\Form\AddType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CallApiService;
use Doctrine\Persistence\ManagerRegistry;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
   
    // Function for the home part and get/use infos from the CoinMarketCap API
 
    public function index(CallApiService $callApiService, ManagerRegistry $doctrine): Response
    {
        // Call to the Crypto repository to get all fields from it

        $repository = $doctrine->getRepository(Crypto::class);
        $cryptos = $repository->findAll();

        // Get result from the Service CallApi and push datas in a string

        $cryptoNameArray = array();
        foreach ($cryptos as $cryptoName) {
            array_push($cryptoNameArray, $cryptoName->getName());
        }
        $string = implode(',', $cryptoNameArray);
        $result = $callApiService->getApi($string);

        // Creation of arrays which get total/quantity of user's transactions and the actual value of cryptocurrencies to be able to make the difference between the purchase value and the current value AND establish the valuation of the wallet

        $actualValue = [];
        $cryptoValue = [];
        foreach ($cryptos as $cryptoEnCours) {
            array_push($cryptoValue, $cryptoEnCours->getTotal());
            array_push($actualValue, $cryptoEnCours->getQte() * $result[$cryptoEnCours->getName()]['quote']['EUR']['price']);
        }
        $userProfit = array_sum($actualValue) - array_sum($cryptoValue);

        // Create a push to database and especially in the SaveOfJourney entity to save datas of the journey

        $saveJourney = $doctrine->getRepository(SaveOfJourney::class);
        $now = new DateTime('now');
        if ($saveJourney->findBy(array('date' => $now)) == null) {
            $manager = $doctrine->getManager();
            $save = new SaveOfJourney();
            $save->setDate($now);
            $save->setProfit(round($userProfit));
            $manager->persist($save);
            $manager->flush();
        }

        // Creation of an alert message if the user database is empty (no transaction yet)

        if ($cryptos == null) {
            $this->addFlash('error', "Mince... Votre portfolio est vide. Pas de panique ! Ajoutez dès à présent votre première transaction en cliquant sur '+'");
        }

        // Return some datas to use it in the home template

        return $this->render('home/index.html.twig', [
            'cryptos' => $cryptos,
            'userProfit' => round($userProfit),
            'result' => $result,
        ]);
    }

    #[Route('/add', name: 'app_add')]

    // Function to get infomations from user into Crypto entity -> Add transaction

    public function addCrypto(ManagerRegistry $doctrine, Request $request): Response
    {
        $crypto = new Crypto();
        $form = $this->createForm(AddType::class, $crypto);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $qte = $crypto->getQte();
            $value = $crypto->getValue();
            $total = $qte * $value;
            $crypto->setTotal($total);
            $manager = $doctrine->getManager();
            $manager->persist($crypto);
            $manager->flush();
            $this->addFlash('success', "Cette transaction a été ajouté avec succès !");
            return $this->redirectToRoute('app_home');
        } else {
            return $this->render('add/index.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }

    #[Route('/delete/{id}', name: 'delete'),]

    // Function to remove data/transaction from the database
    
    public function deleteCrypto(Crypto $crypto = null, ManagerRegistry $doctrine): RedirectResponse
    {
        if ($crypto) {
            $manager = $doctrine->getManager();
            $manager->remove($crypto);
            $manager->flush();
            $this->addFlash('success', "Cette transaction a été supprimé avec succès");
        }
        return $this->redirectToRoute('app_home');
    }
}

