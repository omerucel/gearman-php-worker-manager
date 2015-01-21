<?php

namespace OU\Gearman;

interface Config
{
    public function getStatus();
    public function getIp();
    public function getWorkerMaxLife();
    public function getWorkerCount();
    public function getPHPBin();
    public function getEnvironment();
    public function getWorkerScriptPath();
}
