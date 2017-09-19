<?php
namespace App\Liantech\Traits;
use App\Liantech\Helpers\ScoreboardCalculator;
use Pusher;
use App\Models\Table;
use App\Models\Broker;

trait NotificationsTrait
{

    /**
     * Create a new Pusher instance to send a new
     * notification to the scoreboard platform
     *
     * @param  Broker $broker
     * @param  array $data
     * @return boolean
     */
    public function notifyNewDeposit($broker, $data = [])
    {
        $pusher = new Pusher(
            env('PUSHER_APP_KEY', ''),
            env('PUSHER_APP_SECRET', ''),
            env('PUSHER_APP_ID', '')
        );

        $pusher->trigger($broker->name . 'playground_channel', 'deposit', $data);

        return true;
    }

    public function updateStats($broker)
    {
        $stats = $this->getStats($broker);

        $pusher = new Pusher(
            env('PUSHER_APP_KEY', ''),
            env('PUSHER_APP_SECRET', ''),
            env('PUSHER_APP_ID', '')
        );

        $pusher->trigger($broker->name . 'playground_channel', 'stats', $stats);
    }

    public function notifyScoreboardForTable(Broker $broker, Table $table)
    {
        $employees = (new ScoreboardCalculator)->getScoreboardUpdates($table);
        $pusher = new Pusher(
            env('PUSHER_APP_KEY', ''),
            env('PUSHER_APP_SECRET', ''),
            env('PUSHER_APP_ID', '')
        );

        $pusher->trigger($broker->name . 'scoreboard_channel_' . $table->id, 'update', $employees);

    }


}