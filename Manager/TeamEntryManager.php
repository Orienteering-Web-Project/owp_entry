<?php

namespace Owp\OwpEntry\Manager;

use Owp\OwpEvent\Entity\Event;
use Owp\OwpEntry\Entity\Team;
use Owp\OwpEntry\Entity\People;
use Owp\OwpEntry\Form\TeamAddType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use Owp\OwpEntry\Service\TeamService;

class TeamEntryManager extends AbstractEntryManager
{
    private $service;

    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory, SessionInterface $session, Security $security, TeamService $service)
    {
        parent::__construct($em, $formFactory, $session, $security);

        $this->service = $service;
    }

    protected function getFormClass()
    {
        return TeamAddType::class;
    }

    protected function getFormData(Event $event)
    {
        $team = new Team();
        $team->setEvent($event);
        for ($i = 0; $i < $event->getNumberPeopleByEntries(); $i++) {
            $people = new People();
            $people->setEvent($event);
            $people->setPosition($i + 1);
            $team->addPeople($people);
        }

        return $team;
    }

    protected function register(Event $event, $datas)
    {
        $this->service->add($datas);
    }
}
