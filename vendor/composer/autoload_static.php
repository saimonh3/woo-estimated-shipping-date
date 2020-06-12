<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitefef112eb67ee397cb858c0d1c39fe5d
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Saimon\\WCESD\\' => 13,
        ),
        'A' => 
        array (
            'Appsero\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Saimon\\WCESD\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Appsero\\' => 
        array (
            0 => __DIR__ . '/..' . '/appsero/client/src',
        ),
    );

    public static $classMap = array (
        'Appsero\\Client' => __DIR__ . '/..' . '/appsero/client/src/Client.php',
        'Appsero\\Insights' => __DIR__ . '/..' . '/appsero/client/src/Insights.php',
        'Appsero\\License' => __DIR__ . '/..' . '/appsero/client/src/License.php',
        'Appsero\\Updater' => __DIR__ . '/..' . '/appsero/client/src/Updater.php',
        'Saimon\\WCESD\\Base' => __DIR__ . '/../..' . '/src/Base.php',
        'Saimon\\WCESD\\Constants' => __DIR__ . '/../..' . '/src/Constants.php',
        'Saimon\\WCESD\\Engine' => __DIR__ . '/../..' . '/src/Engine.php',
        'Saimon\\WCESD\\Helper' => __DIR__ . '/../..' . '/src/Helper.php',
        'Saimon\\WCESD\\Product_Settings' => __DIR__ . '/../..' . '/src/Product_Settings.php',
        'Saimon\\WCESD\\Settings' => __DIR__ . '/../..' . '/src/Settings.php',
        'Saimon\\WCESD\\Views' => __DIR__ . '/../..' . '/src/Views.php',
        'Saimon\\WCESD\\Views\\Cart' => __DIR__ . '/../..' . '/src/Views/Cart.php',
        'Saimon\\WCESD\\Views\\Single_Product' => __DIR__ . '/../..' . '/src/Views/Single_Product.php',
        'Saimon\\WCESD\\Views\\Thankyou' => __DIR__ . '/../..' . '/src/Views/Thankyou.php',
        'WeDevs_Settings_API' => __DIR__ . '/..' . '/tareq1988/wordpress-settings-api-class/src/class.settings-api.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitefef112eb67ee397cb858c0d1c39fe5d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitefef112eb67ee397cb858c0d1c39fe5d::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitefef112eb67ee397cb858c0d1c39fe5d::$classMap;

        }, null, ClassLoader::class);
    }
}