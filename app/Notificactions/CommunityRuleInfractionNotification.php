<?php

namespace App\Notifications;

use App\Models\UserInfraction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommunityRuleInfractionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public UserInfraction $infraction) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $rules = config('community_rules.rules');
        $rule = $rules[$this->infraction->rule_id] ?? null;
        $consequence = config("community_rules.consequences.{$this->infraction->strike_level}");

        return (new MailMessage)
            ->subject('Aviso importante sobre tu cuenta: Incumplimiento de normas')
            ->view('emails.infraction_warning', [
                'user' => $notifiable,
                'rule' => $rule,
                'allRules' => $rules,
                'detail' => $this->infraction->detail,
                'consequence' => $consequence,
                'strikeLevel' => $this->infraction->strike_level,
            ]);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'infraction_id' => $this->infraction->id,
            'rule_id' => $this->infraction->rule_id,
            'detail' => $this->infraction->detail,
            'strike_level' => $this->infraction->strike_level,
        ];
    }
}