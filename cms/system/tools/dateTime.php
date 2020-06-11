<?php

class tools_dateTime {

    static $weekdays = [
        '','Понедельник','Вторник',"Среда","Четверг","Пятница","Суббота","Воскресенье"
    ];
    
    static function timeAgo($time) {

        $month_name = array(1 => 'января',
                    2 => 'февраля',
                    3 => 'марта',
                    4 => 'апреля',
                    5 => 'мая',
                    6 => 'июня',
                    7 => 'июля',
                    8 => 'августа',
                    9 => 'сентября',
                    10 => 'октября',
                    11 => 'ноября',
                    12 => 'декабря'
        );

        $month = $month_name[date('n', $time)];

        $day = date('j', $time);
        $year = date('Y', $time);
        $hour = date('G', $time);
        $min = date('i', $time);

        $date = $day . ' ' . $month . ' ' . $year . ' г. в ' . $hour . ':' . $min;

        $tm = time();

        $dif = $tm - $time;

        if ($dif < 59) {
            return $dif . " сек. назад";
        } elseif ($dif / 60 > 1 and $dif / 60 < 59) {
            return round($dif / 60) . " мин. назад";
        } elseif ($dif / 3600 > 1 and $dif / 3600 < 23) {
            return round($dif / 3600) . " час. назад";
        } else {
            return $date;
        }
    }

    static function getCyrMonths() {
        $months = array(
            1 => 'Января',
            2 => 'Февраля',
            3 => "Марта",
            4 => "Апреля",
            5 => "Мая",
            6 => "Июня",
            7 => "Июля",
            8 => "Августа",
            9 => "Сентября",
            10 => "Октября",
            11 => "Ноября",
            12 => "Декабря"
        );

        return $months;
    }

    static function getCyrMonthName($m_num) {
        $m = self::getCyrMonths();

        return $m[(int) $m_num];
    }

    static function getAgeSuffix($years) {

        if ($years > 9 and $years < 21)
            return 'лет';

        $num = (int) substr($years, strlen($years) - 1, 1);

        $sufix = 'лет';

        switch (true) {
            case ($num == 1):
                $sufix = 'год';
                break;

            case ($num > 1 and $num < 5):
                $sufix = 'года';
                break;

            case ($num > 4 || $num == 0):
                $sufix = 'лет';
                break;
        }

        return $sufix;
    }

    static function goodDay() {
        
        return "Добрый день";
        
        
    }
    
    static function getWorkDaysLeftInMonth() {
        
        $current_day = $d = date('d');
        $t = time();
        $days = 0;
        
        while($d>=$current_day) {
            $t+=(3600*24);
            $d = (int)strftime("%d",$t);
            $dow = date("N",$t);
            
            if (in_array($dow,[6,7])) continue;
            $days++;
        }
        return $days;
    }
}
