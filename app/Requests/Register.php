<?php
namespace App\Requests;

class Register
{
    private $data;
    private $errors = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function validate()
    {
      
        if (empty($this->data['name'])) {
            $this->errors['name'] = 'Name is required';
        }

       
        if (empty($this->data['email'])) {
            $this->errors['email'] = 'Email is required';
        } elseif (!filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Invalid email format';
        }

       
        if (empty($this->data['password'])) {
            $this->errors['password'] = 'Password is required';
        } elseif (strlen($this->data['password']) < 6) {
            $this->errors['password'] = 'Password must be at least 6 characters';
        }

       
         if (empty($this->data['role'])) {
        $this->errors['role'] = 'Role is required';
    } else {
        $validRoles = ['admin', 'doctor', 'patient'];
        if (!in_array($this->data['role'], $validRoles)) {
            $this->errors['role'] = 'Invalid role selected';
        }
    }

    return empty($this->errors);
    }

    public function errors()
    {
        return $this->errors;
    }

    public function getData()
    {
        return $this->data;
    }
}
