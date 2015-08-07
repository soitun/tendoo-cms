<?php
namespace App;

use System\Core\App;
use System\Http\Routing\Routes;
use System\Mvc\View\ViewRenderer;

class Modules
{
	public function __construct(App $app)
	{
		$app->getLoader()->addPsr4(__NAMESPACE__ . '\\', MODULES_PATH . __NAMESPACE__);
	}

	public function onRoute(ViewRenderer $viewRenderer)
	{
		$viewRenderer->setPath(MODULES_PATH . __NAMESPACE__ . DIRECTORY_SEPARATOR . 'views');
	}

	public function routes(Routes $routes)
	{
		$routes->any('/', 'App\Controllers\Home::index');
	}
}