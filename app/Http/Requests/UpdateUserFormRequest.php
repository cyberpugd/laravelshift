<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UpdateUserFormRequest extends Request
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
          $rules = [];
          $counter = 0;
          $hasTicketSubject = false;
          $hasTicketDescription = false;
               foreach($this->fields as $field) {
                    
                    $rules['fields.'.$counter.'.default_value'] = rtrim(
                         ($field['type'] == 'date' && $field['default_value'] != '' ? 'date|date_format:m/d/Y|' : '') . 
                         ($field['type'] == 'number' ? 'numeric|' : '') . 
                         ($field['type'] == 'select' ? 'required|':'') .
                         ($field['type'] == 'hidden' ? 'required|':'')
                    , '|');

                    

                    $rules['fields.'.$counter.'.is_required'] = rtrim(
                         ($field['ticket_subject'] == true ? 'accepted|' : '')  .
                         (($field['type'] == 'hidden') ? 'accepted|' : '') 
                    , '|');

                    if(!$hasTicketSubject) {
                         $hasTicketSubject = ($field['ticket_subject'] == true ? true : false);
                    }
                    if(!$hasTicketDescription) {
                         $hasTicketDescription = ($field['ticket_description'] == true ? true : false);
                    }

                    $counter = $counter +1;
               }
               
            $rules['name'] = 'required';
            $rules['subcategory_id'] = 'required';
            $rules['urgency'] = 'required';
            $rules['fields.*.label'] = 'required|distinct';
            $rules['fields.*.type'] = 'required';
            if(!$hasTicketSubject) {
               $rules['ticket_subject'] = 'required';
            }

            if(!$hasTicketDescription) {
               $rules['ticket_description'] = 'required';
            }
            return $rules;
    }

    public function messages()
    {
         $messages = [];
         foreach ($this->request->get('fields') as $key => $value){
               $keyValue = (integer)$key+1;
               $messages['fields.'. $key .'.label.required'] = 'Database field name is required';
               $messages['fields.'. $key .'.label.distinct'] = 'Database field name must be unique';
               $messages['subcategory_id.required'] = ' The category is required.';
               $messages['name.required'] = ' The form name is required.';
               $messages['ticket_subject.required'] = ' A ticket subject must be chosen for at least one field.';
               $messages['ticket_description.required'] = ' A ticket description must be chosen for at least one field.';
               $messages['name.urgency'] = ' The urgency is required.';
               $messages['fields.'. $key .'.type.required'] = 'The field type is required';
               $messages['fields.'. $key .'.default_value.date'] = 'Must be a date';
               $messages['fields.'. $key .'.default_value.date_format'] = 'Does not match the format mm/dd/yyyy';
               $messages['fields.'. $key .'.default_value.numeric'] = 'Must be a number';
               $messages['fields.'. $key .'.default_value.max'] = 'Cannot exceed 255 characters';
               if($value['type'] == 'select') {
                    $messages['fields.'. $key .'.default_value.required'] = 'At least one default value is required when choosing select as a field type';
               } 


               if($value['type'] == 'hidden') {
                    if($value['default_value'] == '') {
                         $messages['fields.'. $key .'.is_required.accepted'] = 'You are required to check this box when field type is hidden.';
                    } 
                    if($value['default_value'] == '') {
                         $messages['fields.'. $key .'.default_value.required'] = 'Default value is requred when the field type is hidden.';
                    } 
               } 
               if($value['ticket_subject'] == true) {
                    $messages['fields.'. $key .'.is_required.accepted'] = 'The required checkbox must be checked when ticket subject is chosen.';
               }
         }
         return $messages;
    }
}
