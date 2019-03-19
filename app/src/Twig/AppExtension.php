<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

//реализация собственного фильтра для twig
class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
          new TwigFilter('price',[$this,'priceFilter'])
        ];
    }

    public function priceFilter($number)
    {
        return '$' . number_format($number,2,'.',',');
    }
}