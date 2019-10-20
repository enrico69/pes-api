<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       06/09/2019 (dd-mm-YYYY)
 */

namespace App\Model;


use App\Entity\Gamer;
use App\Entity\Team;

class GamerTeamAssociation
{
    /** @var \App\Entity\Gamer */
    private $gamer;

    /** @var \App\Entity\Team */
    private $team;

    public function __construct(Gamer $gamer, Team $team)
    {
        $this->gamer = $gamer;
        $this->team = $team;
    }

    /** @return \App\Entity\Gamer ß*/
    public function getGamer(): Gamer
    {
        return $this->gamer;
    }

    /** @return \App\Entity\Team */
    public function getTeam(): Team
    {
        return $this->team;
    }
}
