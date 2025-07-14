<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateChangeTicketRequest extends Request
{
    protected $redirect = '/change-control/create';
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
            'audit_unit' => 'required|numeric',
            'it_approver' => 'required|different:change_owner|different:bus_approver|exists:users,id|numeric',
            'bus_approver' => 'different:change_owner|exists:users,id|numeric',
            'change_owner' => 'required|exists:users,id|numeric',
            'change_type' => 'required|max:255',
            'start_date' => 'required|date|date_format:m/d/Y g:i a',
            'end_date' => 'required|date|date_format:m/d/Y g:i a|after:start_date',
            'change_description' => 'required',
            'roll_out_plan' => 'required',
            'change_reason' => 'required',
            'back_out_plan' => 'required',
            'servers' => 'required',
            'test_plan' => 'required',
            'business_impact' => 'required',
        ];
    }

    public function messages()
    {
        return [
               'it_approver.required' => 'The IT approver field is required.',
               'it_approver.different' => 'The IT approver cannot be the same as the Owner or Bus. Approver.',
        ];
    }
}
