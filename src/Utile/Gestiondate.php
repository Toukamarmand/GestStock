<?php

namespace App\Utile;

class Gestiondate
{
    private \DateTime $dateTime;
    public  function __construct(\DateTime $dateTime=null){
        $this->dateTime=$dateTime ?? new \DateTime();
    }
    public function soustraireannees($date=365):\DateTime
    {
        $intervale=new \DateInterval("P{$date}D");
        $this->dateTime->sub($intervale);
        return $this->dateTime;
    }
    public function soustrairemois($date=30):\DateTime
    {
        $intervale=new \DateInterval("P{$date}D");
        $this->dateTime->sub($intervale);
        return $this->dateTime;
    }
    public function soustrairesemaine($date=7):\DateTime
    {
        $intervale=new \DateInterval("P{$date}D");
        $this->dateTime->sub($intervale);
        return $this->dateTime;
    }
    public function ajoutsemaine($date=7):\DateTime
    {
        $intervale=new \DateInterval("P{$date}D");
        $this->dateTime->add($intervale);
        return $this->dateTime;
    }
}