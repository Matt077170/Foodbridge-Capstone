<?php

namespace App;

class DonorPostVisibility
{
    private array $dashboard = [];
    private array $organizationPage = [];

    public function addPost(array $post): void
    {
        if ($post['visible_on_dashboard'] ?? false) {
            $this->dashboard[] = $post;
        }

        if ($post['visible_on_organization_page'] ?? false) {
            $this->organizationPage[] = $post;
        }
    }

    public function isPostVisible(array $post): bool
    {
        return in_array($post, $this->dashboard) || in_array($post, $this->organizationPage);
    }

    public function getDashboard(): array
    {
        return $this->dashboard;
    }

    public function getOrganizationPage(): array
    {
        return $this->organizationPage;
    }
}
