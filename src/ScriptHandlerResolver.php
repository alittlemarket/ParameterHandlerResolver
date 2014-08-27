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
     */
    public static function buildParameters(Event $event)
    {
        if ($event->isDevMode()) {
            \Incenteev\ParameterHandler\ScriptHandler::buildParameters($event);
        } else {
            \Csa\ParameterHandler\ScriptHandler::buildParameters($event);
        }
    }
}
