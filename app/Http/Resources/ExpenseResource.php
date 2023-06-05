<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    private $message;
    public static $wrap = null;

    public function __construct($resource, $message = null)
    {
        parent::__construct($resource);
        $this->message = $message;
    }
    public function toArray($request)
    {  
        if(isset($this->id)){
            $data = [
                'id' => $this->id,
                'user_id' => $this->user_id,
                'description' => $this->description,
                'reference_date' => $this->reference_date,
                'value' => $this->value,
                'created_at'=>$this->created_at,
                'updated_at'=>$this->updated_at
            ];
    
            $data = ['data'=>$data];
        }
    
        if($this->message)
            $data['message'] = $this->message;

        return $data;
    }
}
