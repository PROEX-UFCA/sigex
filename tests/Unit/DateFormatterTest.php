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

    public function test_formata_data_com_horario(): void
    {
        $this->assertEquals('2024-12-25', DateFormatter::formatDateSafe('25/12/2024 22:04:45'));
    }

    public function test_retorna_null_para_string_vazia(): void
    {
        $this->assertNull(DateFormatter::formatDateSafe(''));
    }

    public function test_retorna_null_para_null(): void
    {
        $this->assertNull(DateFormatter::formatDateSafe(null));
    }

    public function test_retorna_null_para_string_mal_formatada(): void
    {
        $this->assertNull(DateFormatter::formatDateSafe('2024/25/12'));
        $this->assertNull(DateFormatter::formatDateSafe('2024/25/12 22:23'));
        $this->assertNull(DateFormatter::formatDateSafe('2024-12-25'));
        $this->assertNull(DateFormatter::formatDateSafe('algum_texto'));
    }
}