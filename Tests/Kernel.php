<?php

namespace Liip\OneallBundle\Tests;

use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\ClassLoader\UniversalClassLoader;

class Kernel extends BaseKernel
{
    public function __construct()
    {
        $this->tmpDir = sys_get_temp_dir().'/sf2_'.rand(1, 9999);
        if (!is_dir($this->tmpDir)) {
            if (false === @mkdir($this->tmpDir)) {
                die(sprintf('Unable to create a temporary directory (%s)', $this->tmpDir));
            }
        } elseif (!is_writable($this->tmpDir)) {
            die(sprintf('Unable to write in a temporary directory (%s)', $this->tmpDir));
        }

        parent::__construct('env', true);

        require_once __DIR__.'/OneallApiException.php';

        $loader = new UniversalClassLoader();
        $loader->loadClass('\OneallApiException');
        $loader->register();
    }

    public function __destruct()
    {
        $fs = new Filesystem();
        $fs->remove($this->tmpDir);
    }

    public function registerRootDir()
    {
        return $this->tmpDir;
    }

    public function registerBundles()
    {
        return array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
        );
    }

    public function registerBundleDirs()
    {
        return array(
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function ($container) {
            $container->setParameter('kernel.compiled_classes', array());
        });
    }
}
