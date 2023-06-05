<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Expense;
use Illuminate\Auth\Access\AuthorizationException;

class ExpensePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    

    public function manageExpense(User $user, Expense $expense)
    {
        // Verifica se o usuário é o dono da despesa
        if ($user->id === $expense->user_id) {
            return true;
        }
    
        throw new AuthorizationException('You do not have permission to access or update this expense.');
    }

 
}
