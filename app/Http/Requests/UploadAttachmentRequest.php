<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UploadAttachmentRequest extends Request
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
            'file' => 'max:25000'
        ];
    }

     public function response(array $errors)
     {
                  
                       
                if(isset($errors['file']))
                {
                    $errors['file.max'] = 'File cannot exceed 25 MB';
                }
               
           return response()->json([
                    'error' => $errors['file.max']
                ], 500);
     }
}
