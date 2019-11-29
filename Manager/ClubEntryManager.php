<?php

namespace Owp\OwpEntry\Manager;

use Owp\OwpEvent\Entity\Event;
use Owp\OwpEntry\Entity\People;
use Owp\OwpCore\Entity\Club;
use Owp\OwpEntry\Form\ClubType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use Owp\OwpEntry\Service\PeopleService;

class ClubEntryManager extends AbstractEntryManager
{
    private $service;

    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory, SessionInterface $session, Security $security, PeopleService $service)
    {
        parent::__construct($em, $formFactory, $session, $security);

        $this->service = $service;
    }

    protected function getFormClass()
    {
        return ClubType::class;
    }

    protected function getFormData(Event $event)
    {
        return [];
    }

    protected function register(Event $event, $datas)
    {
        $entries = $datas['id']->toArray();

        foreach ($entries as $entry) {
            $people = new People();
            $people
                ->setEvent($event)
                ->setBase($entry)
            ;

            $this->service->add($people);
        }
    }

    protected function getFormOptions($options)
    {
        $options['club'] = isset($options['club']) ? $options['club']->getId() : $_ENV['CLUB'];

        return $options;
    }
}
