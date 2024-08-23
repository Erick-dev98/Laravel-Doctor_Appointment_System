<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use Illuminate\Support\Carbon;
use App\Mail\AppointmentCreated;
use Illuminate\Support\Facades\Mail;

class BookingComponent extends Component
{    
    public $doctor_details;
    public $selectedDate;
    public $availableDates = [];
    public $timeSlots = [];

    public function mount($doctor)
    {
        $this->doctor_details = $doctor;
        // Fetch the available dates when the booking page is loading
        $this->fetchAvailableDates($this->doctor_details);
    }

    public function fetchAvailableDates($doctor)
    {
        // Get all the time schedules from the database
        $schedules = DoctorSchedule::where('doctor_id', $doctor->id)
            ->get();

        $availability = [];
        foreach ($schedules as $schedule) {
            $dayOfWeek = $schedule->available_day; //0 - sunday, 1 - Monday
            $fromTime = Carbon::createFromFormat('H:i:s', $schedule->from);
            $toTime = Carbon::createFromFormat('H:i:s', $schedule->to);
            $availability[$dayOfWeek] = [
                'from' => $fromTime,
                'to' => $toTime,
            ];
        }

        $dates = [];
        $today = Carbon::today();
        for ($i = 0; $i < 365; $i++) { // Looping for 1 year
            $date = $today->copy()->addDays($i);
            $dayOfWeek = $date->dayOfWeek;

            if (isset($availability[$dayOfWeek])) {
                $dates[] = $date->format('Y-m-d');
            }
        }

        $this->availableDates = $dates;
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->fetchTimeSlots($date, $this->doctor_details);
    }

    public function fetchTimeSlots($date, $doctor)
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek; //0 , 1... 5
        $carbonDate = Carbon::parse($date)->format('Y-m-d');
        $schedule = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('available_day', $dayOfWeek)
            ->first();

        if ($schedule) {
            $fromTime = Carbon::createFromFormat('H:i:s', $schedule->from);
            $toTime = Carbon::createFromFormat('H:i:s', $schedule->to);

            $slots = [];
            while ($fromTime->lessThan($toTime)) {

                $timeSlot = $fromTime->format('H:i:s');
                $appointmentExists = Appointment::where('doctor_id',  $doctor->id)
                    ->where('appointment_date', $carbonDate)
                    ->where('appointment_time', $timeSlot)
                    ->exists();
                if (!$appointmentExists) {
                    $slots[] = $timeSlot;
                }

                $fromTime->addHour();
            }

            $this->timeSlots = $slots;
                    // dd($this->timeSlots);

        } else {
            $this->timeSlots = [];
        }
    }

    // public function sendAppointmentNotification($appointmentData)
    // {
    //     // Send to Admin
    //     $appointmentData['recipient_name'] = 'Admin Admin';
    //     $appointmentData['recipient_role'] = 'admin';
    //     Mail::to('shadrackmballah74@gmail.com')->send(new AppointmentCreated($appointmentData));

    //     // Send to Doctor
    //     $appointmentData['recipient_name'] = $appointmentData['doctor_name'];
    //     $appointmentData['recipient_role'] = 'doctor';
    //     Mail::to($appointmentData['doctor_email'])->send(new AppointmentCreated($appointmentData));

    //     // Send to Patient
    //     $appointmentData['recipient_name'] = $appointmentData['patient_name'];
    //     $appointmentData['recipient_role'] = 'patient';
    //     Mail::to($appointmentData['patient_email'])->send(new AppointmentCreated($appointmentData));

    //     return 'Appointment notifications sent successfully!';
    // }

    public function render()
    {
        return view('livewire.booking-component', [
            'availableDates' => $this->availableDates,
        ]);
    }
}