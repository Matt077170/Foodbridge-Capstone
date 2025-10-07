<?php

namespace App;

class DonorCommentAvailVerifier
{
    private bool $commentBoxVisible;

    public function __construct(bool $commentBoxVisible = true)
    {
        $this->commentBoxVisible = $commentBoxVisible;
    }

    public function isCommentBoxAvailable(): bool
    {
        return $this->commentBoxVisible;
    }
}
