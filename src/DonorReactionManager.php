<?php

namespace App;

class DonorReactionManager
{
    private array $reactions = [];

    public function addReaction(string $userId, string $postId, string $type): void
    {
        $this->reactions[] = [
            'user_id' => $userId,
            'post_id' => $postId,
            'type' => $type,
        ];
    }

    public function removeReaction(string $userId, string $postId): void
    {
        $this->reactions = array_filter($this->reactions, function ($reaction) use ($userId, $postId) {
            return !($reaction['user_id'] === $userId && $reaction['post_id'] === $postId);
        });
    }

    public function getReactions(): array
    {
        return $this->reactions;
    }
}
