<?php

namespace App\Enums;

use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;

enum AccountStatus: string
{
    use IsKanbanStatus;

    case LEAD = 'lead';
    case CUSTOMER = 'customer';
}
