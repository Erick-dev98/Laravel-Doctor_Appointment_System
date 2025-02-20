@component('mail::message')
# Appointment Confirmation

Dear {{ $appointmentData['recipient_name'] }},

An appointment has been successfully created with the following details:

### Appointment Details:
- **Date:** {{ $appointmentData['date'] }}
- **Time:** {{ $appointmentData['time'] }}
- **Location:** {{ $appointmentData['location'] }}

### Patient Details:
- **Name:** {{ $appointmentData['patient_name'] }}
- **Email:** {{ $appointmentData['patient_email'] }}

### Doctor Details:
- **Name:** {{ $appointmentData['doctor_name'] }}
- **Specialization:** {{ $appointmentData['doctor_specialization'] }}

@if($appointmentData['recipient_role'] == 'admin')
## Admin Notification
You are receiving this email because an appointment has been scheduled in your system.

@component('mail::button', ['url' => 'http://127.0.0.1:8000/login'])
View Appointment
@endcomponent
@endif

@if($appointmentData['recipient_role'] == 'doctor')
## Doctor Notification
You have a new appointment scheduled. Please review the details and prepare accordingly.

@component('mail::button', ['url' => 'http://127.0.0.1:8000/login'])
View Appointment
@endcomponent
@endif

@if($appointmentData['recipient_role'] == 'patient')
## Patient Notification
Your appointment has been successfully scheduled. For in-person visits, please make sure to arrive on time and bring any necessary documents. For live consultations, please ensure you are available online at the scheduled time and have any necessary documents ready.

@component('mail::button', ['url' => 'http://127.0.0.1:8000/login'])
View Appointment
@endcomponent
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent