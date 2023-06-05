<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    private $token;
    private $message;
    public static $wrap = null;

    public function __construct($resource, $token = null, $message = null)
    {
        parent::__construct($resource);
        $this->token = $token;
        $this->message = $message;
    }

    public function toArray($request)
    {   
        if(isset($this->id)){
            $data = [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'created_at'=>$this->created_at,
                'updated_at'=>$this->updated_at
            ];

            $data = ['data'=>$data];
        }
        
        if ($this->token){
            $data['authorisation']['token'] = $this->token;
            $data['authorisation']['type'] = 'bearer';
        }
           
        if($this->message)
            $data['message'] = $this->message;

        return $data;
    }
}
