<?php

namespace App\Exports;

use App\Attendee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendeeExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct(String $event_id, $headings)
    {
        $this->event_id = $event_id;
        $this->headings = $headings;
        
        return $this;
    }

    public function collection()
    {
        return Attendee::where('event_id', $this->event_id, $this->headings)->get();
    }

    public function headings(): array{
        return $this->headings;
    }
}
