<?php

namespace App\Providers;
use Illuminate\Support\Facades\Gate;
use App\Models\Expense;
use App\Policies\ExpensePolicy;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Expense' => 'App\Policies\ExpensePolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        
        $this->registerPolicies();
        
        Gate::policy(Expense::class, ExpensePolicy::class);
      
    }
}
