<?php

namespace App;

class DonorPostPaginator
{
    private array $posts;

    public function __construct(array $posts)
    {
        $this->posts = $posts;
    }

    public function paginate(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        return array_slice($this->posts, $offset, $perPage);
    }

    public function hasMorePages(int $page, int $perPage): bool
    {
        return count($this->posts) > $page * $perPage;
    }
}
