<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * The token.
     *
     * @var string
     */
    private $token;

    /**
     * Create a new notification instance.
     *
     * @param string $token
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
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
        $resetPasswordUrl = $this->resetPasswordUrl($notifiable);

        return (new MailMessage)->view('emails.resetPasswordEmail', ['url' => $resetPasswordUrl]);
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

    /*
   * Build the verification URL
   *
   * @return URL
   */
    protected function resetPasswordUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'reset-password',
            Carbon::now()->addMinutes(
                Config::get('auth.verification.expire', 60)),
            [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]
        );
    }
}
