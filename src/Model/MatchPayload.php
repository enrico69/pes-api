<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       20/09/2019 (dd-mm-YYYY)
 */

namespace App\Model;

use App\Entity\Match;
use App\Entity\Player;
use App\Entity\Team;
use App\Entity\Stadium;

class MatchPayload
{
    /** @var \App\Entity\Match*/
    private $match;
    /** @var \App\Entity\Stadium*/
    private $stadium;
    /** @var \App\Entity\Player[] */
    private $players = [];
    /** @var \App\Entity\Appearance[] */
    private $appearances = [];
    /** @var \App\Entity\Goal[] */
    private $goals;
    /** @var \App\Entity\Event[] */
    private $events = [];
    /** @var string */
    private $comments;

    /**
     * @return \App\Entity\Match
     */
    public function getMatch() : Match
    {
        return $this->match;
    }

    /**
     * @param \App\Entity\Match $match
     *
     * @return MatchPayload
     */
    public function setMatch(Match $match) : MatchPayload
    {
        $this->match = $match;
        return $this;
    }

    /**
     * @return Stadium
     */
    public function getStadium() : ?Stadium
    {
        return $this->stadium;
    }

    /**
     * @param Stadium $stadium
     * @return MatchPayload
     */
    public function setStadium(Stadium $stadium) : MatchPayload
    {
        $this->stadium = $stadium;
        return $this;
    }

    /**
     * @return \App\Entity\Player[]
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * @param \App\Entity\Player[] $players
     * @return MatchPayload
     */
    public function setPlayers(array $players): MatchPayload
    {
        $this->players = $players;
        return $this;
    }

    /**
     * @return \App\Entity\Appearance[]
     */
    public function getAppearances(): array
    {
        return $this->appearances;
    }

    /**
     * @param \App\Entity\Appearance[] $appearances
     * @return MatchPayload
     */
    public function setAppearances(array $appearances): MatchPayload
    {
        $this->appearances = $appearances;
        return $this;
    }

    /**
     * @return \App\Entity\Goal[]
     */
    public function getGoals(): array
    {
        return $this->goals;
    }

    /**
     * @param \App\Entity\Goal[] $goals
     * @return MatchPayload
     */
    public function setGoals(array $goals): MatchPayload
    {
        $this->goals = $goals;
        return $this;
    }

    /**
     * @return \App\Entity\Event[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * @param \App\Entity\Event[] $events
     * @return MatchPayload
     */
    public function setEvents(array $events): MatchPayload
    {
        $this->events = $events;
        return $this;
    }

    /**
     * @return string
     */
    public function getComments(): string
    {
        return $this->comments;
    }

    /**
     * @param string $comments
     * @return MatchPayload
     */
    public function setComments(string $comments): MatchPayload
    {
        $this->comments = $comments;
        return $this;
    }
}