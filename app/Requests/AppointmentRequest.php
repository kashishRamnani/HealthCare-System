<?php 
namespace App\Requests;

use App\Exceptions\ValidationException;

class AppointmentRequest {
    private array $errors = [];

    public function __construct(private array $data){}

    public function validate(): bool
    {
        $required = [
            'doctor_name',
            'patient_name',       
            'appointment_date',
            'appointment_time'
        ];

        foreach ($required as $field) {
            if (empty($this->data[$field])) {
                throw new ValidationException("$field is required");
            }
        }

        // Auto-set status if not provided
        if (!isset($this->data['status'])) {
            $this->data['status'] = 'pending';
        }

        return empty($this->errors);
    }

    public function errors(): array {
        return $this->errors;
    }

    public function getData(): array {
        return $this->data;
    }
}
?>
