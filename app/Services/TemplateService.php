<?php

namespace App\Services;

class TemplateService
{
    public function render($content, $data = [])
    {
        return str_replace(
            array_keys($data),
            array_values($data),
            $content
        );
    }
}