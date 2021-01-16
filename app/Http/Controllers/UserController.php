<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function getUserlist()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function getUser($user_id)
    {
        $user = User::find($user_id);
        return response()->json($user);
    }

    public function getUserTransactions($user_id)
    {
        $transactions = User::find($user_id)->transactions()->with(['sender', 'recipient'])->get();
        return response()->json($transactions);
    }

    public function transact($user_id, Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'sum' => 'required|numeric|min:0'
        ]);

        DB::transaction(function() use ($user_id, $request) {
            $sender = User::find($user_id);
            $recipient = User::find($request->recipient_id);    
            $sum = $request->sum;

            if ($request->transaction_sum > $sender->balance) {
                return response('not_enough_money', 400);
            }

            $transaction = new Transaction();
            $transaction->sender_id = $sender->id;
            $transaction->recipient_id = $recipient->id;
            $transaction->sum = $sum;

            $sender->balance = $sender->balance - $sum;
            $recipient->balance = $recipient->balance + $sum;

            $transaction->save();
            $sender->save();
            $recipient->save();
        });

        return response('success');
    }
}
