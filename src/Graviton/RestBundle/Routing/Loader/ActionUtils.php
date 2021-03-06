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
class ActionUtils
{
    const ID_PATTERN = '[a-zA-Z0-9\-_\/]+';

    /**
     * Get route for GET requests
     *
     * @param string $service       service id
     * @param array  $serviceConfig service configuration
     *
     * @return Route
     */
    public static function getRouteGet($service, $serviceConfig)
    {
        return self::getRoute($service, 'GET', 'getAction', $serviceConfig, array('id' => self::ID_PATTERN));
    }

    /**
     * Get Route
     *
     * @param string $service       name of service containing controller
     * @param string $method        HTTP method to generate route for
     * @param string $action        action to call for route
     * @param array  $serviceConfig service configuration
     * @param array  $parameters    route parameters to append to route as pair of name and patterns
     *
     * @return Route
     */
    private static function getRoute($service, $method, $action, $serviceConfig, $parameters = array())
    {
        $pattern = self::getBaseFromService($service, $serviceConfig);
        $defaults = array(
            '_controller' => $service . ':' . $action,
            '_format' => '~',
        );

        $requirements = array(
            '_method' => $method,
        );

        foreach ($parameters as $paramName => $paramPattern) {
            $pattern .= '/{' . $paramName . '}';
            $requirements[$paramName] = $paramPattern;
        }

        $route = new Route($pattern, $defaults, $requirements);

        return $route;
    }

    /**
     * Get entity name from service strings.
     *
     * By convention the last part of the service string so far
     * makes up the entities name.
     *
     * @param string $service       (partial) service id
     * @param array  $serviceConfig service configuration
     *
     * @return string
     */
    private static function getBaseFromService($service, $serviceConfig)
    {
        if (isset($serviceConfig[0]['router-base']) && strlen($serviceConfig[0]['router-base']) > 0) {
            $base = $serviceConfig[0]['router-base'];
        } else {
            $parts = explode('.', $service);

            $entity = array_pop($parts);
            $module = $parts[1];

            $base = '/' . $module . '/' . $entity;
        }

        return $base;
    }

    /**
     * Get route for getAll requests
     *
     * @param string $service       service id
     * @param array  $serviceConfig service configuration
     *
     * @return Route
     */
    public static function getRouteAll($service, $serviceConfig)
    {
        return self::getRoute($service, 'GET', 'allAction', $serviceConfig);
    }

    /**
     * Get route for POST requests
     *
     * @param string $service       service id
     * @param array  $serviceConfig service configuration
     *
     * @return Route
     */
    public static function getRoutePost($service, $serviceConfig)
    {
        return self::getRoute($service, 'POST', 'postAction', $serviceConfig);
    }

    /**
     * Get route for PUT requests
     *
     * @param string $service       service id
     * @param array  $serviceConfig service configuration
     *
     * @return Route
     */
    public static function getRoutePut($service, $serviceConfig)
    {
        return self::getRoute($service, 'PUT', 'putAction', $serviceConfig, array('id' => self::ID_PATTERN));
    }

    /**
     * Get route for PATCH request
     *
     * @param string $service       service id
     * @param array  $serviceConfig service configuration
     *
     * @return Route
     */
    public static function getRoutePatch($service, $serviceConfig)
    {
        return self::getRoute($service, 'PATCH', 'patchAction', $serviceConfig, array('id' => self::ID_PATTERN));
    }

    /**
     * Get route for DELETE requests
     *
     * @param string $service       service id
     * @param array  $serviceConfig service configuration
     *
     * @return Route
     */
    public static function getRouteDelete($service, $serviceConfig)
    {
        return self::getRoute($service, 'DELETE', 'deleteAction', $serviceConfig, array('id' => self::ID_PATTERN));
    }

    /**
     * Get route for OPTIONS requests
     *
     * @param string $service       service id
     * @param array  $serviceConfig service configuration
     * @param array  $parameters    service params
     *
     * @return Route
     */
    public static function getRouteOptions($service, $serviceConfig, array $parameters = array())
    {
        return self::getRoute($service, 'OPTIONS', 'optionsAction', $serviceConfig, $parameters);
    }

    /**
     * Get canonical route for schema requests
     *
     * @param string $service       service id
     * @param array  $serviceConfig service configuration
     * @param string $type          service type (item or collection)
     *
     * @return Route
     */
    public static function getCanonicalSchemaRoute($service, $serviceConfig, $type = 'item')
    {
        $pattern = self::getBaseFromService($service, $serviceConfig);
        $pattern = '/schema' . $pattern . '/' . $type;

        $defaults = array(
            '_controller' => $service . ':optionsAction',
            '_format' => '~',
        );

        $requirements = array(
            '_method' => 'GET',
        );

        $route = new Route($pattern, $defaults, $requirements);

        return $route;
    }
}
