<?php

namespace App\Models;

use App\Kroombase\Model;

class Message extends Model
{
    protected string $table = 'messages';

    protected array $fillable = ['name', 'email', 'subject', 'body', 'is_read'];

    protected array $casts = ['is_read' => 'boolean'];
}
