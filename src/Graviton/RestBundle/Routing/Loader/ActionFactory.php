<?php
namespace Graviton\RestBundle\Routing\Loader;

use Symfony\Component\Routing\Route;

/**
 * Generate routes for individual actions
 *
 * @category GravitonRestBundle
 * @package  Graviton
 * @author   Lucas Bickel <lucas.bickel@swisscom.com>
 * @author   Manuel Kipfer <manuel.kipfer@swisscom.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.com
 */
class ActionFactory
{
    /**
     * Get route for GET requests
     *
     * @param String $service service id
     *
     * @return Route
     */
    public static function getRouteGet($service)
    {
        return self::getRoute($service, 'GET', 'getAction', array('id' => '\w+'));
    }

    /**
     * Get route for getAll requests
     *
     * @param String $service service id
     *
     * @return Route
     */
    public static function getRouteAll($service)
    {
        return self::getRoute($service, 'GET', 'allAction');
    }

    /**
     * Get route for POST requests
     *
     * @param String $service service id
     *
     * @return Route
     */
    public static function getRoutePost($service)
    {
        return self::getRoute($service, 'POST', 'postAction');
    }

    /**
     * Get route for PUT requests
     *
     * @param String $service service id
     *
     * @return Route
     */
    public static function getRoutePut($service)
    {
        return self::getRoute($service, 'PUT', 'putAction', array('id' => '\w+'));
    }

    /**
     * Get route for DELETE requests
     *
     * @param String $service service id
     *
     * @return Route
     */
    public static function getRouteDelete($service)
    {
        return self::getRoute($service, 'DELETE', 'deleteAction', array('id' => '\w+'));
    }

    /**
     * Get entity name from service strings.
     *
     * By convention the last part of the service string so far
     * makes up the entities name.
     *
     * @param String $service (partial) service id
     *
     * @return String
     */
    private static function getBaseFromService($service)
    {
        $parts = explode('.', $service);

        $entity = array_pop($parts);
        $module = $parts[1];

        return '/'.$module.'/'.$entity;
    }

    /**
     * Get Route
     *
     * @param String $service    name of service containing controller
     * @param String $method     HTTP method to generate route for
     * @param String $action     action to call for route
     * @param Array  $parameters route parameters to append to route as pair of name and patterns
     *
     * @return Route
     */
    private static function getRoute($service, $method, $action, $parameters = array())
    {
        $pattern = '/'.static::getBaseFromService($service);
        $defaults = array(
            '_controller' => $service.':'.$action,
            '_format' => '~',
        );

        $requirements = array(
            '_method' => $method,
        );

        foreach ($parameters as $paramName => $paramPattern) {
            $pattern .= '/{'.$paramName.'}';
            $requirements[$paramName] = $paramPattern;
        }

        $route = new Route($pattern, $defaults, $requirements);

        return $route;
    }
}