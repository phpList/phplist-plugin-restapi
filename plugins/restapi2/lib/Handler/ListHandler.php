<?php

namespace Rapi\Handler;

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
        \phpList\Entity\ListEntity $listEntity
        , \phpList\ListManager $listManager
        , \phpList\Entity\SubscriberEntity $scrEntity
    )
    {
        $this->listEntity = $listEntity;
        $this->listManager = $listManager;
        $this->scrEntity = $scrEntity;
    }

    public function addSubscriber( $listId, $scrId )
    {
        // Save the id to the subscriber entity
        $this->scrEntity->id = $scrId;

        // Add subscriber to list and return
        // FIXME: swap the order of the arguments here to make them alphabetical
        return $this->listManager->addSubscriber( $this->scrEntity, $listId );

    }
}
