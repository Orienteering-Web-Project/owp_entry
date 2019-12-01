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
use Knp\Bundle\SnappyBundle\Snappy\Response\SnappyResponse;
use Sonata\Exporter\Handler;
use Sonata\Exporter\Source\SourceIteratorInterface;
use Sonata\Exporter\Writer\CsvWriter;
use Sonata\Exporter\Writer\JsonWriter;
use Sonata\Exporter\Writer\XlsWriter;
use Sonata\Exporter\Writer\XmlWriter;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Sonata\Exporter\Source\ArraySourceIterator;

class OE2010ExporterManager extends AbstractExporterManager
{
    public function export(Event $event)
    {
        $writer = new CsvWriter('php://output', ';', '"', '\\', false, true);
        $contentType = 'text/csv';

        $source = new ArraySourceIterator($this->data($event));

        $callback = static function () use ($source, $writer) {
            $handler = Handler::create($source, $writer);
            $handler->export();
        };

        return new StreamedResponse($callback, 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => sprintf('attachment; filename="%s"', 'export_oe2010.csv'),
        ]);
    }

    private function data(Event $event)
    {
        $data = [];
        $data[] = $this->header();

        foreach($event->getPeoples() as $people) {
            $data[] = [
                "",
                "",
                "",
                !(empty($people->getBase())) ? $people->getBase()->getSi() : "", // Puce
                !(empty($people->getBase())) ? $people->getBase()->getId() : "",
                !(empty($people->getBase())) ? $people->getBase()->getLastName() : $people->getLastName(), // Nom
                !(empty($people->getBase())) ? $people->getBase()->getFirstName() : $people->getFirstName(), // Prénom
                "", // Né
                "", // Sexe
                0,
                0,
                "",
                "",
                "",
                0,
                "",
                "",
                "",
                !(empty($people->getBase())) ? $people->getBase()->getClub()->getId() : "10000", // Numéro club
                !(empty($people->getBase())) ? $people->getBase()->getClub()->getName() : "10000PO", // Nom club
                !(empty($people->getBase())) ? $people->getBase()->getClub()->getLabel() : "Pass-Orientation", // Nom complet
                "FR", // Nationalité
                "",
                "",
                "", // Numéro de catégorie
                "", // Libellé de catégorie
                "", // Libellé de catégorie
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                "",
                ""
            ];
        }

        return $data;
    }

    private function header()
    {
        return [
            "OE0001",
            "Doss.",
            "X Nr",
            "Nr puce",
            "Ident. base de données",
            "NOM",
            "Prénom",
            "Né",
            "S",
            "Plage",
            "nc",
            "Départ",
            "Arrivée",
            "Temps",
            "Classer",
            "Bonification -",
            "Pénalité +",
            "Commentaire",
            "Nr club",
            "Nom club",
            "Ville",
            "Nat",
            "Groupe",
            "Région",
            "Nr catg.",
            "Catg. Court",
            "Catg. Long",
            "Nr Catg. Inscription",
            "Catégorie d\'inscription (court)",
            "Catégorie d\'inscription (long)",
            "Ranking",
            "Points Ranking",
            "Num1",
            "Num2",
            "Num3",
            "Text1",
            "Text2",
            "Text3",
            "Adr. nom de famille",
            "Adr. prénom",
            "Rue",
            "Ligne2",
            "CP",
            "Adr. ville",
            "Tél.",
            "Mobile",
            "Fax",
            "Email",
            "Louée",
            "Tarif inscription",
            "Payé",
            "Equipe",
            "Nr circuit",
            "Circuit",
            "km",
            "m",
            "Postes du circuit"
        ];
    }

}
