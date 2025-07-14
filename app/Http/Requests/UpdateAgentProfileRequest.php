<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UpdateAgentProfileRequest extends Request
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
            'status' => 'required|numeric|in:1,0',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|max:255|email',
            'phone_number' => 'required|max:255',
            'location' => 'required|numeric|exists:locations,id',
            'timezone' => 'required|max:255',
            'teams.*' => 'numeric|exists:teams,id'
        ];
    }
}
