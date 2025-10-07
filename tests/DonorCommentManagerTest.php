<?php

use PHPUnit\Framework\TestCase;
use App\DonorCommentManager;

class DonorCommentManagerTest extends TestCase
{
    public function test_comment_has_correct_donor_user_info_and_timestamp()
    {
        $manager = new DonorCommentManager();

        $userId = 'user007';
        $postId = 'postXYZ';
        $content = 'Timestamp test comment';

        $before = time();
        $manager->postComment($userId, $postId, $content);
        $after = time();

        $comments = $manager->getComments();
        $this->assertCount(1, $comments);

        $comment = $comments[0];

        $this->assertEquals($userId, $comment['user_id']);
        $this->assertEquals($postId, $comment['post_id']);
        $this->assertEquals($content, $comment['content']);
        $this->assertGreaterThanOrEqual($before, $comment['timestamp']);
        $this->assertLessThanOrEqual($after, $comment['timestamp']);
    }
}
