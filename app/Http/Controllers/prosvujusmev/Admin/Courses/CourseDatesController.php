<?php

namespace App\Http\Controllers\prosvujusmev\Admin\Courses;

use App\Http\Controllers\Controller;
use App\prosvujusmev\Courses\Course;
use App\prosvujusmev\Courses\CourseDate;
use App\prosvujusmev\Courses\Repositories\CourseDateRepository;
use App\prosvujusmev\Courses\Resources\CourseDateResource;
use Illuminate\Http\Request;

class CourseDatesController extends Controller
{
    public function index()
    {
        return response()->view('admin.courses.dates.index', [
            'courseDates' => CourseDate::with('course')->get()
        ]);
    }
    
    public function show(Request $request, CourseDate $courseDate)
    {
        return $request->ajax() ?
            response()->json(['courseDate' => new CourseDateResource($courseDate)]) :
            response()->view('admin.courses.dates.show', [
                'courseDate' => json_encode(new CourseDateResource($courseDate)),
                'backUrl' => json_encode([str_replace(url('/'), '', url()->previous())]),
            ]);
    }


    public function create()
    {
        return response()->view('admin.courses.dates.create', [
            'courses' => Course::all()
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
            'lecturer' => 'nullable|string|max:254',
            'limit' => 'required|integer|min:0|max:9999999',
            'description' => 'nullable|string|max:5000',
        ]);

        CourseDate::create([
            'course_id' => $request->course_id,
            'from_date' => \Carbon\Carbon::createFromDate($request->from_date_date)->setTime(\Carbon\Carbon::parse($request->from_date_time)->hour, \Carbon\Carbon::parse($request->from_date_time)->minute),
            'to_date' => \Carbon\Carbon::createFromDate($request->to_date_date)->setTime(\Carbon\Carbon::parse($request->from_date_time)->hour, \Carbon\Carbon::parse($request->from_date_time)->minute),
            'venue' => $request->venue,
            'lecturer' => $request->lecturer,
            'limit' => $request->limit,
            'description' => $request->description,
            'status' => CourseDate::STATUS_ACTIVE,
        ]);

        return redirect('/admin/course-dates')->with([
            'message' => 'Termín kurzu byl vytvořen.'
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
            'lecturer' => 'nullable|string|min:1|max:254',
            'limit' => 'required|integer|min:0|max:9999999',
            'description' => 'nullable|string|min:1|max:5000',
        ]);

        $courseDate->update([
            'course_id' => $request->course_id,
            'from_date' => \Carbon\Carbon::createFromDate($request->from_date_date)->setTime(\Carbon\Carbon::parse($request->from_date_time)->hour, \Carbon\Carbon::parse($request->from_date_time)->minute),
            'to_date' => \Carbon\Carbon::createFromDate($request->to_date_date)->setTime(\Carbon\Carbon::parse($request->to_date_time)->hour, \Carbon\Carbon::parse($request->to_date_time)->minute),
            'venue' => $request->venue,
            'lecturer' => $request->lecturer,
            'limit' => $request->limit,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Kurz byl upraven.',
            'courseDate' => new CourseDateResource($courseDate->fresh()),
        ]);
    }

    public function destroy(Request $request, CourseDate $courseDate)
    {
        app(CourseDateRepository::class)->delete($courseDate);
        return response()->json();
    }

    public function complete(Request $request, CourseDate $courseDate)
    {
        if ($courseDate->status !== CourseDate::STATUS_COMPLETED) {
            $result = app(CourseDateRepository::class)->complete($courseDate, $request->attendedReservationIds);
        } else {
            $result = false;
        }
        return response()->json([
            'success' => $result,
            'message' => $result ? 'Termín byl úspěšně dokončen.' : 'Termín se nepodařilo dokončit.',
            'courseDate' => $courseDate->fresh() 
        ]);
    }
}
