<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Expense extends Model
{
    use HasFactory,Notifiable;

    protected $fillable = [
        'user_id',
        'description',
        'reference_date',
        'value'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* Atualiza uma despesa buscando pelo id */
    public static function updateById($data,$expense_id){
       $expense =  self::find($expense_id);
       
       if(isset($data['description'])){
        
        $expense->description = $data['description'];
       }

       if(isset($data['reference_date'])){
        $expense->reference_date = $data['reference_date'];
       }

       if(isset($data['value'])){
        $expense->value = $data['value'];
       }

       $expense->save();
       return $expense;
    }


}
