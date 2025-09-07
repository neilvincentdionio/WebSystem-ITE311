<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;

// Core CI4 Filters
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;

// Custom App Filters
use App\Filters\AuthFilter;

class Filters extends BaseFilters
{
    /**
     * Configures aliases for Filter classes.
     *
     * @var array<string, class-string|list<class-string>>
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,

        // Custom filter for login protection
        'auth'          => AuthFilter::class,
    ];

    /**
     * Special filters that always run.
     */
    public array $required = [
        'before' => [
            // 'forcehttps', // Uncomment to enforce HTTPS globally
            // 'pagecache',  // Uncomment if you want page caching
        ],
        'after' => [
            'toolbar',       // Debug Toolbar
            'secureheaders', // Secure headers
            // 'performance', // Uncomment if you want performance metrics
        ],
    ];

    /**
     * Filters applied globally before/after requests.
     */
    public array $globals = [
        'before' => [
            // 'csrf',
            // 'honeypot',
            // 'invalidchars',
        ],
        'after' => [
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    /**
     * Filters per HTTP method.
     */
    public array $methods = [];

    /**
     * Filters for specific URI patterns.
     */
    public array $filters = [
        'auth' => [
            'before' => [
                'dashboard/*',
                'dashboard',
                'account/*',
            ],
        ],
    ];
}
