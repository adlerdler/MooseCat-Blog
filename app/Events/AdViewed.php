<?php

namespace App\Events;

use App\Models\Advertisement;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdViewed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Advertisement $advertisement;

    public function __construct(Advertisement $advertisement)
    {
        $this->advertisement = $advertisement;
    }
}
