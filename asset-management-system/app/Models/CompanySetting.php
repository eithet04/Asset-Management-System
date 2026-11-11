<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['company_name', 'contact_email', 'phone', 'timezone', 'currency'])]
class CompanySetting extends Model {}
