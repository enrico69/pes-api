<?php
/**
 * @author     Eric COURTIAL <e.courtial30@gmail.com>
 * @date       07/09/2019 (dd-mm-YYYY)
 */

namespace App\Factory;

use App\Model\GamerTeamAssociation;
use App\Model\GamerTeamAssociationCollection;

class GamerTeamAssociationCollectionFactory
{
    /** @var \App\Factory\GamerCollectionFactory */
    private $gamerCollectionFactory;
    /** @var \App\Factory\TeamCollectionFactory */
    private $teamCollectionFactory;

    public function __construct(GamerCollectionFactory $gamerCollectionFactory, TeamCollectionFactory $teamCollectionFactory)
    {
        $this->gamerCollectionFactory = $gamerCollectionFactory;
        $this->teamCollectionFactory = $teamCollectionFactory;
    }

    /**
     * @param array $associations where the key is the gamer id and the value is the team id
     *
     * @return \App\Model\GamerTeamAssociationCollection
     */
    public function createFromAssociativeArray(array $associations): GamerTeamAssociationCollection
    {
        $gamerCollection = $this->gamerCollectionFactory->createFromArray(\array_keys($associations));
        $teamCollection = $this->teamCollectionFactory->createFromArray(\array_values($associations));
        $associationCollection = new GamerTeamAssociationCollection();

        foreach ($associations as $gamerId => $teamId) {
            $association = new GamerTeamAssociation(
                $gamerCollection[$gamerId],
                $teamCollection[$teamId]
            );

            $associationCollection->add($association);
        }

        return $associationCollection;
    }
}
