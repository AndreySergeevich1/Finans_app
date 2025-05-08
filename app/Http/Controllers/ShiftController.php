<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        // Получаем параметры периода из запроса или используем текущую дату
        $year = $request->input('year') ?? now()->year;
        $month = $request->input('month') ?? now()->month;
        $isFirstPeriod = $request->input('isFirstPeriod');
        if ($isFirstPeriod === null) {
            // Определяем по текущей дате
            $today = now();
            $isFirstPeriod = ($today->day >= 30 || $today->day <= 14);
        } else {
            $isFirstPeriod = filter_var($isFirstPeriod, FILTER_VALIDATE_BOOLEAN);
        }

        // Логика расчёта периода
        if ($isFirstPeriod) {
            // 30–14
            $prevMonth = $month - 1;
            $prevYear = $year;
            if ($prevMonth < 1) {
                $prevMonth = 12;
                $prevYear--;
            }
            $lastDayPrevMonth = \Carbon\Carbon::create($prevYear, $prevMonth, 1)->endOfMonth()->day;
            $startDay = min(30, $lastDayPrevMonth);
            $firstPeriodStart = \Carbon\Carbon::create($prevYear, $prevMonth, $startDay);
            $firstPeriodEnd = \Carbon\Carbon::create($year, $month, 14);
            $secondPeriodStart = \Carbon\Carbon::create($year, $month, 15);
            $secondPeriodEnd = \Carbon\Carbon::create($year, $month, 29);
        } else {
            // 15–29
            $firstPeriodStart = \Carbon\Carbon::create($year, $month, 30)->subMonth();
            $firstPeriodEnd = \Carbon\Carbon::create($year, $month, 14);
            $secondPeriodStart = \Carbon\Carbon::create($year, $month, 15);
            $secondPeriodEnd = \Carbon\Carbon::create($year, $month, 29);
        }

        $firstPeriodShifts = Shift::where('user_id', auth()->id())
            ->whereBetween('date', [$firstPeriodStart, $firstPeriodEnd])
            ->orderBy('date')
            ->get()
            ->map(function($shift) {
                return [
                    'id' => $shift->id,
                    'date' => $shift->date,
                    'start_time' => $shift->start_time,
                    'end_time' => $shift->end_time,
                    'type' => $shift->type,
                    'notes' => $shift->notes ?? '',
                ];
            });

        $secondPeriodShifts = Shift::where('user_id', auth()->id())
            ->whereBetween('date', [$secondPeriodStart, $secondPeriodEnd])
            ->orderBy('date')
            ->get()
            ->map(function($shift) {
                return [
                    'id' => $shift->id,
                    'date' => $shift->date,
                    'start_time' => $shift->start_time,
                    'end_time' => $shift->end_time,
                    'type' => $shift->type,
                    'notes' => $shift->notes ?? '',
                ];
            });

        return Inertia::render('Shifts/Index', [
            'firstPeriodShifts' => $firstPeriodShifts,
            'secondPeriodShifts' => $secondPeriodShifts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'type' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        Shift::create([
            'user_id' => auth()->id(),
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'type' => $validated['type'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->back()->with('success', 'Смена успешно добавлена');
    }

    public function update(Request $request, Shift $shift)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'type' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $shift->update($validated);

        return redirect()->back()->with('success', 'Смена успешно обновлена');
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();
        return redirect()->back()->with('success', 'Смена успешно удалена');
    }

    public function show(Shift $shift)
    {
        if ($shift->user_id !== auth()->id()) {
            abort(403);
        }
        return response()->json([
            'id' => $shift->id,
            'date' => $shift->date->format('Y-m-d'),
            'start_time' => $shift->start_time,
            'end_time' => $shift->end_time,
            'type' => $shift->type,
            'notes' => $shift->notes,
        ]);
    }
} 