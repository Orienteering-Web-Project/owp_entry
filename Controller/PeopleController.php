<?php

namespace Owp\OwpEntry\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Owp\OwpEntry\Entity\People;
use Owp\OwpEvent\Entity\Event;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Owp\OwpEntry\Service\EntryService;
use Owp\OwpEvent\Service\EventService;
use Owp\OwpEntry\Service\PeopleService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class PeopleController extends Controller
{
    public function update(Request $request, People $people, PeopleService $peopleService): Response
    {
        $form = $peopleService->update($request, $people);

        return $this->render('@OwpEntry/Form/form__people_update.html.twig', [
            'form' => $form->createView(),
            'people' => $people
        ]);
    }

    public function delete(People $people, PeopleService $peopleService): Response
    {
        $peopleService->delete($people);

        return $this->redirectToRoute('owp_event_show', array(
            'slug' => $people->getEvent()->getSlug(),
        ));
    }
}
