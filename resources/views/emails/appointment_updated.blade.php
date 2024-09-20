@component('mail::message')
# Appointment Confirmation

Dear {{ $appointmentData['recipient_name'] }},

An appointment has been rescheduled with the following details:

### New Appointment Details:
- **Date:** {{ $appointmentData['date'] }}
- **Time:** {{ $appointmentData['time'] }}
- **Location:** {{ $appointmentData['location'] }}

### Old Appointment Details:
- **Date:** {{ $appointmentData['date'] }}
- **Time:** {{ $appointmentData['time'] }}
- **Location:** {{ $appointmentData['location'] }}

### Patient Details:
- **Name:** {{ $appointmentData['patient_name'] }}
- **Email:** {{ $appointmentData['patient_email'] }}

### Doctor Details:
- **Name:** {{ $appointmentData['doctor_name'] }}
- **Specialization:** {{ $appointmentData['doctor_specialization'] }}

<!-- ### Appointment Updated by:
- **Name:** {{ $appointmentData['cancelled_by'] }}
- **Role:** 
@if ($appointmentData['role'] == 1)
Doctor
@elseif ($appointmentData['role'] == 2)
Admin
@else
Patient
@endif -->


@if($appointmentData['recipient_role'] == 'admin')
## Admin Notification
You are receiving this email because an appointment has been rescheduled in your system.
@endif

@if($appointmentData['recipient_role'] == 'doctor')
## Doctor Notification
Appointment rescheduled from .
@endif

@if($appointmentData['recipient_role'] == 'patient')
## Patient Notification
Your appointment has been rescheduled. 
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent