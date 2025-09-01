<?php

namespace App\Policies;

use App\Models\EmailCampaign;
use App\Models\User;

class EmailCampaignPolicy
{
    public function view(User $user, EmailCampaign $emailCampaign): bool
    {
        return $user->id === $emailCampaign->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, EmailCampaign $emailCampaign): bool
    {
        return $user->id === $emailCampaign->user_id;
    }

    public function delete(User $user, EmailCampaign $emailCampaign): bool
    {
        return $user->id === $emailCampaign->user_id;
    }
}
