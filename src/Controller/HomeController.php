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
   
    public function index(CallApiService $callApiService, ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Crypto::class);
        $cryptos = $repository->findAll();

        $cryptoNameArray = array();
        foreach ($cryptos as $cryptoName) {
            array_push($cryptoNameArray, $cryptoName->getName());
        }
        $stringCrypto = implode(',', $cryptoNameArray);
        $result = $callApiService->getApi($stringCrypto);

        $actualValue = [];
        $cryptoValue = [];
        foreach ($cryptos as $cryptoEnCours) {
            array_push($cryptoValue, $cryptoEnCours->getTotal());
            array_push($actualValue, $cryptoEnCours->getQte() * $result[$cryptoEnCours->getName()]['quote']['EUR']['price']);
        }
        $userProfit = array_sum($actualValue) - array_sum($cryptoValue);

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

        return $this->render('home/index.html.twig', [
            'cryptos' => $cryptos,
            'userProfit' => round($userProfit),
            'result' => $result,
        ]);
    }

    #[Route('/add', name: 'app_add')]

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

