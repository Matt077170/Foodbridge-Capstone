<?php

use App\DonorPostReactions;
use PHPUnit\Framework\TestCase;


class DonorPostReactionsTest extends TestCase
{
    private DonorPostReactions $handler;

    protected function setUp(): void
    {
        $this->handler = new DonorPostReactions();
    }

    public function testReactionOptionsAreVisible()
    {
        $post = [
            'id' => 1,
            'title' => 'Hello World',
            'show_reactions' => true
        ];

        $this->assertTrue(
            $this->handler->isReactionVisible($post),
            'Reactions should be visible on the post'
        );
    }

    public function testReactionOptionsAreHidden()
    {
        $post = [
            'id' => 2,
            'title' => 'Hidden Post',
            'show_reactions' => false
        ];

        $this->assertFalse(
            $this->handler->isReactionVisible($post),
            'Reactions should not be visible on the post'
        );
    }

    public function testValidReactionIsAccepted()
    {
        $this->assertTrue(
            $this->handler->isReactionValid('love'),
            'Valid reaction should be accepted'
        );
    }

    public function testInvalidReactionIsRejected()
    {
        $this->assertFalse(
            $this->handler->isReactionValid('confused'),
            'Invalid reaction should be rejected'
        );
    }

    public function testReactionListIsNotEmpty()
    {
        $this->assertNotEmpty(
            $this->handler->getValidReactions(),
            'Reaction options should be available'
        );
    }
}
