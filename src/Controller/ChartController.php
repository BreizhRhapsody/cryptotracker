<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\SaveOfJourneyRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;


class ChartController extends AbstractController
{
    #[Route('/chart', name: 'app_chart')]

    public function index(ChartBuilderInterface $chartBuilder, SaveOfJourneyRepository $repository): Response
    {
        $saves = $repository->findAll();
        
        $ordinate = [];
        $abscissa = [];
        

        foreach ($saves as $save) {
            array_push($ordinate, $save->getDate()->format('d/m/Y'));
            array_push($abscissa, $save->getProfit());
        }


        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([

            'labels' => $ordinate,
            'datasets' => [
                [
                    'label' => 'Evolution',
                    'borderColor' => '#1fc36c',
                    'data' => $abscissa,

                ],
            ]
        ]);


           
        return $this->render('chart/index.html.twig', [
            'chart'=>$chart,
        ]);


    }
}
