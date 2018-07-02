<?

require_once '../vendor/autoload.php';

use app\Mailer;
use app\Message;


$mailer = new Mailer();
$mailer->auth('test4send@mail.ru', '!@#$%^&*()');
$msg = new Message();
$msg->to('mr4erk@gmail.com')
	->subject('test subject api')
	->message('this is test message from api ['.date('d/m H:i:s').']');
$mailer->sendMessage($msg);
