<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInita7bd3edbf5f0d151bf940c04d26b6bb8
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInita7bd3edbf5f0d151bf940c04d26b6bb8', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInita7bd3edbf5f0d151bf940c04d26b6bb8', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInita7bd3edbf5f0d151bf940c04d26b6bb8::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
