<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminSettings extends BaseModel
{
    protected $fillable = [
          'mail_port',
          'mail_server',
          'mail_user',
          'mail_password',
          'mail_folder',
          'mail_processed_folder',
          'email_address'
    ];


    protected $table = 'admin_settings';
}
