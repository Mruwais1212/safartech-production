<?php

namespace App\Models\Panel;

use Illuminate\Database\Eloquent\Model;

class AiPlannerSection extends Model
{
    protected $guarded = ['id'];

    protected $fillable = ['name_ar', 'name_en', 'icon', 'sort'];
}
