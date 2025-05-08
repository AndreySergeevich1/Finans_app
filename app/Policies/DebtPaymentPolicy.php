<?php

namespace App\Policies;

use App\Models\DebtPayment;
use App\Models\User;

class DebtPaymentPolicy
{
    public function view(User $user, DebtPayment $debtPayment)
    {
        return $user->id === $debtPayment->debt->user_id;
    }

    public function update(User $user, DebtPayment $debtPayment)
    {
        return $user->id === $debtPayment->debt->user_id;
    }

    public function delete(User $user, DebtPayment $debtPayment)
    {
        return $user->id === $debtPayment->debt->user_id;
    }
}
