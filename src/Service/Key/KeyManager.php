<?php

namespace App\Service\Key;

use App\Entity\User;

class KeyManager
{
    public const CACHE_LIST = 'tasks_list';

    public function cacheListKey(User $user): string
    {
        return self::CACHE_LIST . '_' . $user->getId();
    }
}
