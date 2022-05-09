<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\SaveOfJourney;
use Doctrine\Persistence\ManagerRegistry;

class ChartController extends AbstractController
{
    #[Route('/chart', name: 'app_chart')]

    public function index(ChartBuilderInterface $chartBuilder, ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(SaveOfJourney::class);
        $dailyProfits = $repository->findAll();

        $horizontal = [];
        $vertical = [];
        foreach ($dailyProfits as $dailyProfit) {
            array_push($horizontal, $dailyProfit->getDate()->format('d/m/Y'));
            array_push($vertical, $dailyProfit->getProfit());
        }

        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => $horizontal,
            'datasets' => [
                'label' => 'Evolution',
                'borderColor' => '#1fc36c',
                'data' => $vertical,
            ]
        ]);

        return $this->render('chart/index.html.twig', [
            'chart' => $chart,
        ]);
    }
}
