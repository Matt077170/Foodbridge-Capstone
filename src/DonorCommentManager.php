<?php

namespace App;

class DonorCommentManager
{
   private array $comments = [];

    public function postComment(string $userId, string $postId, string $content): void
    {
        $this->comments[] = [
            'user_id' => $userId,
            'post_id' => $postId,
            'content' => $content,
            'timestamp' => time(), // UNIX timestamp
        ];
    }

    public function getComments(): array
    {
        return $this->comments;
    }
}
