<?php

namespace Owp\OwpEntry\Service;

use Owp\OwpEvent\Entity\Event;
use Owp\OwpEntry\Entity\Team;
use Owp\OwpEntry\Entity\People;
use Owp\OwpCore\Entity\User;
use Owp\OwpEntry\Form\TeamAddType;
use Owp\OwpEntry\Form\TeamUpdateType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use SSymfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment;
use Knp\Snappy\Pdf;

class TeamService {

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

    public function add(Team $team)
    {
        if ($this->security->isGranted('add', $team)) {
            $this->em->persist($team);
            $this->em->flush();

            $this->session->getFlashBag()->add('primary', 'L\'inscription a été prise en compte.');
        }
        else {
            $this->session->getFlashBag()->add('danger', 'Vous n\'êtes pas autorisé à vous inscrire à cet événement.');
        }
    }

    public function delete(Team $team)
    {
        if ($this->security->isGranted('delete', $team)) {
            $this->em->remove($team);
            $this->em->flush();

            $this->session->getFlashBag()->add('primary', 'L\'inscription a bien été supprimée.');
        }
        else {
            $this->session->getFlashBag()->add('danger', 'Vous n\'êtes pas autorisé à supprimer cette inscription.');
        }
    }

    public function update(Request $request, Team $team)
    {
        if (!$this->security->isGranted('update', $team)) {
            throw new AccessDeniedException();
        }

        $form = $this->formFactory->create(TeamUpdateType::class, $team);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $team = $form->getData();

            $this->em->persist($team);
            $this->em->flush();

            $this->session->getFlashBag()->add('primary', 'L\'inscription a été prise en compte.');
        }

        return $form;
    }
}
