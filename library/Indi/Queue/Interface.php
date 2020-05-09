<?php
interface Indi_Queue_Interface {
    public function chunk($params);
    public function count($queueTaskId);
    public function items($queueTaskId);
    public function queue($queueTaskId);
    public function apply($queueTaskId);
}