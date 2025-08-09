<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Mail;

class CustomResetPasswordNotification extends BaseResetPasswordNotification
{
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        // Use Laravel's built-in Mail with custom template
        Mail::to($notifiable->getEmailForPasswordReset())
            ->send(new ResetPasswordMail($url, $notifiable->name ?? null));

        // Return a simple confirmation message
        return (new MailMessage)
            ->line('Password reset email has been sent.')
            ->line('Please check your email for further instructions.');
    }
}