<?php

use PHPUnit\Framework\TestCase;
use App\DonorPostVisibility;

class DonorPostVisibilityTest extends TestCase
{
    public function testPostAppearsOnDashboardOrOrganizationPage()
    {
        $manager = new DonorPostVisibility();

        $post = [
            'id' => 1,
            'title' => 'New Feature',
            'content' => 'We launched something cool!',
            'visible_on_dashboard' => true,
            'visible_on_organization_page' => false,
        ];

        $manager->addPost($post);

        $this->assertTrue(
            $manager->isPostVisible($post),
            'Post should appear on either dashboard or organization page'
        );
    }

    public function testPostNotVisibleAnywhere()
    {
        $manager = new DonorPostVisibility();

        $post = [
            'id' => 2,
            'title' => 'Hidden Update',
            'content' => 'This post is not visible',
            'visible_on_dashboard' => false,
            'visible_on_organization_page' => false,
        ];

        $manager->addPost($post);

        $this->assertFalse(
            $manager->isPostVisible($post),
            'Post should not appear on dashboard or organization page'
        );
    }
}
