<?php
/** 
 * File:        class.checkmail.php
 * Date:        Sun 10 Oct 2010 08:58:55 PM CEST
 *
 * checks POP3 mail!
 * 
 * Changelog:
 *
 * 	[Sun 10 Oct 2010 08:58:55 PM CEST] 
 *		first sketch from scratch
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *   // some code
 * </code>
 *
 * @version $id$
 * @copyright 2010 Coder of Salvation
 * @author Coder of Salvation, sqz <info@leon.vankammen.eu>
 * @package sutra
 * 
 * ____ _  _ ___ ____ ____   ____ ____ ____ _  _ ____ _  _ ____ ____ _  _
 * ==== |__|  |  |--< |--|   |--- |--< |--| |\/| |=== |/\| [__] |--< |-:_
 * 
 * @license 
 *  *
 * Copyright (C) 2011, Sutra Framework < info@sutraphp.com | www.sutraphp.com >
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *

 */

class checkmail {

  public  $host;
  public  $user;
  public  $pwd;
  public  $filepath;
  public  $cachefile = "/data/cache/mail.cache";
  private $imapConnection = false;

  public function __construct(){  
    $sutra = sutra::get();
    $this->filepath = $sutra->_path."/data";
    $sutra->event->addListener( "SUTRA_CLOSE", $this, "cleanup" );
  }

  public function init( $host, $user, $pwd, $filepath ){
    $this->host = $host;
    $this->user = $user;
    $this->pwd = $pwd;
    if( $filepath ) $this->filepath = $filepath;
  }

  /**
   * checkMail - checks for email
   * 
   * @access public
   * @return array - containing emails 
   */
  public function check()
  {
    if( !_assert( strlen($this->host) && strlen($this->user) && strlen($this->pwd),
                  "first call init()!") ) return array();
    $sutra          = sutra::get();
    $imapString     = "{{$this->host}:110/pop3/novalidate-cert}INBOX";
    $imapConnection = $this->imapConnection = imap_open($imapString,$this->user,$this->pwd);

    $imapInfo = imap_check($imapConnection);
    if ($imapInfo->Nmsgs <= 0) return array();
    $messageOverview = imap_fetch_overview($imapConnection,"1:{$imapInfo->Nmsgs}", 0);
    if (!is_array($messageOverview) || !count($messageOverview)) return array();

    foreach ($messageOverview as $k => $info) {
      $date = date("Y-m-d H:i:s", isset($info->date) ? strtotime($info->date) : time() );
      $messageOverview[ $k ]->id        = $info->msgno;
      $messageOverview[ $k ]->code      = isset($info->message_id) ? $info->message_id : "(not set)";
      $messageOverview[ $k ]->subject   = isset($info->subject)    ? $info->subject    : "(not set)";
      $messageOverview[ $k ]->email     = isset($info->from)       ? $info->from       : "(not set)";
      $messageOverview[ $k ]->date      = $date;
      $messageOverview[ $k ]->processed = $sutra->cache->get( $info->msgno, $this->cachefile, true );
      if( !$messageOverview[ $k ]->processed )
        $sutra->cache->save( $info->msgno, $date, $this->cachefile );
    }

    return $messageOverview;
  }

  public function cleanup(){
    //quit  
    imap_expunge($this->imapConnection);
  }

  public function parseMessage($message)
  {
    if( !_assert($this->imapConnection,"please call checkMail first! (and pass output-items to this func)") )
      return;
    $imapConnection = $this->imapConnection;
    $message->body = imap_fetchbody($imapConnection, $message->id, "1");
    $structure = imap_fetchstructure($imapConnection, $message->id);
    if (!isset($structure->parts) || !count($structure->parts)) return $message;

    $messageContentId = false;
    $attachment = (object)array(
      "valid"=>false,
      "filename"=>"",
      "name"=>"",
      "encoding"=>false,
      "data"=>""
    ); 
    $attachmentId = false;
    foreach ($structure->parts as $partId=>$partParent) {
      $subParts = ( isset( $partParent->parts) && is_array($partParent->parts) ? $partParent->parts : array($partParent));
      foreach ($subParts as $part) {
        if (strtolower($part->subtype) == "plain" && $messageContentId == false) {
          $messageContentId = $partId;
        }
        if ($attachment->valid) continue;
        //if (!in_array(strtolower($part->subtype),array("octet-stream","quicktime")) && strtolower($part->disposition) != "attachment") continue;
        if ($part->ifdparameters) foreach ($part->dparameters as $object) {
          if (strtolower($object->attribute) == "filename") {
            $attachment->filename = $object->value;
            $attachment->valid = true; 
            $attachmentId = $partId;
          }
        }
        if ($part->ifparameters) foreach ($part->parameters as $object) {
          if (strtolower($object->attribute) == "name") {
            $attachment->name = $object->value;
            $attachment->valid = true;
            $attachmentId = $partId;
          }
        }

        if ($attachment->valid) $attachment->encoding = $part->encoding;
      }
    }
    _assert(($attachment->valid && $messageContentId !== false && $attachmentId !== false), "non valid attachement!");
    $attachmentData = imap_fetchbody($imapConnection, $message->id, $attachmentId+1);
    switch ($attachment->encoding) {
      case 3: /* BASE64 */
        $attachment->data = base64_decode($attachmentData);
      break;
      case 4: /* QUOTED-PRINTABLE */
        $attachment->data = quoted_printable_decode($attachmentData);
      break;
    }
    _assert( (!empty($attachment->data) && !empty($attachment->filename)), "empty or attachment without filename");
    $message->filename    = $this->saveAttachment($attachment);
		$message->filenameExt = array_pop( explode(".", $message->filename) );
    //return array(
    //              "subject"=>$message->subject,
    //              "email"=>$message->email,
    //              "filename"=>filename,
    //              "body"=>$body,
    //              "active"=>1,
    //              "date"=>$message->date,
    //              "message_code"=>$message->code
    //);
    return $message;
  }

  private function saveAttachment($attachment)
  {
    $path = $this->filepath;
    $filename = $attachment->filename;
    $fileInfo = pathinfo($filename);
    $filenameWoExt = $fileInfo['filename'];
    $count = 2;
    while (file_exists($path."/".$filename)) {
      $filename = "{$filenameWoExt}_{$count}." . ( isset($fileInfo['extension']) ? $fileInfo['extension'] : "");
      $count++;
    }
		_log("checkmail() saving attachement: ".$filename);
    $fp = fopen($path."/".$filename, "wb");
    if (!$fp) return false;
    fwrite($fp, $attachment->data);
    fclose($fp);
    return $filename;
  }
}



?>
