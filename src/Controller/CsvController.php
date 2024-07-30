<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use App\Csv\CsvBuilder;

class CsvController extends AbstractController
{
    private CsvBuilder $csvBuilder;

    public function __construct()
    {
        $this->csvBuilder = new CsvBuilder();
    }

    #[Route('/')]
    public function downloadCSV(): Response
    {
        $csvName = 'payment_dates.csv';
        $response = new BinaryFileResponse($this->csvBuilder->buildCsv($csvName));
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $csvName
        );

        return $response;
    }
}