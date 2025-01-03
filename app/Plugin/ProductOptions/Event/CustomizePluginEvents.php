<?php

namespace Plugin\ProductOptions\Event;

final class CustomizePluginEvents
{
    /**
     * Admin/DiscountCodeController
     */
    // index
    public const ADMIN_OPTIONS_INDEX_INITIALIZE       = 'admin.options.index.initialize';
    public const ADMIN_OPTIONS_SEARCH                 = 'admin.options.index.search';
    public const ADMIN_OPTIONS_VISIBILITY_COMPLETE    = 'admin.options.visibility.complete';
    public const ADMIN_OPTIONS_DELETE_COMPLETE        = 'admin.options.delete.complete';
    public const ADMIN_OPTIONS_UPDATE_INITIALIZE      = 'admin.options.update.initialize';
    public const ADMIN_OPTIONS_UPDATE_COMPLETE        = 'admin.options.update.complete';
}
