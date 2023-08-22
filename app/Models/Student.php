<?php

namespace App\Models;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
 use HasFactory;
 use SoftDeletes;

 protected $table      = 'students';
 protected $primaryKey = 'id';
 protected $fillable   = [
  'name',
  'grade',
  'email',
  'phone',
  'status',
  'deleted_at',
 ];

 public function student()
 {

  return $this->hasMany(User::class, 'school_id', 'id');
 }

// Kiran:  To be Used after creating Foreign key between student and master_class table.
// Done
 public function program()
 {

  return $this->belongsTo(Program::class, 'grade', 'id');
 }

}
