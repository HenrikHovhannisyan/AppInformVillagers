<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function show(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $account = Account::with('user')
            ->where('user_id', $user->id)
            ->firstOrFail();

        $filteredAccount = $account->only([
            'field_size',
            'tree_count',
            'olive_type',
            'age_of_trees',
            'location_of_field',
            'continuous_season_count',
            'total_harvested_olives',
            'total_gained_oil',
            'account_creation_date',
            'edit_request',
            'admin_approval',
        ]);

        return response()->json($filteredAccount, 200);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $account = Account::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'field_size' => 'nullable|integer',
            'tree_count' => 'nullable|integer',
            'olive_type' => 'nullable|string',
            'age_of_trees' => 'nullable|integer',
            'location_of_field' => 'nullable|string',
            'continuous_season_count' => 'nullable|integer',
            'total_harvested_olives' => 'nullable|integer',
            'total_gained_oil' => 'nullable|integer',
            'account_creation_date' => 'nullable|date',
            'is_request_pending' => 'nullable|boolean',
            'is_approved' => 'nullable|boolean',
        ]);

        $account->update($validated);

        return response()->json([
            'message' => 'Account updated successfully',
            'account' => $account,
        ], 200);
    }

}
