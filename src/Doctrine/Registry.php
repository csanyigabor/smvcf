<?php

namespace WND\SMVCF\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

/**
 * Class Registry
 */
class Registry
{
    /**
     * @type string
     */
    protected $mapping;

    /**
     * @type string
     */
    protected $cacheDir;

    /**
     * @type array
     */
    protected $connConfig;

    /**
     * @type \Doctrine\ORM\EntityManager
     */
    protected $currentManager;

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getManager()
    {
        if ($this->currentManager === null || !$this->currentManager->isOpen()) {
            $mapping = Setup::createYAMLMetadataConfiguration(
                [$this->mapping],
                true,
                $this->cacheDir
            );
            $this->currentManager = EntityManager::create(
                $this->connConfig,
                $mapping
            );
        }

        return $this->currentManager;
    }

    /**
     * @param array $config
     */
    public function setConfiguration(array $config)
    {
        $this->mapping = $config['mapping'];
        $this->cacheDir = $config['cache_dir'];
        $this->connConfig = $config['config'];
    }
}
