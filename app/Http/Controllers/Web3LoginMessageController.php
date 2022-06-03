<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Web3LoginMessageController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $nonce = Str::random(24);
        $message = "Sign this message to confirm you own this wallet address and Log in. This action will not cost any gas.\n\nNonce: " . $nonce;

        session()->put('sign_message', $message);

        return response()->json($message);
    }
}
