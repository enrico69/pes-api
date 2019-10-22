<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       07/09/2019 (dd-mm-YYYY)
 */

namespace App\Factory;

use App\Entity\Tournament;

class TournamentFactory
{
    /**
     * @param string $type
     *
     * @return \App\Entity\Tournament
     *
     * @throws \Exception
     */
    public function generate(string $type)
    {
        $tournament = new Tournament();
        $tournament->setCreatedAt(new \DateTime());

        switch ($type) {
            case Tournament::TYPE_CALCIO:
                $tournament->setType(Tournament::TYPE_CALCIO);
                break;
            case Tournament::TYPE_CALCIO_CUP:
                $tournament->setType(Tournament::TYPE_CALCIO_CUP);
                break;
            default:
                throw new \RuntimeException("Tournament type '{$type}' is unknown!");
        }
        $tournament->setCreatedAt(new \DateTime());

        return $tournament;
    }
}
