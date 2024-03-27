<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditDoctorRequest extends FormRequest
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
            'media_id' => 'nullable|string',
            'expertise_id' => 'nullable',
            'title_id' => 'nullable',
            'role' => 'nullable|string'
        ];
    }
}
