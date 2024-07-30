<?php

namespace App\Csv;

use DateTime;

class CsvBuilder
{
    private string $dateTimeFormat;
    public function __construct()
    {
        $this->dateTimeFormat = 'd-m-Y';
    }
    public function buildCsv(): string
    {
        return $this->arrayToCsv($this->buildPayrollArray(), 'payment_dates.csv');
    }

    private function buildPayrollArray(): array
    {
        $currentDate = New DateTime;
        $currentMonth = (int) $currentDate->format('n');
        $remainingMonths = [];

        for ($month = $currentMonth; $month <= 12; $month++) {
            $monthName = date('F', mktime(0, 0, 0, $month, 1));
            $bonusDate = $this->determineBonusDate($month);
            $salaryDate = $this->determineSalaryDate($month);

            $remainingMonths[] = [
                'month' => $monthName,
                'bonus_date' => $bonusDate->format($this->dateTimeFormat),
                'salary_date' => $salaryDate->format($this->dateTimeFormat)
            ];
        }

        return $remainingMonths;
    }

    private function arrayToCsv(array $payrollArray, string $csvName): string
    {
        $filePath = sys_get_temp_dir() . '/' . $csvName;
        $file = fopen($filePath, 'w');

        fputcsv($file, array_keys($payrollArray[0]));

        foreach ($payrollArray as $row) {
            fputcsv($file, $row);
        }

        fclose($file);

        return $filePath;
    }

    private function determineBonusDate(string $month): DateTime
    {
        $bonusDate = date($this->dateTimeFormat, mktime(0, 0, 0, $month, 15));
        $bonusDateTime = new DateTime($bonusDate);

        if ($this->isSaturday($bonusDateTime)) {
            $this->addDays($bonusDateTime, 4);
        } elseif ($this->isSunday($bonusDateTime)) {
            $this->addDays($bonusDateTime, 3);
        }

        return $bonusDateTime;
    }

    private function determineSalaryDate(string $month): DateTime
    {
        $salaryDate = date($this->dateTimeFormat, mktime(0, 0, 0, $month, date('t', mktime(0, 0, 0, $month, 1))));
        $salaryDateTime = new DateTime($salaryDate);

        if ($this->isSaturday($salaryDateTime)) {
            $this->addDays($salaryDateTime, 2);
        } elseif ($this->isSunday($salaryDateTime)) {
            $this->addDays($salaryDateTime, 1);
        }

        return $salaryDateTime;
    }

    private function addDays(DateTime $date, int $days ): void
    {
        $date->modify('+' . $days . ' days');

    }

    private function isSaturday(DateTime $dateTime): bool
    {
        if ($dateTime->format('l') === "Saturday") {
            return true;
        }

        return false;
    }

    private function isSunday(DateTime $dateTime): bool
    {
        if ($dateTime->format('l') === "Sunday") {
            return true;
        }

        return false;
    }
}