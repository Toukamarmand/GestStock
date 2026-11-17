<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class DateFrenchExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function dateformatFr($date,$format='j F Y à H:i'):string
    {
        // ...
         $formaterDate=$date->format($format);
         $frenchmonths=[
             'January'=>'Janvier',
             'February'=>'Février',
             'March'=>'Mai',
             'April'=>'Avril',
             'May'=>'Mai',
             'June'=>'Juin',
             'July'=>'Juillet',
             'August'=>'Août',
             'September'=>'Septembre',
             'October'=>'Octobre',
             'November'=>'Noovembre',
             'December'=>'Décembre',
         ];
         return str_replace(array_keys($frenchmonths),array_values($frenchmonths),$formaterDate);
    }
}
