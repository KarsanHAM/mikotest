<?php


namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Csv\CsvBuilder;

class CsvController
{
    private CsvBuilder $csvBuilder;

    public function __construct()
    {
        $this->csvBuilder = new CsvBuilder();
    }

    #[Route('/')]
    public function downloadCsv(): Response
    {
        $data = $this->csvBuilder->buildCsv();

        return new Response(
            $data
        );
    }
}