<?php

namespace Owp\OwpEntry\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Owp\OwpEntry\Entity\People;
use Owp\OwpEvent\Entity\Event;
use Owp\OwpCore\Entity\Club;
use Symfony\Component\HttpFoundation\Request;
use Owp\OwpEvent\Service\EventService;
use Owp\OwpCore\Service\PeopleService;

class EntryController extends Controller
{
    public function entryQuick(string $slug, EventService $eventService, PeopleService $peopleService): Response
    {
        $event = $eventService->get($slug);
        $peopleService->add($this->getUser()->getPeople($event));

        return $this->redirectToRoute('owp_event_show', array(
            'slug' => $event->getSlug(),
        ));
    }

    public function form(string $slug, string $mode, string $option)
    {
        if (!$this->has('manager.entry.' . $mode)) {
            $this->createNotFoundException();
        }

        $event = $this->get('service.event')->get($slug);
        $club = $this->get('service.club')->get($option);

        $form = $this->get('manager.entry.' . $mode)->getForm($event, ['club' => $club]);

        return $this->render('@OwpEntry/Form/form__entry_' . $mode .'.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'option' => $this->get('service.club')->get($option),
            'clubs' => $this->get('service.club')->getBy()
        ]);
    }

    public function submit(Request $request, string $mode, Event $event, string $option)
    {
        $club = $this->get('service.club')->get($option);
        $this->get('manager.entry.' . $mode)->validate($request, $event, ['club' => $club]);

        return $this->redirectToRoute('owp_event_show', array(
            'slug' => $event->getSlug(),
        ));
    }

    public function export(string $slug, string $format, EventService $eventService): Response
    {
        $event = $eventService->get($slug);

        return $this->get('exporter.' . $format)->export($event);
    }
}
