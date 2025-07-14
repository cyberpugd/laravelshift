<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class SaveTicketRequest extends Request
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
            'sub_category' => 'required',
            'title' => 'required|max:255',
            'description' => 'max:4000',
            'caller' => 'required',
            'urgency' => 'required',
            'agent' => 'required',
        ];
    }
}
