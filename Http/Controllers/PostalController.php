<?php

namespace Modules\Postal\Http\Controllers;

use App\Mailbox;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Postal\Entities\FetchEmails;
use Modules\Postal\Entities\Message;

class PostalController extends Controller
{
    /**
     * Process an incoming e-mail.
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|Response|\Symfony\Component\HttpFoundation\Response
     */
    public function endpoint(Request $request, $mailbox_id)
    {
        $data = $request->all();

        $mailbox = Mailbox::where([
            'id' => $mailbox_id,
            'in_protocol' => (int) \App\Option::get('postal.incoming.http.id'),
        ])->firstOrFail();

        $message = new Message(base64_decode($data['message']));

        (new FetchEmails())->processMessage($message, $message->getId(), $mailbox, []);

        return response()->json([]);
    }
}
