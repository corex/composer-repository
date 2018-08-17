<?php

namespace CoRex\Composer\Repository\Services;

use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Package\PackageInterface;
use Composer\Repository\CompositeRepository;
use Composer\Repository\VcsRepository;

class PackagistService
{
    /**
     * Get vendor directory.
     *
     * @return string
     */
    public static function getVendorDirectory()
    {
        $composer = Factory::create(new NullIO(), array());
        return $composer->getConfig()->get('vendor-dir');
    }

    /**
     * Get packagist package.
     *
     * @param string $signature
     * @param string $constraint
     * @return PackageInterface
     */
    public static function getPackagistPackage($signature, $constraint = '*')
    {
        $composer = Factory::create(new NullIO(), array());
        $repositories = new CompositeRepository($composer->getRepositoryManager()->getRepositories());
        return $repositories->findPackage($signature, $constraint);
    }

    /**
     * Packagist has package.
     *
     * @param string $signature
     * @param string $constraint
     * @return boolean
     */
    public static function packagistHasPackage($signature, $constraint = '*')
    {
        return self::getPackagistPackage($signature, $constraint) !== null;
    }

    /**
     * Get repository information by url.
     *
     * @param string $repositoryUrl
     * @return array|boolean
     */
    public static function getRepositoryInformationByUrl($repositoryUrl)
    {
        $io = new NullIO();
        $config = Factory::createConfig();
        $io->loadConfiguration($config);
        $repository = new VcsRepository(['url' => $repositoryUrl], $io, $config);

        if (!($driver = $repository->getDriver())) {
            return false;
        }

        $information = $driver->getComposerInformation($driver->getRootIdentifier());

        return $information;
    }
}