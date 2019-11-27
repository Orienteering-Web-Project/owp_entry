<?php

namespace Owp\OwpEntry\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Owp\OwpEntry\Entity\Team;
use Owp\OwpEntry\Entity\People;
use Owp\OwpEvent\Entity\Event;
use Owp\OwpCore\Entity\Club;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Owp\OwpEntry\Service\EntryService;
use Owp\OwpEvent\Service\EventService;
use Owp\OwpCore\Service\ClubService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class EntryController extends Controller
{
    public function entryQuick(string $slug, EventService $eventService, EntryService $entryService): Response
    {
        $event = $eventService->get($slug);
        $entryService->save($this->getUser()->getPeople($event));

        return $this->redirectToRoute('owp_event_show', array(
            'slug' => $event->getSlug(),
        ));
    }

    public function entryOpen(Request $request, string $slug, EventService $eventService, EntryService $entryService): Response
    {
        $event = $eventService->get($slug);

        $form = $entryService->getForm($request, 'open', $event);
        if (!$form) {
            return $this->redirectToRoute('owp_event_show', array(
                'slug' => $event->getSlug(),
            ));
        }

        return $this->render('@OwpEntry/Form/form__entry_open.html.twig', [
            'form' => $form->createView(),
            'event' => $event
        ]);
    }

    public function entryTeam(Request $request, string $slug, Club $club, EventService $eventService, EntryService $entryService): Response
    {
        $event = $eventService->get($slug);

        $form = $entryService->getForm($request, 'team', $event);
        if (!$form) {
            return $this->redirectToRoute('owp_event_show', array(
                'slug' => $event->getSlug(),
            ));
        }

        return $this->render('@OwpEntry/Form/form__entry_team.html.twig', [
            'form' => $form->createView(),
            'event' => $event
        ]);
    }

    public function entryClub(Request $request, string $slug, string $club, EventService $eventService, EntryService $entryService, ClubService $clubService): Response
    {
        $event = $eventService->get($slug);
        $club = $clubService->get($club);

        $form = $entryService->getForm($request, 'club', $event, $club);
        if (!$form) {
            return $this->redirectToRoute('owp_event_show', array(
                'slug' => $event->getSlug(),
            ));
        }

        return $this->render('@OwpEntry/Form/form__entry_club.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'current_club' => $club,
            'clubs' => $clubService->getBy()
        ]);
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

    public function export(string $slug, $format, EventService $eventService, EntryService $entryService): Response
    {
        $event = $eventService->get($slug);

        return $entryService->export($event, $format);
    }
}
