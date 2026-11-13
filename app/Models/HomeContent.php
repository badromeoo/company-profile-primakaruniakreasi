<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['section_key', 'content_value', 'group'])]
class HomeContent extends Model {}
