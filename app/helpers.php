<?php

use App\Models\Workspace;

function ws(): ?Workspace
{
    return app()->bound('currentWorkspace') ? app('currentWorkspace') : null;
}

function ws_route(string $name, array $params = []): string
{
    // Only for routes that REQUIRE {workspace}
    $workspace = ws();
    if (!$workspace) {
        return route('app'); // safe fallback
    }

    return route($name, array_merge(['workspace' => $workspace->slug], $params));
}
