<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Long ID Generator
 * 
 * 生成长id
 * 
 * @author 胥国玉 2015-06-17
 * 
 * 特点：
 * 参照Snowflake service from Twitter。
 * id粗略有序。
 * 可反解。
 * 时间相关。
 * 可制造。
 * 不能保证绝对唯一，但重复的几率微乎其微，在可容忍范围内。
 * 多机器部署灵活、方便。
 * 
 * 四部分组成：
 * 最高位是符号位，始终为0
 * 41位的时间序列(精确到毫秒，41位的长度可以使用69年)
 * 10位的机器标识(包括数据中心5位 + 机器编号5位)（10位的长度最多支持部署1024个节点)
 * 12位的毫秒内随机数（理论上12位支持每个节点每毫秒产生4096个ID序号)
 * 
 * 缺点：
 * 毫秒内产生相同随机数会造成重复，虽然几率很小！因为id一般作为主键，写DB时如果有重复会写入失败，
 * 建议因为id重复写表失败的，重新生成新id重试写表一次（为了防止死循环，建议只重试一次，重试失败即抛错)。
 */
class Extension_Longid
{
    // ID开始时间(毫秒) 2015-06-15 00:00:00
    const INIT_TIME = 1434297600000;
    
    //const time_bits       = 41;
    const datacenter_bits = 5;
    const machine_bits    = 5;
    const sequence_bits   = 12;

    /**
     * 生成ID
     * @author 胥国玉 2015-06-17
     * 
     * @param  int $machine 机器编号
     * @param  int $datacenter IDC编号
     * @return int
     */
    public static function generate($machine, $datacenter = 0) {
        if ($machine - intval($machine) != 0 || $datacenter - intval($datacenter) != 0) {
            return false;
        }
        
        $datacenter_max = -1 ^ (-1 << self::datacenter_bits);
        $machine_max    = -1 ^ (-1 << self::machine_bits);
        if ($machine < 0 || $machine > $machine_max
            || $datacenter < 0 || $datacenter > $datacenter_max) {
            return false;
        }
        
        $time_shift       = self::sequence_bits + self::machine_bits + self::datacenter_bits;
        $datacenter_shift = self::sequence_bits + self::machine_bits;
        $machine_shift    = self::sequence_bits;
        $sequence_max     = -1 ^ (-1 << self::sequence_bits);
        
        $time = floor(microtime(true) * 1000);
        
        $long_id = (($time - self::INIT_TIME) << $time_shift)
                    | ($datacenter << $datacenter_shift)
                    | ($machine << $machine_shift)
                    | mt_rand(0, $sequence_max);
        
        return $long_id;
    }
    
    /**
     * 根据ID，反解获得时间
     * @author 胥国玉 2015-06-17
     * 
     * @param  int $id
     * @return int
     */
    public static function get_time($id) {
        return ($id >> self::sequence_bits + self::machine_bits + self::datacenter_bits) + self::INIT_TIME;
    }
    
    /**
     * 根据ID，反解获得IDC编号
     * @author 胥国玉 2015-06-17
     * 
     * @param  int $id
     * @return int
     */
    public static function get_datacenter($id) {
        $shift = self::sequence_bits + self::machine_bits + self::datacenter_bits;
        return (($id >> $shift << $shift ^ $id) >> self::sequence_bits + self::machine_bits);
    }
    
    /**
     * 根据ID，反解获得机器编号
     * @author 胥国玉 2015-06-17
     * 
     * @param  int $id
     * @return int
     */
    public static function get_machine($id) {
        $shift = self::sequence_bits + self::machine_bits;
        return (($id >> $shift << $shift ^ $id) >> self::sequence_bits);
    }

}

