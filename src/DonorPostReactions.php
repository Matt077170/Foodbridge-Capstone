<?php

namespace App;

class DonorPostReactions
{
    private array $validReactions = ['like', 'love', 'care', 'wow'];

    public function getValidReactions(): array
    {
        return $this->validReactions;
    }

    public function isReactionVisible(array $post): bool
    {
        return isset($post['show_reactions']) && $post['show_reactions'] === true;
    }

    public function isReactionValid(string $reaction): bool
    {
        return in_array($reaction, $this->validReactions);
    }
}
