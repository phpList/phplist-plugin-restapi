<?php

namespace Rapi\Handler;

use phpList\Entity\ListEntity;
use phpList\Entity\SubscriberEntity;
use phpList\ListManager;

/**
* Class to handle API calls to ListManager{}
*/
class ListHandler
{
    /**
    * Set object properties
    * @param ListEntity $scrEntity
    * @param ListManager $listManager
    */
    public function __construct(
        ListEntity $listEntity,
        ListManager $listManager,
        SubscriberEntity $scrEntity
    )
    {
        $this->listEntity = $listEntity;
        $this->listManager = $listManager;
        $this->scrEntity = $scrEntity;
    }

    /**
     * @param int $listId
     * @param int $scrId
     *
     * @return \PDOStatement|null
     */
    public function addSubscriber( $listId, $scrId )
    {
        // Save the id to the subscriber entity
        $this->scrEntity->id = $scrId;

        // Add subscriber to list and return
        // FIXME: swap the order of the arguments here to make them alphabetical
        return $this->listManager->addSubscriber( $this->scrEntity, $listId );

    }
}
