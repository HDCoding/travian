<?php

namespace Travian\Automations;

class Mutex
{
    public $lock_name;
    public $writeable_path;
    public $file_handle = null;

    public $log_path = LOG_PATH;

    public function __construct($lock_name, $writeable_path = null)
    {
        $this->lock_name = $lock_name . '.lock';

        if ($writeable_path == null) {
            $this->writeable_path = $this->log_path .  'lock';
        }

        if (!is_dir($this->writeable_path)) {
            mkdir($this->writeable_path);
        }

        file_fix_write($this->writeable_path);

        return $this;
    }

    public function controlLock($time = 0)
    {
        if($this->isLocked() || $this->getLastModified() < $time) {
            return false;
        }
        return $this->getLock();
    }

    public function isLocked()
    {
        $file_handle = fopen($this->getLockFilePath(), 'c');

        $can_lock = flock($file_handle, LOCK_EX);

        if ($can_lock) {
            flock($file_handle, LOCK_UN);
            fclose($file_handle);
            return false;
        } else {
            fclose($file_handle);
            return true;
        }
    }

    public function getLockFilePath()
    {
        return $this->writeable_path . $this->lock_name;
    }

    public function getLastModified()
    {
        return time() - filemtime($this->getLockFilePath());
    }

    public function getLock()
    {
        return flock($this->getFileHandle(), LOCK_EX);
    }

    public function getFileHandle()
    {
        if (file_exists($this->getLockFilePath())) {
            file_fix_write($this->getLockFilePath());
        }

        if ($this->file_handle == null) {
            $this->file_handle = fopen($this->getLockFilePath(), 'c');
        }

        return $this->file_handle;
    }

    public function releaseLock()
    {
        $success = flock($this->getFileHandle(), LOCK_UN);
        fclose($this->getFileHandle());
        touch($this->getLockFilePath());
        return $success;
    }

    public function findWriteablePath()
    {
        $filename = tempnam('/tmp', 'LOCK');
        $path = dirname($filename);
        $file_handle = fopen($filename, 'c');

        if (flock($file_handle, LOCK_EX)) {
            flock($file_handle, LOCK_UN);
            fclose($file_handle);
            $this->writeable_path = $path;
        } else {
            exit('Lock Failed');
        }
    }
}