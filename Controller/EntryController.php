<?php

namespace Owp\OwpEntry\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Owp\OwpEntry\Entity\Team;
use Owp\OwpEntry\Entity\People;
use Owp\OwpEvent\Entity\Event;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Owp\OwpEntry\Service\EntryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class EntryController extends AbstractController
{
    /**
     * @Route("/entry/quick/{id}", name="owp_entry_quick", requirements={"page"="\d+"})
     * @IsGranted("ROLE_USER")
     * @ParamConverter("event")
     */
    public function quick(Event $event, EntryService $entryService): Response
    {
        $entryService->save($this->getUser()->getPeople($event));

        return $this->redirectToRoute('owp_event_show', array(
            'slug' => $event->getSlug(),
        ));
    }

    /**
     * @Route("/team/{id}/update", name="owp_team_update", requirements={"page"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function updateTeam(Team $team, EntryService $entryService): Response
    {
        $entryService->updateTeam($team);

        return $this->redirectToRoute('owp_event_show', array(
            'slug' => $team->getEvent()->getSlug(),
        ));
    }

    /**
     * @Route("/people/{id}/update", name="owp_people_update", requirements={"page"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function updatePeople(People $people, EntryService $entryService): Response
    {
        $entryService->updatePeople($people);

        return $this->redirectToRoute('owp_event_show', array(
            'slug' => $people->getEvent()->getSlug(),
        ));
    }

    /**
     * @Route("/team/{id}/delete", name="owp_team_delete", requirements={"page"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function deleteTeam(Team $team, EntryService $entryService): Response
    {
        $entryService->deleteTeam($team);

        return $this->redirectToRoute('owp_event_show', array(
            'slug' => $team->getEvent()->getSlug(),
        ));
    }

    /**
     * @Route("/people/{id}/delete", name="owp_people_delete", requirements={"page"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function deletePeople(People $people, EntryService $entryService): Response
    {
        $entryService->delete($people);

        return $this->redirectToRoute('owp_event_show', array(
            'slug' => $people->getEvent()->getSlug(),
        ));
    }

    /**
     * @Route("/entry/{id}/export/{format}", name="owp_entry_export", requirements={"page"="\d+"})
     */
    public function export(Event $event, $format, EntryService $entryService): Response
    {
        return $entryService->export($event, $format);
    }
}
