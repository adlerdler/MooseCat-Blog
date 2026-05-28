<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('backup:run --only-db')->daily()->at('02:00');
Schedule::command('backup:clean')->daily()->at('03:00');
