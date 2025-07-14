<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateAnnouncementRequest extends Request
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
            'type' => 'required|in:info,warning,danger|max:255',
            'title' => 'required|max:255',
            'start_date' => 'required|date|date_format:m/d/Y g:i a',
            'end_date' => 'required|date|date_format:m/d/Y g:i a',
        ];
    }
}
