<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $account = Account::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'field_size' => 'nullable|integer',
            'tree_count' => 'nullable|integer',
            'olive_type' => 'nullable|string',
            'age_of_trees' => 'nullable|integer',
            'location_of_field' => 'nullable|string',
            'continuous_season_count' => 'nullable|integer',
            'total_harvested_olives' => 'nullable|integer',
            'total_gained_oil' => 'nullable|integer',
        ]);

        if (isset($validated['email'])) {
            $user->email = $validated['email'];
            $user->save();
        }

        if (isset($validated['phone'])) {
            $user->phone = $validated['phone'];
            $user->save();
        }

        $account->admin_approval = false;
        $account->edit_request = false;
        $account->update(array_filter($validated));

        return response()->json([
            'message' => 'Account updated successfully',
            'account' => $account,
            'user' => $user,
        ], 200);
    }

    /**
     * @param Account $account
     * @param Request $request
     * @return RedirectResponse
     */
    public function approve(Account $account, Request $request)
    {
        if ($request->has('reject')) {
            $account->admin_approval = false;
            $account->edit_request = false;
        } else {
            $account->admin_approval = false;
            $account->edit_request = true;
        }

        $account->save();

        return back()->with('success', 'Account approval updated.');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function approveAccount(Request $request)
    {
        $user = auth()->user();

        $account = $user->account;

        if (!$account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        $account->admin_approval = true;
        $account->edit_request = false;

        $account->update();

        return response()->json([
            'message' => 'Your request to edit your account has been successfully sent to the admin.',
        ], 200);
    }

    /**
     * @param Request $request
     * @param Account $account
     * @return RedirectResponse
     */
    public function updateWeb(Request $request, Account $account)
    {

        $validated = $request->validate([
            'field_size' => 'nullable|integer',
            'tree_count' => 'nullable|integer',
            'olive_type' => 'nullable|string|max:255',
            'age_of_trees' => 'nullable|integer',
            'location_of_field' => 'nullable|string|max:255',
            'continuous_season_count' => 'nullable|integer',
            'total_harvested_olives' => 'nullable|integer',
            'total_gained_oil' => 'nullable|integer',
        ]);

        $account->update($validated);

        return back()->with('success', 'Account updated successfully.');
    }


}
