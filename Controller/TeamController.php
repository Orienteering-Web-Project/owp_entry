<?php

namespace Owp\OwpEntry\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Owp\OwpEntry\Entity\Team;
use Owp\OwpEntry\Service\TeamService;
use Symfony\Component\HttpFoundation\Request;

class TeamController extends Controller
{
    public function update(Request $request, Team $team, TeamService $teamService): Response
    {
        $form = $teamService->update($request, $team);

        return $this->render('@OwpEntry/Form/form__team_update.html.twig', [
            'form' => $form->createView(),
            'team' => $team
        ]);
    }

    public function delete(Team $people, TeamService $teamService): Response
    {
        $teamService->delete($people);

        return $this->redirectToRoute('owp_event_show', array(
            'slug' => $people->getEvent()->getSlug(),
        ));
    }
}
