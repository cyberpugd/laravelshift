<?php

namespace App\Http;

class Flash {

     /**
      * Gets called by methods below to create a flash message in the session
      * @param  [type] $title   Title of the message
      * @param  [type] $message Message to display to the user
      * @param  [type] $level   The type of message to display
      * @param  string $key     The key stored in the session ex: "session()->has('key')""
      * @return           
      */
     public function create($title, $message, $level, $key = 'flash_message', $buttonText = null)
     {
          session()->flash($key, [
               'title'   => $title,
               'message'      => $message,
               'level'        => $level,
               'buttonText'=> $buttonText
          ]);
     }


     /**
      * Basic Info message
      * @param  [type] $title   Title of the message
      * @param  [type] $message The actual message
      * @return [type]          [description]
      */
     public function info($title, $message)
     {
          $this->create($title, $message, 'info');
     }    


     /**
      * Success message
      * @param  [type] $title   Title of the message
      * @param  [type] $message The actual message
      * @return [type]          [description]
      */
     public function success($title, $message)
     {
          $this->create($title, $message, 'success');
     }


     /**
      * Error message
      * @param  [type] $title   Title of the message
      * @param  [type] $message The actual message
      * @return [type]          [description]
      */
     public function error($title, $message)
     {
          $this->create($title, $message, 'error');
     }


     /**
      * Flash message for a user to confirm
      * @param  [type] $title      [Title of the message]
      * @param  [type] $message    [The actual message]
      * @param  string $level      [The type of message to be displayed]
      * @param  string $buttonText [What you want the button text to say]
      * @return [type]             [description]
      */
     public function confirm($title, $message, $level = 'success', $buttonText = 'Okay')
     {
          $this->create($title, $message, $level, 'flash_message_confirm', $buttonText);
     }


     /**
      * Creates a basic flash message in the session
      * @param  [type] $message [The actual message to be displayed]
      * @param  [type] $level   [The type of message (bootstrap classes)]
      * @return [type]          [description]
      */
     public function createBasic($message, $level)
     {
          session()->flash('message', [
               'level'   => $level,
               'message'      => $message
          ]);
     }

     /**
      * Creates a basic flash message in the session
      * @param  [type] $message [The actual message to be displayed]
      * @param  [type] $level   [The type of message (bootstrap classes)]
      * @return [type]          [description]
      */
     public function createBasicStay($message, $level)
     {
          session()->flash('messagestay', [
               'level'   => $level,
               'message'      => $message
          ]);
     }


     /**
      * Shows a basic message
      * @param  [type] $message [The actual message to be displayed]
      * @return [type]          [description]
      */
     public function basicInfo($message)
     {
          $this->createBasic($message, 'info');
     }    

     
     /**
      * Shows a success message
      * @param  [type] $message [The actual message to be displayed]
      * @return [type]          [description]
      */
     public function basicSuccess($message)
     {
          $this->createBasic($message, 'success');
     }


     /**
      * Shows a Warning message
      * @param  [type] $message [The actual message to be displayed]
      * @return [type]          [description]
      */
     public function basicWarning($message)
     {
          $this->createBasic($message, 'warning');
     }



     /**
      * Shows a basic message that stays
      * @param  [type] $message [The actual message to be displayed]
      * @return [type]          [description]
      */
     public function basicInfoStay($message)
     {

          $this->createBasicStay($message, 'info');
     }    


     /**
      * Shows a basic message that stays
      * @param  [type] $message [The actual message to be displayed]
      * @return [type]          [description]
      */
     public function basicSuccessStay($message)
     {
          $this->createBasicStay($message, 'success');
     }

     /**
      * Shows a basic message that stays
      * @param  [type] $message [The actual message to be displayed]
      * @return [type]          [description]
      */
     public function basicWarningStay($message)
     {
          $this->createBasicStay($message, 'warning');
     }

}