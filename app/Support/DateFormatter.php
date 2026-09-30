<?php

namespace App\Support;

use Carbon\Carbon;

class DateFormatter
{
    public static function formatDateSafe($dateString) {
        if (empty($dateString)) return null;
        try {
            return Carbon::createFromFormat('d/m/Y', $dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::createFromFormat('d/m/Y H:i:s', $dateString)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
    }
}