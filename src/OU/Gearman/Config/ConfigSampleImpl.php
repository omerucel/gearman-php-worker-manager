<?php

namespace OU\Gearman\Config;

class ConfigSampleImpl implements Config
{
    /**
     * @var array
     */
    protected $status = 1;
    protected $ip = '0.0.0.0';
    protected $workerMaxLife = 2880;
    protected $workerCount = 2;
    protected $phpBin = '/usr/bin/php';
    protected $environment = 'development';
    protected $workerScriptPath;

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    public function setWorkerMaxLife($workerMaxLife)
    {
        $this->workerMaxLife = $workerMaxLife;
    }

    public function setWorkerCount($workerCount)
    {
        $this->workerCount = $workerCount;
    }

    public function setPhpBin($phpBin)
    {
        $this->phpBin = $phpBin;
    }

    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    public function setWorkerScriptPath($workerScriptPath)
    {
        $this->workerScriptPath = $workerScriptPath;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function getWorkerMaxLife()
    {
        return $this->workerMaxLife;
    }

    public function getWorkerCount()
    {
        return $this->workerCount;
    }

    public function getPHPBin()
    {
        return $this->phpBin;
    }

    public function getEnvironment()
    {
        return $this->environment;
    }

    public function getWorkerScriptPath()
    {
        return $this->workerScriptPath;
    }
}
