<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = !$user->status;
        $user->save();

        return redirect()->back()->with('success', 'User status updated successfully.');
    }

    /**
     * Update statistics for the given user.
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function updateStatistic(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (!$user->statistics()->exists()) {
            return redirect()->back()->withErrors(['error' => 'This user has no statistics.']);
        }

        $validatedData = $request->validate([
            'statistics' => 'required|array',
            'statistics.*.year' => 'required|integer|min:2000|max:' . date('Y'),
            'statistics.*.olive_amount' => 'nullable|integer|min:0',
            'statistics.*.oil_amount' => 'nullable|integer|min:0',
        ]);

        foreach ($validatedData['statistics'] as $statistic) {
            $user->statistics()->updateOrCreate(
                ['year' => $statistic['year']],
                [
                    'olive_amount' => $statistic['olive_amount'] ?? null,
                    'oil_amount' => $statistic['oil_amount'] ?? null,
                ]
            );
        }

        return redirect()->back()->with('success', 'Statistics updated successfully.');
    }

}
