<?php

namespace Platon\Facades;

use Platon\Wordpress\AdvancedCustomFields\AcfConfigurator;

/**
 * @method static AcfConfigurator group(string $classname)
 * @method static AcfConfigurator groups(array $classnames)
 * @method static AcfConfigurator discoverGroups(string $namespace)
 */
class Acf extends Facade
{
    public static function getFacadeAccessor()
    {
        return AcfConfigurator::class;
    }
}