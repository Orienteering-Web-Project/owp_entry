<?php

namespace Owp\OwpEntry\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Owp\OwpEntry\Entity\Team;
use Owp\OwpEntry\Service\TeamService;

class TeamController extends Controller
{
    public function update(Team $team, TeamService $teamService): Response
    {
        $teamService->update($people);

        return $this->redirectToRoute('owp_event_show', array(
            'slug' => $people->getEvent()->getSlug(),
        ));
    }

    public function delete(Team $people, TeamService $teamService): Response
    {
        $teamService->delete($people);

        return $this->redirectToRoute('owp_event_show', array(
            'slug' => $people->getEvent()->getSlug(),
        ));
    }
}
