<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreatePortalTicketRequest extends Request
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
            'urgency' => 'required',
          ];
    }

     public function messages()
    {
         return [
               'sub_category.required' => 'The category field is required.',
               'title.required' => 'The subject field is required.',
        ];
    }
}
