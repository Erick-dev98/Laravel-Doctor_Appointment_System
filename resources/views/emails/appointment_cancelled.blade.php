@component('mail::message')
# Appointment Confirmation

Dear {{ $appointmentData['recipient_name'] }},

An appointment has been cancelled with the following details:

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

### Appointment Cancelled by:
- **Name:** {{ $appointmentData['cancelled_by'] }}
- **Role:** 
@if ($appointmentData['role'] == 1)
Doctor
@elseif ($appointmentData['role'] == 2)
Admin
@else
Patient
@endif


@if($appointmentData['recipient_role'] == 'admin')
## Admin Notification
You are receiving this email because an appointment has been cancelled in your system.
@endif

@if($appointmentData['recipient_role'] == 'doctor')
## Doctor Notification
An appointment has been cancelled.
@endif

@if($appointmentData['recipient_role'] == 'patient')
## Patient Notification
Your appointment has been cancelled. You can still book another appointment.
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent