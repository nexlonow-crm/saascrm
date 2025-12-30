<?php

use App\Models\Workspace;

/**
 * Get the current workspace instance.
 */
function ws(): ?Workspace
{
    if (!app()->bound('currentWorkspace')) {
        return null;
    }

    return app('currentWorkspace');
}

/**
 * Generate a route URL specifically for the current workspace context.
 */
function ws_route(string $name, array $params = []): string
{
    $workspace = ws();

    // Fallback: If no workspace is active, just return a standard route
    if (!$workspace) {
        return route($name, $params);
    }

    return route($name, array_merge(['workspace' => $workspace->slug], $params));
}