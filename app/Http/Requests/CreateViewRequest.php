<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateViewRequest extends Request
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
        if (isset($this->filter_column)) {
            $rules = [
                              'filter_column.*.0' => 'required',
                              'filter_column.*.1' => 'required',
                              'filter_column.*.2' => 'required',
                              'filter_column.*.3' => 'required_if:filter_column.*.1,between',
                              'name' => 'required|max:50|regex:/(^[a-zA-Z0-9.\-\_ ]+$)+/',
                              'selectedColumns' => 'required',
                         ];
        } else {
            $rules = [
                    'name' => 'required|max:50|regex:/(^[a-zA-Z0-9.\-\_ ]+$)+/',
                    'selectedColumns' => 'required',
               ];
        }
        return $rules;
    }

    public function messages()
    {
        return [
               'filter_column.*.0.required' => 'The filter column is required',
               'filter_column.*.1.required' => 'The filter operator is required',
               'filter_column.*.2.required' => 'The filter criteria is required',
               'filter_column.*.3.required_if' => 'The second date field is required when using between as an operator',
               'name.regex' => 'The Query Name format is invalid.',
         ];
    }
}
