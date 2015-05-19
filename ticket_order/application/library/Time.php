<?php
class Time{
    /**
     * 生成年份
     *
     * @return array
     */
    public static function dateYears() {
        $tab = array();

        for ($i = date('Y') - 10; $i >= 1900; $i--)
            $tab[] = $i;

        return $tab;
    }

    /**
     * 生成日
     *
     * @return array
     */
    public static function dateDays() {
        $tab = array();

        for ($i = 1; $i != 32; $i++)
            $tab[] = $i;

        return $tab;
    }

    /**
     * 生成月
     *
     * @return array
     */
    public static function dateMonths() {
        $tab = array();

        for ($i = 1; $i != 13; $i++)
            $tab[$i] = date('F', mktime(0, 0, 0, $i, date('m'), date('Y')));

        return $tab;
    }

    /**
     * 根据时分秒生成时间字符串
     *
     * @param $hours
     * @param $minutes
     * @param $seconds
     *
     * @return string
     */
    public static function hourGenerate($hours, $minutes, $seconds) {
        return implode(':', array($hours, $minutes, $seconds));
    }

    /**
     * 一日之初
     *
     * @param $date
     *
     * @return string
     */
    public static function dateFrom($date) {
        $tab = explode(' ', $date);
        if (!isset($tab[1]))
            $date .= ' ' . self::hourGenerate(0, 0, 0);

        return $date;
    }

    /**
     * 一日之终
     *
     * @param $date
     *
     * @return string
     */
    public static function dateTo($date) {
        $tab = explode(' ', $date);
        if (!isset($tab[1]))
            $date .= ' ' . self::hourGenerate(23, 59, 59);

        return $date;
    }

    /**
     * 获取精准的时间
     *
     * @return int
     */
    public static function getExactTime() {
        return microtime(true);
    }

    /**
     * 获取日期
     *
     * @param null $timestamp
     *
     * @return bool|string
     */
    public static function getSimpleDate($timestamp = null) {
        if ($timestamp == null) {
            return date('Y-m-d');
        } else {
            return date('Y-m-d', $timestamp);
        }
    }

    /**
     * 获取完整时间
     *
     * @param null $timestamp
     *
     * @return bool|string
     */
    public static function getFullDate($timestamp = null) {
        if ($timestamp == null) {
            return date('Y-m-d H:i:s');
        } else {
            return date('Y-m-d H:i:s', $timestamp);
        }
    }

    /**
     * 日期计算
     *
     * @param $interval
     * @param $step
     * @param $date
     *
     * @return bool|string
     */
    public static function dateadd($interval, $step, $date) {
        list($year, $month, $day) = explode('-', $date);
        if (strtolower($interval) == 'y') {
            return date('Y-m-d', mktime(0, 0, 0, $month, $day, intval($year) + intval($step)));
        } elseif (strtolower($interval) == 'm') {
            return date('Y-m-d', mktime(0, 0, 0, intval($month) + intval($step), $day, $year));
        } elseif (strtolower($interval) == 'd') {
            return date('Y-m-d', mktime(0, 0, 0, $month, intval($day) + intval($step), $year));
        }

        return date('Y-m-d');
    }

    public static function echo_microtime($tag) {
        list($usec, $sec) = explode(' ', microtime());
        echo $tag . ':' . ((float) $usec + (float) $sec) . "\n";
    }

    public static function getmicrotime() {
        list($usec, $sec) = explode(" ", microtime());

        return floor($sec + $usec * 1000000);
    }
}