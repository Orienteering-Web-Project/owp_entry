<?php

namespace Owp\OwpEntry\Service;

use Owp\OwpEvent\Entity\Event;
use Owp\OwpEntry\Entity\Team;
use Owp\OwpEntry\Entity\People;
use Owp\OwpCore\Entity\User;
use Owp\OwpCore\Entity\Club;
use Owp\OwpEntry\Form\TeamType;
use Owp\OwpEntry\Form\PeopleType;
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

class EntryService {

    const REGISTER_OPEN = 'open';
    const REGISTER_TEAM = 'team';
    const REGISTER_CLUB = 'club';

    const EXPORT_PDF = 'pdf';

    private $em;
    private $formFactory;
    private $session;
    private $security;
    private $twig;
    private $pdf;

    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory, SessionInterface $session, Security $security, Environment $twig, Pdf $pdf)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->session = $session;
        $this->security = $security;
        $this->twig = $twig;
        $this->pdf = $pdf;
    }

    public function getForm(Request $request, string $mode, Event $event, Club $club = null)
    {
        if (!$this->security->isGranted('register_' . $mode, $event)) {
            throw new AccessDeniedException();
        }

        switch($mode)
        {
            case self::REGISTER_OPEN:
                return $this->getFormOpen($request, $event);
            case self::REGISTER_TEAM:
                return $this->getFormTeam($request, $event);
            case self::REGISTER_CLUB:
                return $this->getFormClub($request, $event, $club);
        }

        throw new RouteNotFoundException();
    }

    private function getFormOpen(Request $request, Event $event)
    {
          $form = $this->formFactory->create(PeopleType::class, new People());

          $form->handleRequest($request);
          if ($form->isSubmitted() && $form->isValid()) {
              $entity = $form->getData();
              $this->save($entity);

              return NULL;
          }

          return $form;
    }

    private function getFormClub(Request $request, Event $event, Club $club)
    {
        $form = $this->formFactory->create(ClubType::class, [], [
            'club' => $club ? $club->getId() : $_ENV['CLUB']]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entries = $form->getData();

            foreach ($entries as $entry) {
                $people = new People();
                $people
                    ->setEvent($event)
                    ->setBase($entry->current())
                ;
                $this->save($people);
            }

            return NULL;
        }

        return $form;
    }

    private function getFormTeam(Request $request, Event $event)
    {
        $team = new Team();
        $team->setEvent($event);
        for ($i = 0; $i < $event->getNumberPeopleByEntries(); $i++) {
            $people = new People();
            $people->setPosition($i + 1);
            $team->addPeople($people);
        }

        $form = $this->formFactory->create(TeamType::class, $team);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity = $form->getData();
            $this->save($entity);

            return NULL;
        }

        return $form;
    }

    public function delete($entry)
    {
        if (!($entry instanceof Team) && !($entry instanceof People)) {
            $this->session->getFlashBag()->add('danger', 'Une erreur inattendue est survenue.');
        }
        elseif ($this->security->isGranted('delete', $entry)) {
            $this->em->remove($entry);
            $this->em->flush();

            $this->session->getFlashBag()->add('primary', 'L\'inscription a bien été supprimée.');
        }
        else {
            $this->session->getFlashBag()->add('danger', 'Vous n\'êtes pas autorisé à supprimer cette inscription.');
        }
    }

    public function export(Event $event, $format)
    {
        switch($format)
        {
            case self::EXPORT_PDF:
                return $this->exportPDF($event);
        }

        return new RouteNotFoundException();
    }

    public function save($entry)
    {
        if (!($entry instanceof Team) && !($entry instanceof People)) {
            $this->session->getFlashBag()->add('danger', 'Une erreur inattendue est survenue.');
        }
        elseif ($this->security->isGranted('add', $entry)) {
            $this->em->persist($entry);
            $this->em->flush();

            $this->session->getFlashBag()->add('primary', 'L\'inscription a été prise en compte.');
        }
        else {
            $this->session->getFlashBag()->add('danger', 'Vous n\'êtes pas autorisé à vous inscrire à cet événement.');
        }
    }

    private function exportPDF(Event $event)
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
