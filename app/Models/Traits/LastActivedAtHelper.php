<?php

namespace App\Models\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

trait LastActivedAtHelper
{
    protected $hash_perfix = 'handle_guitar_last_actived_at_';
    protected $field_perfix = 'user_';

    public function recordLastActivedAt()
    {
        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());
        $field = $this->getHashField();

        $now = Carbon::now()->toDateTimeString();
        Redis::hset($hash, $field, $now);
    }

    public function syncUserActivedAt()
    {
        $hash = $this->getHashFromDateString(Carbon::yesterday()->toDateString());
        $dates = Redis::hgetall($hash);

        foreach ($dates as $user_id => $actived_at) {
            $user_id = str_replace($this->field_perfix, '', $user_id);
            if ($user = $this->find($user_id)) {
                $user->last_actived_at = $actived_at;
                $user->save();
            }
        }

        Redis::del($hash);
    }

    public function getLastActivedAtAttribute($value)
    {
        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());

        $field = $this->getHashField();
        $datetime = Redis::hget($hash, $field) ? : $value;

        if ($datetime) {
            return new Carbon($datetime);
        } else {
            return $this->created_at;
        }
    }

    public function getHashFromDateString($date)
    {
        return $this->hash_perfix . $date;
    }

    public function getHashField()
    {
        return $this->field_perfix . $this->id;
    }
}