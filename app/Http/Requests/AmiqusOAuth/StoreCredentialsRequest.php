<?php

namespace App\Http\Requests\AmiqusOAuth;

use Illuminate\Foundation\Http\FormRequest;

class StoreCredentialsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'client_id' => 'required|string|max:255',
            'client_secret' => 'required|string|max:255',
            'redirect_uri' => 'required|url|max:255',
            'scope' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The integration name is required.',
            'client_id.required' => 'The client ID is required.',
            'client_secret.required' => 'The client secret is required.',
            'redirect_uri.required' => 'The redirect URI is required.',
            'redirect_uri.url' => 'The redirect URI must be a valid URL.',
        ];
    }
}
