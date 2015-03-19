<?php

Class Hash
{
    /**
     * 生成唯一的订单HASH值
     *
     * @param int $locationHash 5位景区编码
     * @param int $orderId 订单ID
     * @return array
     */
    public static function genHash($locationHash, $source)
    {
        return str_pad($locationHash, 5, '0', STR_PAD_LEFT) . $source . self::rand(10);
    }

    /**
     * 获取随机数
     * @param  int $length 长度
     * @return string
     */
    public static function rand($length)
    {
        $randStr = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $randStr{mt_rand(0, 35)};
        }
        return $result;
    }

    /**
     * 返回加密的密码，不可逆的加密方式
     *
     * @param string $password 密码
     * @return string
     **/
    public static function getHashedPassword($password)
    {
        return Password::password_hash($password, PASSWORD_BCRYPT, array('cost'=>8));
    }
}