<?php

namespace App\Http\Controllers\prosvujusmev\Admin\Reservations;

use App\Http\Controllers\Controller;
use App\prosvujusmev\Courses\Course;
use App\prosvujusmev\Courses\CourseDate;
use App\prosvujusmev\Reservations\Repositories\ReservationRepository;
use App\prosvujusmev\Reservations\Reservation;
use App\prosvujusmev\Reservations\Resources\ReservationResource;
use Illuminate\Http\Request;

class ReservationsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->json == true) {
            return response()->json([
                'data' => Reservation::with(['courseDate', 'courseDate.course'])->get(),
            ]);
        }
        return response()->view('admin.reservations.index');
    }

    public function create()
    {
        return response()->view('admin.courses.dates.create', [
            'courses' => Course::all()
        ]);
    }

    public function show(Request $request, Reservation $reservation)
    {
        return response()->view('admin.reservations.show', [
            'reservation' => json_encode(new ReservationResource($reservation)),
            'backUrl' => json_encode([str_replace(url('/'), '', url()->previous())]),
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'course_id' => 'required|integer|exists:courses,id',
            'from_date_date' => 'required|date',
            'from_date_time' => 'required',
            'from_date_date' => 'required|date',
            'from_date_time' => 'required',
            'venue' => 'required|string|min:1|max:254',
            'lecturer' => 'required|string|min:1|max:254',
            'limit' => 'required|integer|min:0|max:9999999',
            'description' => 'nullable|string|min:1|max:5000',
        ]);

        CourseDate::create([
            'course_id' => $request->course_id,
            'from_date' => \Carbon\Carbon::createFromDate($request->from_date_date)->setTime(\Carbon\Carbon::parse($request->from_date_time)->hour, \Carbon\Carbon::parse($request->from_date_time)->minute),
            'to_date' => \Carbon\Carbon::createFromDate($request->to_date_date)->setTime(\Carbon\Carbon::parse($request->from_date_time)->hour, \Carbon\Carbon::parse($request->from_date_time)->minute),
            'venue' => $request->venue,
            'lecturer' => $request->lecturer,
            'limit' => $request->limit,
            'description' => $request->description,
        ]);

        return redirect('/admin/course-dates')->with([
            'message' => 'Termín kurzu byl vytvořen.'
        ]);
    }

    public function edit(Request $request, CourseDate $courseDate)
    {
        return response()->view('admin.courses.dates.edit', [
            'courseDate' => $courseDate,
            'courses' => Course::all()
        ]);
    }

    public function update(Request $request, CourseDate $courseDate)
    {
        $this->validate($request, [
            'course_id' => 'required|integer|exists:courses,id',
            'from_date_date' => 'required|date',
            'from_date_time' => 'required',
            'from_date_date' => 'required|date',
            'from_date_time' => 'required',
            'venue' => 'required|string|min:1|max:254',
            'lecturer' => 'required|string|min:1|max:254',
            'limit' => 'required|integer|min:0|max:9999999',
            'description' => 'nullable|string|min:1|max:5000',
        ]);

        $courseDate->update([
            'course_id' => $request->course_id,
            'from_date' => \Carbon\Carbon::createFromDate($request->from_date_date)->setTime(\Carbon\Carbon::parse($request->from_date_time)->hour, \Carbon\Carbon::parse($request->from_date_time)->minute),
            'to_date' => \Carbon\Carbon::createFromDate($request->to_date_date)->setTime(\Carbon\Carbon::parse($request->from_date_time)->hour, \Carbon\Carbon::parse($request->from_date_time)->minute),
            'venue' => $request->venue,
            'lecturer' => $request->lecturer,
            'limit' => $request->limit,
            'description' => $request->description,
        ]);

        return redirect('/admin/course-dates')->with([
            'message' => 'Kurz byl upraven.'
        ]);
    }

    public function destroy(Request $request, Reservation $reservation)
    {
        $result = app(ReservationRepository::class)->delete($reservation);
        return response()->json([
            'success' => $result,
            'message' => $result ? 'Rezervace byla odstraněna.' : 'Rezervaci se nepodařilo odstranit.',
        ]);
    }

    public function approve(Request $request, Reservation $reservation)
    {
        if ($reservation->status !== Reservation::STATUS_APPROVED) {
            $reservation = app(ReservationRepository::class)->approve($reservation);
            $result = true;
        } else {
            $result = false;
        }
        return response()->json([
            'success' => $result,
            'reservation' => $reservation,
            'message' => $result ? 'Rezervace byla schválena.' : 'Rezervaci se nepodařilo schválit.',
        ]);
    }

    public function reject(Request $request, Reservation $reservation)
    {
        if ($reservation->status !== Reservation::STATUS_REJECTED) {
            $reservation = app(ReservationRepository::class)->reject($reservation);
            $result = true;
        } else {
            $result = false;
        }
        return response()->json([
            'success' => $result,
            'reservation' => $reservation,
            'message' => $result ? 'Rezervace byla zamítnuta.' : 'Rezervaci se nepodařilo zamítnout.',
        ]);
    }

    public function complete(Request $request, Reservation $reservation)
    {
        if ($reservation->status !== Reservation::STATUS_COMPLETED) {
            $reservation = app(ReservationRepository::class)->complete($reservation);
            $result = true;
        } else {
            $result = false;
        }
        return response()->json([
            'success' => $result,
            'reservation' => $reservation,
            'message' => $result ? 'Rezervace byla dokončena.' : 'Rezervaci se nepodařilo schválit.',
        ]);
    }
}
