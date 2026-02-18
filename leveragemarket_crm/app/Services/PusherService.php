<?php
// app/Services/PusherService.php

namespace App\Services;


use App\Events\MessageSent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class PusherService
{
    /**
     * Send a message to users based on their group
     *
     * @param array $pusherData
     * @return void
     */
    public function sendPusherMessage(array $pusherData)
    {
        try {
            $userGroupId = (string)session('user')['group_id'];
            $pusherUsers = DB::table('emplist')
                ->whereJsonContains('user_group_id', $userGroupId)
                ->pluck('email')
                ->toArray();

            $relationshipManagerEmail = DB::table('relationship_manager')
                ->where('user_id', session('clogin'))
                ->pluck('rm_id')
                ->first();

            if ($relationshipManagerEmail) {
                $pusherUsers[] = $relationshipManagerEmail;
            }

            $pusherUsers = array_unique($pusherUsers);

            foreach ($pusherUsers as $user) {
                $pusherData['id'] = time() . '_' . strtolower(str_replace(' ', '_', $pusherData['type'])) . '_' . $pusherData['enc_id'];
                try {
                    broadcast(new MessageSent($pusherData, $user));
                    Log::info("Pusher message sent successfully to: $user");
                } catch (Exception $e) {
                    Log::error("Failed to send Pusher message to: $user. Error: " . $e->getMessage());
                }
            }
        } catch (Exception $e) {
            Log::error("Error in sendPusherMessage function: " . $e->getMessage());
        }
    }
}
