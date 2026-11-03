<?php

namespace App\Policies;

use App\Models\Deal;
use App\Models\User;

class DealPolicy
{
    /**
     * Determine whether the user can view the deal workspace and deliverables.
     */
    public function view(User $user, Deal $deal): bool
    {
        return $user->isAdmin() || $user->id === $deal->initiator_id || $user->id === $deal->counterparty_id;
    }

    /**
     * Determine whether the user can access confidential deliverables.
     */
    public function viewDeliverables(User $user, Deal $deal): bool
    {
        return $this->view($user, $deal);
    }
}
