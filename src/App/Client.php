<?php

namespace App;

class Client 
{
    public $id;
    public $token;

    public function __construct($id,$token)
    {
        $this->id = $id;
        $this->token = $token;
    }

    public function validateAccess()
    {
        if (isset( $this->id ) && isset( $this->token )) {
            $user = User::where('id',$this->id)->get()[0];
            if ($user) {
                return ($user->api_token == $this->token);
            }
        }
        return false;
    }
}
