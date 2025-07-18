<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'content', 'user_id'];
    protected $appends = ['author_name',  'author_is_admin'];

    /**
     * Get the user that owns the post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the display name for the post author
     */
    public function getAuthorNameAttribute()
    {
        if (!$this->user || $this->user->is_deleted) {
            return 'Deleted User';
        }
        
        return $this->user->name;
    }

    public function getAuthorIsAdminAttribute()
    {
        return $this->user ? (bool)$this->user->is_admin : false;
    }
}
