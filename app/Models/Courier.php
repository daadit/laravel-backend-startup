<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Courier extends Model
{
    use HasFactory;

    public function list()
    {
        return DB::table('couriers')->get();
    }
}
