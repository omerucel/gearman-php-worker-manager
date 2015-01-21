<?php

namespace OU\Gearman;

use OU\Gearman\Config\Config;
use Psr\Log\LoggerInterface;

class WorkerManager
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \GearmanClient
     */
    protected $gearmanClient;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Config $config
     * @param \GearmanClient $gearmanClient
     * @param LoggerInterface $logger
     */
    public function __construct(Config $config, \GearmanClient $gearmanClient, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->gearmanClient = $gearmanClient;
        $this->logger = $logger;
    }

    public function run()
    {
        if (!$this->config->getStatus()) {
            $this->logger->emergency('Worker manager status is disabled!');
            return;
        }

        $this->syncWorkers();
    }

    protected function syncWorkers()
    {
        $this->killWorkersForTimeout();
        $this->killWorkersForWorkerCount();
        $this->startNewWorkers();
    }

    protected function killWorkersForTimeout()
    {
        foreach ($this->getCurrentProcesses() as $pid => $minute) {
            if ($minute > $this->config->getWorkerMaxLife()) {
                $this->logger->info('Gearman worker (' . $pid . ') will terminate because of timeout.');
                $this->terminateWorker($pid);
            }
        }
    }

    protected function killWorkersForWorkerCount()
    {
        $currentProcesses = $this->getCurrentProcesses();
        $currentProcessCount = count($currentProcesses);
        if ($currentProcessCount > $this->config->getWorkerCount()) {
            $different = $currentProcessCount - $this->config->getWorkerCount();
            $counter = 0;
            arsort($currentProcesses);
            foreach ($currentProcesses as $pid => $minute) {
                $counter++;
                $this->logger->info('Gearman worker (' . $pid . ') will terminate because of worker count setting.');
                $this->terminateWorker($pid);
                if ($counter == $different) {
                    break;
                }
            }

        }
    }

    protected function startNewWorkers()
    {
        $currentProcesses = $this->getCurrentProcesses();
        $currentProcessCount = count($currentProcesses);
        if ($currentProcessCount < $this->config->getWorkerCount()) {
            $different = $this->config->getWorkerCount() - $currentProcessCount;
            while ($different > 0) {
                $different--;
                $cmd = $this->config->getPHPBin() . ' ' . $this->config->getWorkerScriptPath()
                    . ' -e ' . $this->config->getEnvironment();
                exec($cmd . ' > /dev/null &');
            }
        }
    }

    protected function terminateWorker($pid)
    {
        $this->gearmanClient->doHigh('terminate_' . $this->config->getIp() . '_' . $pid, '1');
    }

    /**
     * $processes[[pid : executionMinute], [pid : executionMinute]];
     * @return array
     */
    protected function getCurrentProcesses()
    {
        $baseName = basename($this->config->getWorkerScriptPath());
        $command = 'ps -eo pid,etime,cmd '
            . '| grep "' . $baseName . '" '
            . '| grep "grep" -v '
            . '| awk "{print \$1\";\"\$2}"';
        exec($command, $processes);
        $temp = [];
        foreach ($processes as $process) {
            $process = explode(';', $process);
            $parsedDate = explode('-', $process[1]);
            $day = 0;
            if (count($parsedDate) > 1) {
                $day = $parsedDate[0];
                $time = $parsedDate[1];
            } else {
                $time = $parsedDate[0];
            }
            $time = explode(':', $time);
            $minute = ($time[0] + ($day * 24)) * 60;
            $minute+= $time[1];
            $temp[$process[0]] = $minute;
        }
        return $temp;
    }
}
