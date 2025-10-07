<?php

use PHPUnit\Framework\TestCase;
use App\DonorReactionManager;

class DonorReactionManagerTest extends TestCase
{
    public function test_user_can_remove_reaction()
    {
        $manager = new DonorReactionManager();

        $userId = 'user123';
        $postId = 'post456';

        $manager->addReaction($userId, $postId, 'like');

        $this->assertCount(1, $manager->getReactions());

        $manager->removeReaction($userId, $postId);

        $this->assertCount(0, $manager->getReactions());
    }
}
