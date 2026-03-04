<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification
{
    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        
        // Get branding settings
        $siteName = \App\Models\HomepageSetting::getSetting('branding', 'site_name') ?? config('app.name');
        $logoPath = \App\Models\HomepageSetting::getImagePath('branding', 'logo_dark') 
            ?? \App\Models\HomepageSetting::getImagePath('branding', 'logo_light')
            ?? (file_exists(public_path('assets/images/logo.png')) ? asset('assets/images/logo.png') : null);

        return (new MailMessage)
            ->view('emails.verify-email', [
                'user' => $notifiable,
                'verificationUrl' => $verificationUrl,
                'expiresAt' => Carbon::now()->addHours(24),
                'siteName' => $siteName,
                'logoPath' => $logoPath,
            ])
            ->subject('Verify Your Email Address - ' . $siteName);
    }

    /**
     * Get the verification URL for the user.
     *
     * @param mixed $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addHours(24),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
