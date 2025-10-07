<?php

use PHPUnit\Framework\TestCase;
use App\DonorCommentAvailVerifier;

class DonorCommentAvailVerifierTest extends TestCase
{
    public function test_comment_box_is_available_on_post()
    {
        $post = new DonorCommentAvailVerifier(true);

        $this->assertTrue($post->isCommentBoxAvailable(), 'Comment box should be available on the post.');
    }

    public function test_comment_box_is_not_available_on_post()
    {
        $post = new DonorCommentAvailVerifier(false);

        $this->assertFalse($post->isCommentBoxAvailable(), 'Comment box should not be available on the post.');
    }
}
