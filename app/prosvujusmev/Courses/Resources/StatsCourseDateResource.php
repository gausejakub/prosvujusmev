<?php

namespace App\prosvujusmev\Courses\Resources;

use App\prosvujusmev\Courses\CourseDate;
use Illuminate\Http\Resources\Json\JsonResource;

class StatsCourseDateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'course' => $this->course,
            'from_date' => $this->from_date,
            'from_date_date' => $this->from_date->format('Y-m-d'),
            'from_date_time' => $this->from_date->format('H:i:s'),
            'to_date' => $this->to_date,
            'to_date_date' => $this->to_date->format('Y-m-d'),
            'to_date_time' => $this->to_date->format('H:i:s'),
            'status' => $this->status,
            'venue' => $this->venue,
            'limit' => $this->limit,
            'remaining' => $this->remaining,
            'lecturer' => $this->lecturer,
            'description' => $this->description,
            'reservations' => $this->reservations,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
        ];
    }
}