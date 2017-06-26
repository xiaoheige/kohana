<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Short ID Generator
 * 
 * 生成短id
 * 
 * @author 胥国玉 2015-06-16
 * 
 * 特点：
 * id存储长度不超过32bit，以无符号int类型存储。
 * id表现长度为8~10位。
 * 支持扩展到8台机器。
 * id自增，步长随机。
 * 单机有序。
 * 可反解。
 * 时间不相关。
 * 不可制造。
 * 绝对唯一。
 * 多机器部署，需要多个数据库表支持，一机一表，一一对应。
 * 
 * 三部分组成：
 * 1. msyql自增id：占19~27bit，单机支持(133,890,046 = 134217727 - 327681)个id
 * 2. 随机数：占2bit，相邻两个id的自增步长随机
 * 3. 机器编号：占3bit，支持扩展到8台机器
 * 
 * 单机取值范围：
 * 最小值：1010000000000000001-00-001          (10485793)
 * 最大值：111111111111111111111111111-00-001  (4294967265)
 * 说明：1010000000000000001为最小值，是为了保证10进制id最短8位
 * 
 * id自增表：
 * CREATE TABLE generate_id_x (
 *     id int(10) unsigned NOT NULL auto_increment,
 *     stub char(1) NOT NULL default '',
 *     PRIMARY KEY  (id),
 *     UNIQUE KEY stub (stub)
 * ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 * 
 * SQL：
 * REPLACE INTO generate_id_x (stub) VALUES ('a');
 * SELECT LAST_INSERT_ID();
 * 
 * 扩展性：
 * 假如id超过上限，只需修改id存储字段的数据类型为long型。
 */
class Extension_Shortid
{
    // ID基数
    const UID_BASE_NUM = 327680;
    
    //const mysqlid_bits = 27;
    const random_bits  = 2;
    const machine_bits = 3;
    
    /**
     * 生成ID
     * @author 胥国玉 2015-06-16
     * 
     * @param  int $machine 机器编号 （0-7）
     * @return int
     */
    public static function generate($machine = 0) {
        if ($machine - intval($machine) != 0) {
            return false;
        }
        
        $machine_max = -1 ^ (-1 << self::machine_bits);
        if ($machine < 0 || $machine > $machine_max) {
            return false;
        }
        
        $table = 'generate_id_' . $machine;
        $sql = 'replace into ' . $table . " (stub) values ('a')";
        $query = DB::query(Database::INSERT, $sql);
        list($insert_id, $affect) = $query -> execute();
        if (! is_numeric($insert_id) || $insert_id < 1) {
            return false;
        }
        
        $mysqlid_shift = self::random_bits + self::machine_bits;
        $random_shift  = self::machine_bits;
        $random_max    = -1 ^ (-1 << self::random_bits);
        
        $id = (self::UID_BASE_NUM + $insert_id) << $mysqlid_shift
                | mt_rand(0, $random_max) << $random_shift
                | $machine;
        
        return $id;
    }
    
    /**
     * 根据ID，反解获得数据库自增ID
     * @author 胥国玉 2015-06-17
     * 
     * @param  int $id
     * @return int
     */
    public static function get_mysqlid($id) {
        return ($id >> self::random_bits + self::machine_bits) - self::UID_BASE_NUM;
    }

    /**
     * 根据ID，反解获得机器编号
     * @author 胥国玉 2015-06-17
     *
     * @param  int $id
     * @return int
     */
    public static function get_machine($id) {
        return $id >> self::machine_bits << self::machine_bits ^ $id;
    }
    
}

