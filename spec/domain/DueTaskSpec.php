<?php
namespace vendor\project\domain;

use rtens\udity\check\DomainSpecification;
use rtens\udity\utils\Time;

class DueTaskSpec extends DomainSpecification {

    function undueTask() {
        $this->given(Task::class)->created('Foo');
        $this->whenProjectObject(Task::class);
        $this->assert()->equals($this->projection(Task::class)->isOverdue(), false);
    }

    function dueTask() {
        $this->given(Task::class)->created('Foo', Time::now());
        $this->whenProjectObject(Task::class);
        $this->assert()->equals($this->projection(Task::class)->isOverdue(), false);
    }

    function overdueTask() {
        $this->given(Task::class)->created('Foo', Time::at('10 minutes ago'));
        $this->whenProjectObject(Task::class);
        $this->assert()->equals($this->projection(Task::class)->isOverdue(), true);
    }
}