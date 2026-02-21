<?php 

class Appointment{
    public function __construct(
        public int $patient_id,
        public int $doctor_id,
        public string $appointment_date,
        public string $appointment_time,
        public ?string $department = null,
        public string $status = 'pending'


    ){}

public function toArray(): array{
    return[
         'patient_id'       => $this->patient_id,
            'doctor_id'        => $this->doctor_id,
            'department'       => $this->department,
            'appointment_date' => $this->appointment_date,
            'appointment_time' => $this->appointment_time,
            'status'           => $this->status
    ];
}
}
?>