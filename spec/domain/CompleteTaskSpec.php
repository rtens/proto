<?php
namespace vendor\project\domain;

use rtens\udity\check\DomainSpecification;

class CompleteTaskSpec extends DomainSpecification {

    function incompleteTask() {
        $this->given(Task::class, 'foo')->created('Foo');
        $this->whenProjectObject(Task::class, 'foo');
        $this->assert()->equals($this->projection(Task::class)->isCompleted(), false);
    }

    function completeTask() {
        $this->given(Task::class, 'foo')->created('Foo');
        $this->given(Task::class, 'foo')->didComplete();
        $this->whenProjectObject(Task::class, 'foo');
        $this->assert()->equals($this->projection(Task::class)->isCompleted(), true);
    }

    function cannotCompleteCompletedTask() {
        $this->given(Task::class, 'foo')->created('Foo');
        $this->given(Task::class, 'foo')->didComplete();
        $this->tryTo(Task::class, 'foo')->doComplete();
        $this->thenShouldFailWith('Task was already completed');
    }
}