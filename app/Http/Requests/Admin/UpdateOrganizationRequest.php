<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizationRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'legal_address' => ['nullable', 'string', 'max:500'],
            'registration_number' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'tin' => ['nullable', 'string', 'max:255'],
            'emails' => ['nullable', 'array'],
            'emails.*' => ['email'],
            'phones' => ['nullable', 'array'],
            'mobile_apps' => ['nullable', 'array'],
            'social_media' => ['nullable', 'array'],
            'auth_type' => ['sometimes', 'required', 'string', 'in:api_key,bearer,none'],
            'auth_value' => ['nullable', 'string'],
            'base_url' => ['nullable', 'url', 'max:255'],
            'api_key' => ['nullable', 'string', 'max:255'],
            'webhook_secret' => ['nullable', 'string', 'max:255'],
            'sync_type' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'required', 'string', 'in:active,inactive'],
        ];
    }
}
