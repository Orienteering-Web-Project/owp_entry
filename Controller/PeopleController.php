<?php

namespace Owp\OwpEntry\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Owp\OwpEntry\Entity\People;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Owp\OwpEntry\Service\EntryService;
use Owp\OwpEvent\Service\EventService;
use Owp\OwpEntry\Service\PeopleService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PeopleController extends Controller
{
    public function update(People $people, PeopleService $peopleService): Response
    {
        $peopleService->update($people);

        return $this->redirectToRoute('owp_event_show', array(
            'slug' => $people->getEvent()->getSlug(),
        ));
    }

    public function delete(People $people, PeopleService $peopleService): Response
    {
        $peopleService->delete($people);

        return $this->redirectToRoute('owp_event_show', array(
            'slug' => $people->getEvent()->getSlug(),
        ));
    }
}
