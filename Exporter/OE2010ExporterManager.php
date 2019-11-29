<?php

namespace Owp\OwpEntry\Exporter;

use Owp\OwpEvent\Entity\Event;
use Owp\OwpEntry\Entity\Team;
use Owp\OwpEntry\Entity\People;
use Owp\OwpCore\Entity\User;
use Owp\OwpCore\Entity\Club;
use Owp\OwpEntry\Form\TeamAddType;
use Owp\OwpEntry\Form\PeopleAddType;
use Owp\OwpEntry\Form\ClubType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment;
use Knp\Snappy\Pdf;
use Owp\OwpEntry\Service\PeopleService;
use Sonata\Exporter\Writer\CsvWriter;
use Knp\Bundle\SnappyBundle\Snappy\Response\SnappyResponse;

class OE2010ExporterManager extends AbstractExporterManager
{
    public function export(Event $event)
    {
        
    }

    private function header()
    {
        return [
            'Doss.',
            'X Nr',
            'Nr puce',
            'Ident. base de données',
            'NOM',
            'Prénom',
            'Né',
            'S',
            'Plage',
            'nc',
            'H. Dép.',
            'Arrivée',
            'Temps',
            'Classer',
            'Bonification -',
            'Pénalité +',
            'Commentaire',
            'Nr club',
            'Nom club',
            'Ville',
            'Nat',
            'Groupe',
            'Région',
            'Nr catg.',
            'Catg. Court',
            'Catg. Long',
            'Nr Catg. Inscription',
            'Catégorie d\'inscription (court)',
            'Catégorie d\'inscription (long)',
            'Ranking',
            'Points Ranking',
            'Num1',
            'Num2',
            'Num3',
            'Text1',
            'Text2',
            'Text3',
            'Adr. nom de famille',
            'Adr. prénom',
            'Rue',
            'Ligne2',
            'CP',
            'Adr. ville',
            'Tél.',
            'Mobile',
            'Fax',
            'Email',
            'Louée',
            'Tarif inscription',
            'Payé',
            'Equipe',
            'Nr circuit',
            'Circuit',
            'km',
            'm',
            'Postes du circuit'
        ];
    }

}
