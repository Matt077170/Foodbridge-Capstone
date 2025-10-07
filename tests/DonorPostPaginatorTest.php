<?php

use PHPUnit\Framework\TestCase;
use App\DonorPostPaginator;

class DonorPostPaginatorTest extends TestCase
{
    private array $samplePosts;

    protected function setUp(): void
    {
        $this->samplePosts = [];
        for ($i = 1; $i <= 25; $i++) {
            $this->samplePosts[] = ['id' => $i, 'title' => "Post $i"];
        }
    }

    public function testPaginationReturnsCorrectNumberOfPosts()
    {
        $paginator = new DonorPostPaginator($this->samplePosts);
        $page1 = $paginator->paginate(1, 10);
        $this->assertCount(10, $page1);
        $this->assertEquals('Post 1', $page1[0]['title']);
        $this->assertEquals('Post 10', $page1[9]['title']);
    }

    public function testPaginationReturnsRemainingPostsOnLastPage()
    {
        $paginator = new DonorPostPaginator($this->samplePosts);
        $page3 = $paginator->paginate(3, 10);
        $this->assertCount(5, $page3);
        $this->assertEquals('Post 21', $page3[0]['title']);
        $this->assertEquals('Post 25', $page3[4]['title']);
    }

    public function testHasMorePagesReturnsTrueWhenMoreExist()
    {
        $paginator = new DonorPostPaginator($this->samplePosts);
        $this->assertTrue($paginator->hasMorePages(1, 10));
        $this->assertTrue($paginator->hasMorePages(2, 10));
    }

    public function testHasMorePagesReturnsFalseOnLastPage()
    {
        $paginator = new DonorPostPaginator($this->samplePosts);
        $this->assertFalse($paginator->hasMorePages(3, 10));
    }
}
