<?php

namespace App\Http\Controllers\API;

use App\Models\Expense;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ExpenseResource;
use App\Http\Requests\Expenses\StoreExpenseRequest;
use App\Http\Requests\Expenses\UpdateExpenseRequest;
use App\Http\Requests\Expenses\ShowExpenseRequest;
use App\Http\Requests\Expenses\DeleteExpenseRequest;
use PhpParser\Node\Stmt\TryCatch;
use App\Notifications\newExpense;

class ExpenseController extends Controller
{   
    protected $user;
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->user = Auth::user();
    }

    /**
 * @OA\Post(
 *     path="/api/expenses",
 *     summary="Cria uma nova despesa",
 *     tags={"Expenses"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="description", type="string", example="Descrição da despesa"),
 *             @OA\Property(property="reference_date", type="string", format="date", example="2023-06-02"),
 *             @OA\Property(property="value", type="number", example=150.5)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Despesa registrada com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="user_id", type="integer"),
 *                 @OA\Property(property="description", type="string"),
 *                 @OA\Property(property="reference_date", type="string", format="date"),
 *                 @OA\Property(property="value", type="number"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             ),
 *             @OA\Property(property="message", type="string", example="Despesa registrada com sucesso")
 *         )
 *     )
 * )
 */
    public function store(StoreExpenseRequest $request)
    {
        $user = Auth::user();
        
        $expense = Expense::create([
            'user_id' => $user->id,
            'description' => $request->description,
            'reference_date' => $request->reference_date,
            'value' => $request->value
        ]);

        $user->notify(new newExpense($user,$expense));
        return (new ExpenseResource($expense, 'Expense registered successfully'));
            
    }

    /**
 * @OA\Put(
 *     path="/api/expenses/{expense_id}",
 *     summary="Atualiza uma despesa existente",
 *     tags={"Expenses"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="expense_id",
 *         in="path",
 *         required=true,
 *         description="ID da despesa",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="description", type="string", example="Descrição atualizada da despesa"),
 *             @OA\Property(property="reference_date", type="string", format="date", example="2023-06-02"),
 *             @OA\Property(property="value", type="number", example=200.75)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Despesa atualizada com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="user_id", type="integer"),
 *                 @OA\Property(property="description", type="string"),
 *                 @OA\Property(property="reference_date", type="string", format="date"),
 *                 @OA\Property(property="value", type="number"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             ),
 *             @OA\Property(property="message", type="string", example="Despesa atualizada com sucesso")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Despesa não encontrada",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Despesa não encontrada")
 *         )
 *     )
 * )
 */
    public function update(UpdateExpenseRequest $request, $expense_id)
    { 
        $expense = Expense::find($expense_id); 
        
        if(!$expense){
            return (new ExpenseResource(null, 'Expense not found'))
            ->response()
            ->setStatusCode(404);
        }
     
        $this->authorize('manageExpense', $expense);
       
        $expense = Expense::updateById($request->all(), $expense_id);
        return (new ExpenseResource($expense, 'Expense updated successfully'));
    }
    

    /**
 * @OA\Get(
 *     path="/api/expenses/{expense_id}",
 *     summary="Obtém detalhes de uma despesa",
 *     tags={"Expenses"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="expense_id",
 *         in="path",
 *         required=true,
 *         description="ID da despesa",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Detalhes da despesa",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="user_id", type="integer"),
 *                 @OA\Property(property="description", type="string"),
 *                 @OA\Property(property="reference_date", type="string", format="date"),
 *                 @OA\Property(property="value", type="number"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             ),
 *             @OA\Property(property="message", type="string", example="Expense retrieved successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Despesa não encontrada",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Despesa não encontrada")
 *         )
 *     )
 * )
 */
    public function show($expense_id)
    {
        $expense = Expense::find($expense_id);
        if(!$expense){
            return (new ExpenseResource(null, 'Expense not found'))
            ->response()
            ->setStatusCode(404);
        }
        $this->authorize('manageExpense', $expense);
        return (new ExpenseResource($expense, null));
    }


    /**
 * @OA\Delete(
 *     path="/api/expenses/{expense_id}",
 *     summary="Deleta uma despesa",
 *     tags={"Expenses"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="expense_id",
 *         in="path",
 *         required=true,
 *         description="ID da despesa",
 *         @OA\Schema(
 *             type="integer",
 *             format="int64"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Despesa deletada com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Expense deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Despesa não encontrada",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Despesa não encontrada")
 *         )
 *     )
 * )
 */

    public function destroy($expense_id)
    {
    
        $expense = Expense::find($expense_id);
        if(!$expense){
            return (new ExpenseResource(null, 'Expense not found'))
            ->response()
            ->setStatusCode(404);
        }

        $this->authorize('manageExpense', $expense);
        $expense->delete();    
        return (new ExpenseResource(null, 'Expense deleted successfully'));
    }
   
}
