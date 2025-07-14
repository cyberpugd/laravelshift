<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class SaveChangeTicketRequest extends Request
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
            'audit_unit' => 'required',
            'it_approver' => 'required_with:it_approver|different:change_owner|different:bus_approver',
            'bus_approver' => 'different:change_owner',
            'change_owner' => 'required',
            'change_type' => 'required',
            'start_date' => 'required',
            'end_date' => 'required|date|after:start_date',
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
