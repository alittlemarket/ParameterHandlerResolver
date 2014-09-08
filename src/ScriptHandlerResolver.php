<?php
/**
 *  2014 - Incubart
 *
 * @license http://www.spdx.org/licenses/MIT MIT
 */
namespace ALittle\ParameterHandlerResolver;

use Composer\Script\Event;

/**
 * This class intends to delegates the building of parameters files
 * to the csa/composer-parameter-handler or the incenteev/composer-parameter-handler
 * depending on the composer dev flag.
 *
 * @author Sylvain Mauduit <sylvain@alittlemarket.fr>
 */
class ScriptHandlerResolver
{
    /**
     * Build the parameters files.
     *
     * If the composer dev flag is on, the incenteev/composer-parameter-handler is
     * used to build parameters, because it can be entirely non-interactive (use the parameters.yml.dist values)
     * or be interactive and ask the developer for the value to use if the parameter is missing.
     *
     * If the dev flag is off (i.e. for deployment purpose), the csa/composer-parameter-handler fork is used
     * in order to force the deployment to fail if an argument is missing.
     *
     * @param Event $event
     *
     * @throws \InvalidArgumentException
     */
    public static function buildParameters(Event $event)
    {
        $extras = $event->getComposer()->getPackage()->getExtra();

        if (!isset($extras['alittle-parameters'])) {
            throw new \InvalidArgumentException('The parameter handler needs to be configured through the extra.alittle-parameters setting.');
        }

        $envConfigs = $extras['alittle-parameters'];

        if ($event->isDevMode()) {
            $processor = new \Incenteev\ParameterHandler\Processor($event->getIO());
            $configKey = 'dev';
        } else {
            $processor = new \Csa\ParameterHandler\Processor($event->getIO());
            $configKey = 'no-dev';
        }

        if (!is_array($envConfigs)) {
            throw new \InvalidArgumentException('The extra.alittle-parameters setting must be an array or a configuration object.');
        }

        if (!array_key_exists($configKey, $envConfigs)) {
            throw new \InvalidArgumentException('The extra.alittle-parameters.'.$configKey.' setting must be defined.');
        }

        $envConfig = $envConfigs[$configKey];

        if (!is_array($envConfig)) {
            throw new \InvalidArgumentException('The extra.alittle-parameters.'.$configKey.' setting must be an array or a configuration object.');
        }

        if (array_keys($envConfig) !== range(0, count($envConfig) - 1)) {
            $envConfig = array($envConfig);
        }

        foreach ($envConfig as $config) {

            if (!is_array($config)) {
                throw new \InvalidArgumentException('The extra.alittle-parameters setting must be an array of configuration objects.');
            }

            $processor->processFile($config);
        }
    }
}
