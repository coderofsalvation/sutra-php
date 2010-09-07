<?
/**
 * This is a dbObject
 */
class dbObject_sutra_page_comment{
  public $id;                 // [int]
  public $author;             // [varchar]
  public $date;               // [date]
  public $email;              // [varchar]
  public $website;            // [varchar]
  public $html;               // [html]
  public $page_id;            // [int]

  public function __construct() { 
     dbManager::addRelation( 'page', 'sutra_page_comment.page_id', 'sutra_page.id' ); 
  }
}
dbObject::addDecorator( new dbObject_sutra_page_comment(), "sutra_page_comment" );
