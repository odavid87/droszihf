<?php

namespace App\Observers;

use App\Models\Session;
use App\Services\AbTestService;

class SessionObserver
{

    public function __construct(private AbTestService $abTestService)
    {
    }

    /**
     * Handle the Session "created" event.
     */
    public function created(Session $session): void
    {
        $this->abTestService->defineAbtestVariantsForSession($session);
    }
}
