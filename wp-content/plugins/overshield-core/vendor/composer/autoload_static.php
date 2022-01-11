<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbb9c74a036baff166ac7826729040f6b
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'FrontEnd\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'FrontEnd\\' => 
        array (
            0 => __DIR__ . '/../..' . '/public',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitbb9c74a036baff166ac7826729040f6b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitbb9c74a036baff166ac7826729040f6b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitbb9c74a036baff166ac7826729040f6b::$classMap;

        }, null, ClassLoader::class);
    }
}