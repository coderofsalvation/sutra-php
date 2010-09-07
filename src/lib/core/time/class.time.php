<?
/** 
 * File:        class.time.php
 * Date:        March 12, 2008 By Frank Marcello
 * Author:      Leon van Kammen
 * 
 * Changelog:
 *
 * 	[Mon Jan 18 00:23:56 2010] 
 *    start from scratch, inspired by from Frank Marcello's dateclass
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *    $time = new time();
 *    $now  = time();
 *    $time->time->set( $now );
 *    $then = $time->time->addYear( 20 );
 *    echo date(DATE_ATOM, $then);
 *    $diff = $time->time->diff( $then );
 *    echo "<br>";
 *    echo date(DATE_ATOM, $diff );
 *    die();
 * </code>
 *
 * @package ...
 */

define( "TIME_DAY",     1);
define( "TIME_MONTH",   2);
define( "TIME_YEAR",    3);

class time
{
  public $timestamp;
  public $timestampDiff;
  private $intDays;
  private $intHours; 
  private $intMinutes;

  public function set( $timestamp ){
    $this->timestamp = $timestamp;
  }

  public function diff( $timestampDiff )
  {
    $this->timestampDiff = $timestampDiff;
    $dateDiff = ( $this->timestamp > $timestampDiff ) ? $this->timestamp - $timestampDiff : $timestampDiff - $this->timestamp;
    $this->intDays    = floor(  $dateDiff / (60*60*24) );
    $this->intHours   = floor( ($dateDiff - ($this->intDays*60*60*24) ) / (60*60) );
    $this->intMinutes = floor( ($dateDiff - ($this->intDays*60*60*24) - ($this->intHours*60*60) ) /60 );
    return $dateDiff;
  }

  function diffStringTime($t1,$t2)
  {
    $a1 = explode(":",$t1);
    $a2 = explode(":",$t2);
    $time1 = (($a1[0]*60*60)+($a1[1]*60)+( isset( $a1[2] ) ? $a1[2] : 0 ));
    $time2 = (($a2[0]*60*60)+($a2[1]*60)+( isset( $a1[2] ) ? $a1[2] : 0 ));
    $diff = abs($time1-$time2);
    $hours = floor($diff/(60*60));
    $mins = floor(($diff-($hours*60*60))/(60));
    $secs = floor(($diff-(($hours*60*60)+($mins*60))));
    $result = sprintf( "%02s", $hours ).":".sprintf( "%02s", $mins).":".sprintf( "%02d", $secs);
    return $result;
  }

  function addStringTime($t1,$t2)
  {
    $a1 = explode(":",$t1);
    $a2 = explode(":",$t2);
    $time = ((($a1[0]+$a2[0])*60*60)+(($a1[1]+$a2[1])*60)+($a1[2]));
    $hours = floor($time/(60*60));
    $mins = floor(($time-($hours*60*60))/(60));
    $secs = floor(($time-(($hours*60*60)+($mins*60))));
    $result = sprintf( "%02s", $hours ).":".sprintf( "%02s", $mins).":".sprintf( "%02d", $secs);
    return $result;
  }

  /**
   * add 
   * 
   * @param str $type can be 'month', 'day',
   * @param mixed $str 
   * @access private
   * @return void
   */
  private function add( $type, $increase = 1 ){
    $month  = false;
    $str    = date( DATE_ATOM, $this->timestamp );
    switch( $type ){
      case TIME_MONTH:
                          $month  = (int)substr( $str, 5, 2 );
                          $month  = sprintf("%02s", $month + $increase );
                          $str    = substr_replace( $str, $month, 5, 2 );
                          break;
      case TIME_DAY:
                          $day  = (int)substr( $str, 8, 2 );
                          $day  = sprintf("%02s", $day + $increase );
                          $str    = substr_replace( $str, $day, 8, 2 );
                          break;
      case TIME_YEAR:
                          $year  = (int)substr( $str, 0, 4 );
                          $year  = sprintf("%04s", $year + $increase );
                          $str    = substr_replace( $str, $year, 0, 4 );
                          break;
    }
    return strtotime( $str );
  }

  public function addDay( $days )
  {
    return $this->add( TIME_DAY, $days );
  }

  public function addMonth( $months = 1 )
  {
    return $this->add( TIME_MONTH, $months );
  }

  public function addYear( $years = 1 )
  {
    return $this->add( TIME_YEAR, $years );
  }

  public function getDiffInDays()
  { return $this->intDays;    }

  public function getDiffInHours()
  { return $this->intHours;   }
  
  public function getDiffInMinutes()
  { return $this->intMinutes; }


  function __destruct()
  {
    unset($this->dateFrom);
    unset($this->dateTo);
    unset($this->intDays);
    unset($this->intHours);
    unset($this->intMinutes); 
  }
} 

?>
