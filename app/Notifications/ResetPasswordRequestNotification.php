<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordRequestNotification extends Notification
{
    use Queueable;

    protected $token;
    protected $id_user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token,$id_user)
    {
        $this->token = $token;
        $this->id_user = $id_user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url    =   url('users/'.$this->id_user.'/password?token='.$this->token);
        return (new MailMessage)
                ->line('You are receiving this email because we received a password reset request for your account.')
                ->line('Your reset token is: '.$this->token)
                ->line('You also can reset your password by clicking the below link')
                ->action('Reset Password', url($url))
                ->line('If you did not request a password reset, no further action is required.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
