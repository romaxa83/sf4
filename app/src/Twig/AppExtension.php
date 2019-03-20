<?php

namespace App\Twig;

use App\Entity\LikeNotification;
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

    public function getTests()
    {
        return [
            new \Twig_SimpleTest(
                'like',
                function ($obj) { return $obj instanceof LikeNotification;}
            )
        ];
    }
}