<?php

namespace Owp\OwpEntry\Exporter;

use Owp\OwpEvent\Entity\Event;
use Owp\OwpCore\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment;
use Knp\Snappy\Pdf;

class PdfExporterManager extends AbstractExporterManager
{
    private $em;
    private $session;
    private $security;
    private $twig;
    private $pdf;

    public function __construct(EntityManagerInterface $em, SessionInterface $session, Security $security, Environment $twig, Pdf $pdf)
    {
        $this->em = $em;
        $this->session = $session;
        $this->security = $security;
        $this->twig = $twig;
        $this->pdf = $pdf;
    }

    public function export(Event $event)
    {
        if ($event->getNumberPeopleByEntries() > 1) {
            $html = $this->twig->render('Entry/export_pdf__teams.html.twig', array(
                'event'  => $event
            ));
        }
        else {
            $html = $this->twig->render('Entry/export_pdf__individuals.html.twig', array(
                'event'  => $event
            ));
        }


        return new PdfResponse(
            $this->pdf->getOutputFromHtml($html, array(
                'encoding' => 'utf-8'
            )),
            'file.pdf'
        );
    }

}
