<?php

namespace App\Policies;

use App\Models\DocumentTemplate;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentTemplatePolicy
{
    use HandlesAuthorization;

    public function view(User $user, DocumentTemplate $template)
    {
        // System templates are viewable by everyone
        if ($template->is_system) {
            return true;
        }
        // Tenant templates only viewable by tenant users
        return $user->tenant_id === $template->tenant_id;
    }

    public function update(User $user, DocumentTemplate $template)
    {
        // System templates cannot be edited by tenant users
        if ($template->is_system) {
            return false;
        }
        // Locked templates (standardized copies) cannot be edited
        if ($template->is_locked) {
            return false;
        }
        return $user->tenant_id === $template->tenant_id;
    }

    public function delete(User $user, DocumentTemplate $template)
    {
        // System templates cannot be deleted by tenant users
        if ($template->is_system) {
            return false;
        }
        return $user->tenant_id === $template->tenant_id;
    }
}
