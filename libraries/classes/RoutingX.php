<?php
echo 'TOP OF RoutingX.php<br>';
// Temporary RoutingX.php for stepwise debugging

declare(strict_types=1);

namespace PhpMyAdmin;

// Comment out all use statements for now
use FastRoute\DataGenerator\GroupCountBased as DataGeneratorGroupCountBased;
use FastRoute\Dispatcher;
use FastRoute\Dispatcher\GroupCountBased as DispatcherGroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std as RouteParserStd;
use PhpMyAdmin\Http\ServerRequest;
use Psr\Container\ContainerInterface;

use function __;
use function file_exists;
use function file_put_contents;
use function htmlspecialchars;
use function is_readable;
use function is_string;
use function is_writable;
use function rawurldecode;
use function sprintf;
use function trigger_error;
use function var_export;

use const CACHE_DIR;
use const E_USER_WARNING;
use const ROOT_PATH;

class RoutingX
{
    public function __construct() {
        echo 'RoutingX loaded<br>';
    }

    public static function skipCache(): bool
    {
        global $cfg;

        return ($cfg['environment'] ?? '') === 'development';
    }
}
