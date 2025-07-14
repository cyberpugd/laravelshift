<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateWorkOrderRequest extends Request
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
            'subject' => 'required|max:255',
            'work_requested' => 'required',
            'assigned_to' => 'required|numeric|exists:users,id',
            'due_date' => 'required|date|date_format:m/d/Y g:i a',
        ];
    }
}
