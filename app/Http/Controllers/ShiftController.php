<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Services\ShiftService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ShiftController extends Controller
{
    protected $shiftService;

    public function __construct(ShiftService $shiftService)
    {
        $this->shiftService = $shiftService;
    }

    public function prompt()
    {
        if (auth()->user()->activeShift) {
            return redirect()->route('dashboard');
        }
        return view('shifts.prompt');
    }

    public function start(Request $request)
    {
        $request->validate([
            'starting_cash' => 'required|numeric|min:0',
        ]);

        try {
            $this->shiftService->start(auth()->user(), $request->starting_cash);
            return back()->with('success', 'Shift started successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function end(Request $request)
    {
        $request->validate([
            'actual_cash' => 'required|numeric|min:0',
            'actual_gcash' => 'required|numeric|min:0',
        ]);

        $shift = auth()->user()->activeShift;

        if (!$shift) {
            return back()->with('error', 'No active shift found.');
        }

        try {
            $closedShift = $this->shiftService->close($shift, $request->actual_cash, $request->actual_gcash);
            
            // Redirect to shift report to show the Z-Read
            return redirect()->route('shifts.report', $closedShift->id)->with('success', 'Shift ended successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function report(Shift $shift)
    {
        // Ensure user can only view their own shift unless they are an admin
        if (auth()->id() !== $shift->user_id && strtolower(auth()->user()->position ?? '') !== 'admin') {
            abort(403, 'Unauthorized access to shift report.');
        }

        $totals = $this->shiftService->calculateTotals($shift);

        return view('shifts.report', [
            'shift' => $shift,
            'totals' => $totals
        ]);
    }

    public function forceClose(Request $request, Shift $shift)
    {
        if (strtolower(auth()->user()->position ?? '') !== 'admin') {
            abort(403, 'Only admins can force close a shift.');
        }

        try {
            $this->shiftService->forceClose($shift, auth()->user(), $request->input('notes'));
            return back()->with('success', 'Shift forcefully closed.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
