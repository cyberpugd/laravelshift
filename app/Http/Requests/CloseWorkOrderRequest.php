<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CloseWorkOrderRequest extends Request
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
               'work_completed' => 'required_if:status,closed',
               'status' => 'required|in:open,closed',
               'assigned_to' => 'required|exists:users,id|numeric',
          ];
    }
}
