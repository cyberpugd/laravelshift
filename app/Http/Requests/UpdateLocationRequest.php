<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UpdateLocationRequest extends Request
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
            // 'holidays' => 'required'
        ];
    }

    public function response(array $errors)
     {   
           //      if(isset($errors['holidays']))
           //      {
           //          $errors['holidays.required'] = 'Please select at least one holiday';
           //      }
               
           // return response()->json([
           //          'error' => $errors['holidays.required'],
           //          'location_id' => $this->id
           //      ], 500);
     }
}
