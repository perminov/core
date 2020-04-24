<?php
interface Indi_Queue_Interface {
    public function chunk(array $params);
    public function count($queueTaskId);
    public function items($queueTaskId);
    public function queue($queueTaskId);
    public function apply($queueTaskId);
}