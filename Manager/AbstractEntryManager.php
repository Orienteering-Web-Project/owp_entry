<?php

namespace Owp\OwpEntry\Manager;

use Owp\OwpEvent\Entity\Event;
use Owp\OwpEntry\Entity\Team;
use Owp\OwpEntry\Entity\People;
use Owp\OwpCore\Entity\User;
use Owp\OwpCore\Entity\Club;
use Owp\OwpEntry\Form\TeamType;
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

abstract class AbstractEntryManager
{
    private $em;
    private $formFactory;
    private $session;
    private $security;
    private $twig;

    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory, SessionInterface $session, Security $security, Environment $twig, Pdf $pdf)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->session = $session;
        $this->security = $security;
        $this->twig = $twig;
        $this->pdf = $pdf;
    }

    public function getForm(Event $event, array $options = [])
    {
        $options['event'] = $event->getId();

        return $this->formFactory->create($this->getFormClass(), $this->getFormData($event), $this->getFormOptions($options));
    }

    public function validate(Request $request, Event $event, array $options)
    {
        $form = $this->getForm($event, $options);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->register($event, $form->getData());
        }
    }

    protected function getFormOptions($options)
    {
        return $options;
    }

    abstract protected function getFormClass();

    abstract protected function getFormData(Event $event);

    abstract protected function register(Event $event, $datas);
}
