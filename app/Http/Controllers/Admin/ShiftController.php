<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $shifts = Shift::with('user')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('admin.shifts.index', compact('shifts'));
    }
}
