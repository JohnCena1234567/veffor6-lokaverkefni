<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model {
    use RecordsActivity;
    /**
     * Don't auto-apply mass assignment protection.
     * 
     * @var array
     */
	protected $guarded = [];

    /**
     * The relationships to always eager-load.
     * 
     * @var array
     */
    protected $with = ['creator', 'channel'];

    /**
     * Boot the model.
     */
    protected static function boot() {
        parent::boot();

        static::addGlobalScope('replyCount', function($builder) {
            $builder->withCount('replies');
        });

        static::deleting(function($thread) {
            $thread->replies->each->delete();
        });
    }

    /**
     * Get a string path for the thread.
     * 
     * @return string
     */
    public function path() {
    	return "/threads/{$this->channel->slug}/{$this->id}";
    }

    public function replies() {
    	return $this->hasMany(Reply::class);
    }

    public function creator() {
    	return $this->belongsTo(User::class, 'user_id');
    }

    public function channel() {
        return $this->belongsTo(Channel::class);
    }

    public function addReply($reply) {
    	$this->replies()->create($reply);
    }

    public function scopeFilter($query, $filters) {
        return $filters->apply($query);
    }
}
