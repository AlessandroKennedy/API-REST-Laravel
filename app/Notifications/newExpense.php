<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Expense;
use DateTime;
use Illuminate\Contracts\Queue\ShouldQueue;

class newExpense extends Notification implements ShouldQueue
{
    use Queueable;
    private $user;
    private $expense;
    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, Expense $expense)
    {
        $this->user = $user;
        $this->expense = $expense;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $value_format = str_replace('.',',',$this->expense->value);
        $reference_date = DateTime::createFromFormat('Y-m-d', $this->expense->reference_date); 
        $reference_date_format = $reference_date->format('d/m/Y');

        return (new MailMessage)
                    ->subject('Despesa cadastrada')
                    ->greeting('Despesa cadastrada')
                    ->line('Olá ' . $this->user->name.', uma nova despesa foi cadastrada para seu usuário.')
                    ->line('Valor: R$'.$value_format)
                    ->line('Data: '.$reference_date_format)
                    ->line('Descrição: '.$this->expense->description)
                    ->line('Obrigado por utilizar o nosso sistema!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
