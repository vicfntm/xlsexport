<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class XlsData extends Model
{
    protected $fillable = ['first_name', 'middle_name', 'surname', 'birth_year', 'occupation', 'annual_salary'];
}
