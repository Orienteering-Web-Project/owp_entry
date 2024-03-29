<?php

namespace Owp\OwpEntry\Service;

use Owp\OwpEvent\Entity\Event;
use Owp\OwpEntry\Entity\Team;
use Owp\OwpEntry\Entity\People;
use Owp\OwpCore\Entity\User;
use Owp\OwpEntry\Form\TeamAddType;
use Owp\OwpEntry\Form\PeopleAddType;
use Owp\OwpEntry\Form\PeopleUpdateType;
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

class PeopleService {

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

    public function add(People $people)
    {
        if ($this->security->isGranted('add', $people)) {
            $this->em->persist($people);
            $this->em->flush();

            $this->session->getFlashBag()->add('primary', 'L\'inscription a été prise en compte.');
        }
        else {
            $this->session->getFlashBag()->add('danger', 'Vous n\'êtes pas autorisé à vous inscrire à cet événement.');
        }
    }

    public function delete(People $people)
    {
        if ($this->security->isGranted('delete', $people)) {
            $this->em->remove($people);
            $this->em->flush();

            $this->session->getFlashBag()->add('primary', 'L\'inscription a bien été supprimée.');
        }
        else {
            $this->session->getFlashBag()->add('danger', 'Vous n\'êtes pas autorisé à supprimer cette inscription.');
        }
    }

    public function update(Request $request, People $people)
    {
        if (!$this->security->isGranted('update', $people)) {
            throw new AccessDeniedException();
        }

        $form = $this->formFactory->create(PeopleUpdateType::class, $people);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $people = $form->getData();

            $this->em->persist($people);
            $this->em->flush();

            $this->session->getFlashBag()->add('primary', 'L\'inscription a été prise en compte.');
        }

        return $form;
    }
}
