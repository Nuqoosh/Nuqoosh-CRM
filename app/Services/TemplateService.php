<?php

namespace App\Services;

/**
 * Class TemplateService
 * @package App\Services
 * 
 * Handles template rendering with placeholders
 */
class TemplateService
{
    /**
     * Replace placeholders in template content
     * 
     * @param string $content
     * @param array $data
     * @return string
     */
    public function render(string $content, array $data): string
    {
        $search = array_keys($data);
        $replace = array_values($data);
        
        return str_replace($search, $replace, $content);
    }
}