```php
$config = new \OU\Gearman\Config\ConfigSampleImpl();
$config->setWorkerScriptPath('/data/www/project/services/worker.php');

$manager = new \OU\Gearman\WorkerManager($config, $gearmanClient, $logger);
$manager->run();
```