<?php

namespace App\Livewire;

use App\Models\DoctorSchedule;
use Livewire\Component;

class ScheduleComponent extends Component
{

    public $daysOfWeek;

    public function mount(){
        $this->daysOfWeek = [
            '0' => 'Sunday',
            '1' => 'Monday',
            '2' => 'Tuesday',
            '3' => 'Wednesday',
            '4' => 'Thursday',
            '5' => 'Friday',
            '6' => 'Saturday',
        ];
    }

    public function delete($id){
        $data = DoctorSchedule::find($id);

        $data->delete();

        session()->flash('message','Schedule deleted successfully');

        return $this->redirect('/my/schedules',navigate: true);
    }

    public function render()
    {
        $user_id = auth()->user()->id;

        return view('livewire.schedule-component', [
            'schedules' => DoctorSchedule::with('doctor')
                ->whereHas('doctor', function ($query) use ($user_id) {
                    $query->where('doctors.user_id', $user_id);
                })->get()
        ]);
    }
}
