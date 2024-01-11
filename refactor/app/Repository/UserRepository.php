<?php

namespace DTApi\Repository;

use DTApi\Models\User;

class UserRepository
{

    /**
     * initilize models associated with user
     */
    public function __construct(
        User $userModel
    ){
        $this->user = $userModel;
        
    }

    /**
     * Custom Function
     */
    public function identifyUserByID($user_id){
        /**
         * return user 
         */
        return $this->user->find($user_id);
    }

}
