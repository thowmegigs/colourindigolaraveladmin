<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'description',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(Vendor::class,'user_id','id');
    }
    public function replies()
    {
        return $this->HasMany(TicketReply::class,'ticket_id','id');
    }
}
