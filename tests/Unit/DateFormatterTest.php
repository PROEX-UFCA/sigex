<?php

namespace Tests\Unit;

use App\Support\DateFormatter;
use PHPUnit\Framework\TestCase;

class DateFormatterTest extends TestCase
{
    public function test_formata_data_no_formato_brasileiro(): void
    {
        $this->assertEquals('2024-12-25', DateFormatter::formatDateSafe('25/12/2024'));
    }
}