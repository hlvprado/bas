<?php
$year = date("Y");

class BAS {
    protected $date = "";
    protected $ar_return_values = [];

    // this method is used for tests in application
    private function dump($value) {
        echo("<br>--------------------------------<br>");
        var_dump($value);
    }

    public function getArrayValues() {
        return $this->ar_return_values;
    }

    public function getNextDate($year, $month, $bonus = false) {
        $month += $bonus ? 1 : 0;
        $day = $bonus ? 15 : date("t", mktime(0, 0, 0, $month, 1, $year));
        $this->date = date("Y-m-d", mktime(0, 0, 0, $month, $day, $year));

        $weekday = date("N", strtotime($this->date));

        if($weekday > 5):
            $next = 8 - $weekday;
            $next = $this->date . " +$next day" . ($next > 1 ? "s" : "");

            $weekday_str = "next " . ($bonus ? "Wednesday" : "Monday");

            return date('Y-m-d', strtotime($weekday_str, strtotime($this->date)));
        endif;

        return $this->date;
    }

    function __construct($year, $month) {
        $payment = $this->getNextDate($year, $month);
        $month_name = date("F", strtotime($this->date));
        $bonus = $this->getNextDate($year, $month + 1, true);

        $this->ar_return_values = array(
            $month_name,
            $payment,
            $bonus
        );
    }
}

$output = fopen("php://output",'w') or die("Can't open php://output");

header("Content-Type:application/csv"); 
header("Content-Disposition:attachment;filename=salary_payment.csv"); 

fputcsv($output, array('month_name','salary_payment_date','bonus_payment_date'));

for($month = 1; $month <= 12; $month++):
    $app = new BAS($year, $month);
    fputcsv($output, $app->getArrayValues());
endfor;

fputcsv($output, array('month_name','salary_payment_date','bonus_payment_date'));

fclose($output) or die("Can't close php://output");

?>