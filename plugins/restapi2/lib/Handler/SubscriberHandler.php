<?php

namespace Rapi\Handler;

/**
 * Class to handle API calls to SubscriberManager{}
 */
class SubscriberHandler
 {
    /**
     * Set object properties
     * @param SubscriberEntity $scrEntity
     * @param SubscriberManager $subscriberManager
     */
     public function __construct(
        \phpList\Entity\SubscriberEntity $scrEntity
        , \phpList\SubscriberManager $subscriberManager )
     {
         $this->scrEntity = $scrEntity;
         $this->subscriberManager = $subscriberManager;
     }

     /**
      * Insert a new subscriber with complete subscriber details
      * @return int ID of new subscriber
      */
     public function add(
        $blacklisted = 0
        , $bounceCount = 0
        , $confirmed = 1
        , $disabled = 0
        , $emailAddress = ""
        , $encPass = ""
        , $entered = ""
        , $extraData = ""
        , $foreigKkey = ""
        , $htmlEmail = 1
        , $modified = ""
        , $optedIn = 0
        , $passwordChanged = ""
        , $plainPass = ""
        , $plainPasschanged = ""
        , $rssFrequency = ""
        , $subscribePage = ""
    )
     {
        // Make an array of all those function arguments for easier handling
        $argsArray = get_defined_vars();

        // Loop through each function argument
        foreach ( $argsArray as $key => $value ) {
            // Assign the correct value to each of the Entity properties
            $this->scrEntity->$key = $value;
        }

        // Insert the subscriber & return
        return $this->subscriberManager->add( $this->scrEntity );

     }

     /**
     * Insert a new subscriber with only an email address
     * @param string $emailAddress Address of the new subscriber
     * @return int ID of new subscriber
     */
     public function addEmailOnly( $emailAddress )
     {
         // Save the email address to the subscriber entity
         $this->scrEntity->emailAddress = $emailAddress;

         // Insert the subscriber & return
         return $this->subscriberManager->add( $this->scrEntity );

     }

     /**
      * Get a subscriber by their ID
      * @param int $id ID of the subscriber to fetch
      */
     public function getById( $id )
     {
        // Get the subscriber & return
         return $this->subscriberManager->getSubscriberById( $id );
     }

     /**
      * Delete a subscriber by their ID
      * @param int $id ID of the subscriber to delete
      */
     public function delete( $id )
     {
         // delete the subscriber and return
         return $this->subscriberManager->delete( $id );
     }

}
