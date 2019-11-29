<?php

namespace Owp\OwpEntry\Manager;

use Owp\OwpEvent\Entity\Event;
use Owp\OwpEntry\Entity\People;
use Owp\OwpEntry\Form\PeopleAddType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use Owp\OwpEntry\Service\PeopleService;

class OpenEntryManager extends AbstractEntryManager
{
    private $service;

    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory, SessionInterface $session, Security $security, PeopleService $service)
    {
        parent::__construct($em, $formFactory, $session, $security);

        $this->service = $service;
    }

    protected function getFormClass()
    {
        return PeopleAddType::class;
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
