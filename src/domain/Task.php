<?php
namespace vendor\project\domain;

use rtens\udity\domain\objects\DomainObject;
use rtens\udity\utils\Time;

class Task extends DomainObject {

    private $name;
    private $due;
    private $completed = false;

    public function created($name, \DateTimeImmutable $due = null) {
        $this->name = $name;
        $this->due = $due;
    }

    public function getName() {
        return $this->name;
    }

    public function getDue() {
        return $this->due;
    }

    public function isOverdue() {
        return $this->due && $this->due < Time::now();
    }

    public function doComplete() {
        if ($this->completed) {
            throw new \Exception('Task was already completed');
        }
    }

    public function didComplete() {
        $this->completed = true;
    }

    public function isCompleted() {
        return $this->completed;
    }
}