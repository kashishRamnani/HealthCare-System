<?php
namespace App\Services;

use App\Repositories\DoctorRepository;
use App\Exceptions\ValidationException;
use App\Repositories\UserRepository; 

class DoctorService
{
    private DoctorRepository $repo;
    private UserRepository $userRepo;

    public function __construct(
        ?DoctorRepository $repo = null,
        ?UserRepository $userRepo = null
    ) {
        $this->repo = $repo ?? new DoctorRepository();
        $this->userRepo = $userRepo ?? new UserRepository();
    }

    public function create(array $data): bool
    {
        // Validation
        foreach (['name', 'email', 'department', 'specialization'] as $field) {
            if (empty($data[$field])) {
                throw new ValidationException("$field is required");
            }
        }

        // 1️⃣ Create user first
        $userId = $this->userRepo->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => 'doctor',
        ]);

        // 2️⃣ Create doctor record with userId
        return $this->repo->create($userId, [
            'department' => $data['department'],
            'specialization' => $data['specialization'],
        ]);
    }

    public function getAll(): array
    {
        return $this->repo->getAll();
    }

    public function getDoctorById(int $id): ?array
    {
        return $this->repo->getById($id);
    }

    public function update(int $id, array $data): bool
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }
}
