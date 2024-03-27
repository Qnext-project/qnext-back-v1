<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDoctorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'username' => 'nullable|string',
            'password' => 'nullable|string|confirmed',
            'acl' => 'nullable|array',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'media_id' =>'nullable|integer',
            'expertise_id' => 'nullable|integer',
            'title_id' => 'nullable|integer',
            'role' => 'nullable|string'
        ];
    }
}
