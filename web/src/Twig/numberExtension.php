<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class numberExtension extends AbstractExtension {
    public function getFilters(){
        return 
        [
            new TwigFilter('isInteger',
            [
                $this, 'isInteger'
            ])
        ];
    }

    public function isInteger($value){
        if(is_numeric($value)) return true;
        else return false;
    }
}