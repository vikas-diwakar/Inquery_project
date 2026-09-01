<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FacebookWebhookController extends Controller
{
    public function verify(Request $request)
    {
        $verify_token = "test_webhook";

        if ($request->hub_verify_token === $verify_token) {
            return response($request->hub_challenge, 200);
        }

        return response("Invalid token", 403);
    }

    public function handle(Request $request)
    {
        \Log::info('Facebook Lead Data:', $request->all());

        return response('EVENT_RECEIVED', 200);
    }
}
