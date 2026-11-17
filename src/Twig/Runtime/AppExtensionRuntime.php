<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class AppExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function formaterNombre($value,$thousanqSeparetor=" "): string
    {
        //Filtre avec parametres
        return number_format($value,0,',',$thousanqSeparetor);
        //Filtre sans parametres
        //return number_format($value,0,","," ");
    }
}
