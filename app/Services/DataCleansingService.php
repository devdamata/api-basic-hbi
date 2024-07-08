<?php

namespace App\Services;

class DataCleansingService
{


    public function escapeArray(array $data)
    {
        return array_map(function($value) {
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }, $data);
    }

}