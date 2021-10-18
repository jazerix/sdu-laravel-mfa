<?php

namespace SDU\MFA;

trait SDUUser
{
    public function initializeSDUUser()
    {
        $this->casts['ad_groups'] = 'array';
        $this->appends[] = 'username';
    }

    public function getUsernameAttribute()
    {
        return explode('@', $this->email)[0];
    }
}
