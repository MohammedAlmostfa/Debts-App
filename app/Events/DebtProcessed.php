<?php

namespace App\Events;

use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class DebtProcessed
{

    use InteractsWithSockets;
    use SerializesModels;


    /**
     * The debt ID that triggered the event
     *
     * @var int
     */
    public $debtId;

    /**
     * The amount difference to apply
     *
     * @var float
     */
    public $difference;
    public $customerId;
    /**
     * Create a new event instance.
     *
     * @param int $debtId
     * @param float $difference
     */
    public function __construct($debtId, $customerId, $difference)
    {
        $this->debtId = $debtId;
        $this->customerId = $customerId;
        $this->difference = $difference;
    }

}
