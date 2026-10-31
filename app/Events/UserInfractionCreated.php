<?php

namespace App\Events;

use App\Models\UserInfraction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserInfractionCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public UserInfraction $infraction) {}
}