<?php

/*
 * This file is part of the hyn/multi-tenants package.
 *
 * (c) Daniël Klabbers <daniel@klabbers.email>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://laravel-tenancy.com
 * @see https://github.com/hyn/multi-tenant
 */

use Hyn\Tenancy\Database\Connection;

return [
    'models' => [
        /**
         * Specify different models to be used for the global, system database
         * connection. These are also used in their relationships. Models
         * used have to implement their respective contracts and
         * either extend the SystemModel or use the trait
         * UsesSystemConnection.
         */

        // Must implement \Hyn\Tenancy\Contracts\Customer
        'customer' => \Hyn\Tenancy\Models\Customer::class,

        // Must implement \Hyn\Tenancy\Contracts\Hostname
        'hostname' => \Hyn\Tenancy\Models\Hostname::class,

        // Must implement \Hyn\Tenancy\Contracts\Website
        'website' => \Hyn\Tenancy\Models\Website::class
    ],
    'website' => [
        /**
         * Each website has a short random hash that identifies this entity
         * to the application. By default this id is randomized and fully
         * auto-generated. In case you want to force your own logic for
         * when you need to have a better overview of the complete
         * tenants folder structure, disable this and implement
         * your own id generation logic.
         */
        'disable-random-id' => false,

        /**
         * The random Id generator is responsible for creating the hash as mentioned
         * above. You can override what generator to use by modifying this value
         * in the configuration.
         *
         * @warn This won't work if disable-random-id is true.
         */
        'random-id-generator' => Hyn\Tenancy\Generators\Uuid\ShaGenerator::class,

        /**
         * Enable this flag in case you're using a driver that does not support
         * database username or database name with a length of more than 32 characters.
         *
         * This should be enabled for MySQL, but not for MariaDB and PostgreSQL.
         */
        'uuid-limit-length-to-32' => env('LIMIT_UUID_LENGTH_32', false),

        /**
         * Specify the disk you configured in the filesystems.php file where to store
         * the tenants specific files, including media, packages, routes and other
         * files for this particular website.
         *
         * @info If not set, will revert to the default filesystem.
         */
        'disk' => null,

        /**
         * Automatically generate a tenants directory based on the random id of the
         * website. Uses the above disk to store files to override system-wide
         * files.
         *
         * @info set to false to disable.
         */
        'auto-create-tenants-directory' => true,

        /**
         * Automatically rename the tenants directory when the random id of the
         * website changes. This should not be too common, but in case it happens
         * we automatically want to move files accordingly.
         *
         * @info set to false to disable.
         */
        'auto-rename-tenants-directory' => true,

        /**
         * Automatically deletes the tenants specific directory and all files
         * contained within.
         *
         * @see
         * @info set to true to enable.
         */
        'auto-delete-tenants-directory' => false,

        /**
         * Time to cache websites in minutes. Set to false to disable.
         */
        'cache' => 10,
    ],
    'hostname' => [
        /**
         * If you want the multi tenants application to fall back to a default
         * hostname/website in case the requested hostname was not found
         * in the database, complete in detail the default hostname.
         *
         * @warn this must be a FQDN, these have no protocol or path!
         */
        'default' => env('TENANCY_DEFAULT_HOSTNAME'),
        /**
         * The package is able to identify the requested hostname by itself,
         * disable to get full control (and responsibility) over hostname
         * identification. The hostname identification is needed to
         * set a specific website as currently active.
         *
         * @see src/Jobs/HostnameIdentification.php
         */
        'auto-identification' => env('TENANCY_AUTO_HOSTNAME_IDENTIFICATION', true),

        /**
         * In case you want to have the tenancy environment set up early,
         * enable this flag. This will run the tenants identification
         * inside a middleware. This will eager load tenancy.
         *
         * A good use case is when you have set "tenants" as the default
         * database connection.
         */
        'early-identification' => env('TENANCY_EARLY_IDENTIFICATION', false),

        /**
         * Abort application execution in case no hostname was identified. This will throw a
         * 404 not found in case the tenants hostname was not resolved.
         */
        'abort-without-identified-hostname' => false,

        /**
         * Time to cache hostnames in minutes. Set to false to disable.
         */
        'cache' => 10,

        /**
         * Automatically update the app.url configured inside Laravel to match
         * the tenants FQDN whenever a hostname/tenants was identified.
         *
         * This will resolve issues with password reset mails etc using the
         * correct domain.
         */
        'update-app-url' => false,
    ],
    'db' => [
        /**
         * The default connection to use; this overrules the Laravel database.default
         * configuration setting. In Laravel this is normally configured to 'mysql'.
         * You can set a environment variable to override the default database
         * connection to - for instance - the tenants connection 'tenants'.
         */
        'default' => env('TENANCY_DEFAULT_CONNECTION'),
        /**
         * Used to give names to the system and tenants database connections. By
         * default we configure 'system' and 'tenants'. The tenants connection
         * is set up automatically by this package.
         *
         * @see src/Database/Connection.php
         * @var system-connection-name The database connection name to use for the global/system database.
         * @var tenants-connection-name The database connection name to use for the tenants database.
         */
        'system-connection-name' => env('TENANCY_SYSTEM_CONNECTION_NAME', Connection::DEFAULT_SYSTEM_NAME),
        'tenants-connection-name' => env('TENANCY_TENANT_CONNECTION_NAME', Connection::DEFAULT_TENANT_NAME),

        /**
         * The tenants division mode specifies to what database websites will be
         * connecting. The default setup is to use a new database per tenants.
         * In case you prefer to use the same database with a table prefix,
         * set the mode to 'prefix'.
         *
         * @see src/Database/Connection.php
         */
        'tenants-division-mode' => env('TENANCY_DATABASE_DIVISION_MODE', 'database'),

        /**
         * The database password generator takes care of creating a valid hashed
         * string used for tenants to connect to the specific database. Do
         * note that this will only work in 'division modes' that set up
         * a connection to a separate database.
         */
        'password-generator' => Hyn\Tenancy\Generators\Database\DefaultPasswordGenerator::class,

        /**
         * The tenants migrations to be run during creation of a tenants. Specify a directory
         * to run the migrations from. If specified these migrations will be executed
         * whenever a new tenants is created.
         *
         * @info set to false to disable auto migrating.
         *
         * @warn this has to be an absolute path, feel free to use helper methods like
         * base_path() or database_path() to set this up.
         */
        'tenants-migrations-path' => database_path('migrations/tenants'),

        /**
         * The default Seeder class used on newly created databases and while
         * running artisan commands that fire seeding.
         *
         * @info requires tenants-migrations-path in order to seed newly created websites.
         *
         * @warn specify a valid fully qualified class name.
         * @example App\Seeders\AdminSeeder::class
         */
        'tenants-seed-class' => TenantDatabaseSeeder::class,

        /**
         * Automatically generate a tenants database based on the random id of the
         * website.
         *
         * @info set to false to disable.
         */
        'auto-create-tenants-database' => true,

        /**
         * Automatically generate the user needed to access the database.
         *
         * @info Useful in case you use root or another predefined user to access the
         *       tenants database.
         * @info Only creates in case tenants databases are set to be created.
         *
         * @info set to false to disable.
         */
        'auto-create-tenants-database-user' => true,

        /**
         * Automatically rename the tenants database when the random id of the
         * website changes. This should not be too common, but in case it happens
         * we automatically want to move databases accordingly.
         *
         * @info set to false to disable.
         */
        'auto-rename-tenants-database' => true,

        /**
         * Automatically deletes the tenants specific database and all data
         * contained within.
         *
         * @info set to true to enable.
         */
        'auto-delete-tenants-database' => env('AUTO_DELETE_TENANT_DATABASE', false),

        /**
         * Automatically delete the user needed to access the tenants database.
         *
         * @info Set to false to disable.
         * @info Only deletes in case tenants database is set to be deleted.
         */
        'auto-delete-tenants-database-user' => env('TENANCY_DATABASE_AUTO_DELETE_USER', false),

        /**
         * Define a list of classes that you wish to force onto the tenants or system connection.
         * The connection will be forced when the Model has booted.
         *
         * @info Useful for overriding the connection of third party packages.
         */
        'force-tenants-connection-of-models' => [
//            \App\User::class
        ],
        'force-system-connection-of-models' => [
//            \App\User::class
        ],
    ],
    'folders' => [
        'config' => [
            /**
             * Merge configuration files from the config directory
             * inside the tenants directory with the global configuration files.
             */
            'enabled' => true,

            /**
             * List of configuration files to ignore, preventing override of crucial
             * application configurations.
             */
            'blacklist' => ['database', 'tenancy', 'webserver'],
        ],
        'routes' => [
            /**
             * Allows adding and overriding URL routes inside the tenants directory.
             */
            'enabled' => true,

            /**
             * Prefix all tenants routes.
             */
            'prefix' => null,
        ],
        'trans' => [
            /**
             * Allows reading translation files from a trans directory inside
             * the tenants directory.
             */
            'enabled' => true,

            /**
             * Will override the global translations with the tenants translations.
             * This is done by overriding the laravel default translator with the new path.
             */
            'override-global' => true,

            /**
             * In case you disabled global override, specify a namespace here to load the
             * tenants translation files with.
             */
            'namespace' => 'tenants',
        ],
        'vendor' => [
            /**
             * Allows using a custom vendor (composer driven) folder inside
             * the tenants directory.
             */
            'enabled' => true,
        ],
        'media' => [
            /**
             * Mounts the assets directory with (static) files for public use.
             */
            'enabled' => true,
        ],
        'views' => [
            /**
             * Adds the vendor directory of the tenants inside the application.
             */
            'enabled' => true,

            /**
             * Specify a namespace to use with which to load the views.
             *
             * @eg setting `tenants` will allow you to use `tenants::some.blade.php`
             * @info set to null to add to the global namespace.
             */
            'namespace' => null,

            /**
             * If `namespace` is set to null (thus using the global namespace)
             * make it override the global views. Disable to
             */
            'override-global' => true,
        ]
    ]
];
