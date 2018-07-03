Example for Library

<pre>
require_once '../vendor/autoload.php';

use app\Mailer;
use app\Message;


$mailer = new Mailer('test4send@mail.ru', '!@#$%^&*()');  auth
$msg = new Message();
$msg->to('mr4erk@gmail.com')
	->subject('test subject api')
	->message('this is test message from api ['.date('d/m H:i:s').']  {$testimage}');
$msg->addImage(__DIR__ . '/testfile.png','testimage');
$msg->addFile(__DIR__ . '/testfile.pdf');
$mailer->sendMessage($msg);

print_r($mailer->log);
print_r($mailer->errors);
</pre>

Example for API

<pre>
data = {
    auth: {
        login: 'name@mail.ru',
        pass: 'password123'
    },
    data: {
        email: 'friend@mail.ru',
        subject: 'may dear friend',
        message: 'this is little message for you {$love_you}',
         // необязательно
        files: [ 
            {
                file: '/path/to/file/love.png',
                type: 'inline',
                name: 'love_you'
            },
            {
                file: '/path/to/other/file/this_is_little_virus.zip'
            }
        ]
    }
};

$.ajax({
    url: '/api/',
    data: data,
    method: 'POST',
});
</pre>

Response example:
<pre>
    {"success":"ok","data":[]}
    
    {"success":"error","data":["some error messages"]}
</pre>
