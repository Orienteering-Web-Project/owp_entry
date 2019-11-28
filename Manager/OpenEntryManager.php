<?php

namespace Owp\OwpEntry\Manager;

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
use Owp\OwpEntry\Service\PeopleService;

class OpenEntryManager extends AbstractEntryManager
{
    private $service;

    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory, SessionInterface $session, Security $security, Environment $twig, Pdf $pdf, PeopleService $service)
    {
        parent::__construct($em, $formFactory, $session, $security, $twig, $pdf);

        $this->service = $service;
    }

    protected function getFormClass()
    {
        return PeopleType::class;
    }

    protected function getFormData(Event $event)
    {
        $people = new People();
        $people->setEvent($event);

        return $people;
    }

    protected function register(Event $event, $datas)
    {
        $this->service->add($datas);
    }
}
