<?php

namespace App\Service\Database;

use App\Models\Clinic;
use Illuminate\Contracts\Validation\Rule;
use Ramsey\Uuid\Uuid;

class ClinicService {

    public function index($filter = [])
    {
        $orderBy = $filter['order_by'] ?? 'DESC';
        $per_page = $filter['per_page'] ?? 999;

        $query = Clinic::orderBy('created_at', $orderBy);

        $clinics = $query->simplePaginate($per_page);

        return $clinics->toArray();
    }

    public function detail($clinicId)
    {
        $clinic = Clinic::findOrfail($clinicId);

        return $clinic->toArray();
    }

    public function create($payload)
    {
        $clinic = new Clinic();
        $clinic->id = Uuid::uuid4()->toString();
        $clinic = $this->fill($clinic, $payload);
        $clinic->save();

        return $clinic;
    }

    private function fill(Clinic $clinic, array $attributes)
    {

        foreach ($attributes as $key => $value) {
            $clinic->$key = $value;
        }

        Validator::make($clinic->toArray(), [
            'name' => 'required|string',
            'address' => 'required|string',
            'about' => 'required|string',
            'facility' => 'required',
            'service' => 'required',
            'contact' => 'nullable|numeric',
            'email' => 'nullable|email',
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ])->validate();

        return $clinic;
    }

}
