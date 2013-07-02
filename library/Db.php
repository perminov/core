<?php
/**
 * This class is used to make a queries to some other databases
 */
class Db {
    public static $c;
    function connect(){
        self::$c = mysqli_connect('equatorlearning.com', 'equator_ul', 'sdifa9eskgfasdfo0qwkoegfSDFA');
        mysqli_select_db(self::$c, 'equator_live');
        self::query('SET NAMES utf8 COLLATE utf8_general_ci');
        self::query('SET CHARACTER SET utf8');
    }
    function fget($table, $field, $where, $order = null) {
        $sql = 'SELECT ' . $field . ' FROM ' . $table . ' ' . ($where ? 'WHERE ' . $where : '') . ($order ? ' ORDER BY ' . $order : '') . ' LIMIT 1';
        $mr = self::query($sql);
        if (mysqli_num_rows($mr)) {
            return current((array) mysqli_fetch_object($mr));
        } else {
            return false;
        }
    }
    function rget($table, $where, $field = '*', $order = null) {
        $sql = 'SELECT ' . $field . ' FROM ' . $table . ' ' . ($where ? 'WHERE ' . $where : '') . ($order ? ' ORDER BY ' . $order : '') . ' LIMIT 1';
        $mr = self::query($sql);
        if (mysqli_num_rows($mr)) {
            return (array) mysqli_fetch_object($mr);
        } else {
            return false;
        }
    }
    function mget($table, $where = '', $field = '*', $order = null) {
        $sql = 'SELECT ' . $field . ' FROM ' . $table . ' ' . ($where ? 'WHERE ' . $where : '') . ($order ? ' ORDER BY ' . $order : '');
        $mr = self::query($sql);
        while ($r = mysqli_fetch_object($mr)) $rs[] = (array) $r;
        return $rs;
    }
    function fset($table, $field, $value, $where) {
        $sql = 'UPDATE ' . $table . ' SET ' . $field . ' = "' . $value . '"' . ($where ? ' WHERE ' . $where : '');
        return self::query($sql);
    }
    function rset($table, $data, $where) {
        $sql = 'UPDATE ' . $table . ' SET ';
        foreach($data as $field => $value) {
            $set[] = '`' . $field . '` = "' . str_replace('"', '&quot;', $value) . '"';
        }
        $sql .= implode(', ', $set);
        $sql .= ($where ? ' WHERE ' . $where : '');
        return self::query($sql);
    }
    function save($table, $data, $where = null) {
        if ($where) return mset($table, $data, $where); else {
            $sql = 'INSERT INTO ' . $table . ' SET ';
            foreach($data as $field => $value) {
                $set[] = '`' . $field . '` = "' . str_replace('"', '&quot;', $value) . '"';
            }
            $sql .= implode(', ', $set);
            return self::query($sql);
        }
    }
    function error($sql) {
        d(mysqli_error(self::$c));
        d($sql);
    }
    function query($sql){
        if (! self::$c) self::connect();
        $r = mysqli_query(self::$c, $sql);
        if(mysqli_error(self::$c)) {
            self::error($sql);
            die();
        } else {
            if (preg_match('/^INSERT/', $sql)) {
                return mysqli_insert_id(self::$c);
            } else if (preg_match('/^UPDATE|DELETE/', $sql)) {
                return mysqli_affected_rows(self::$c);
            } else {
                return $r;
            }
        }
    }
}