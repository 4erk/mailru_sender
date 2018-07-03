<?php

use app\Mailer;
use app\Message;

require_once '../vendor/autoload.php';

$emails = [
	'mr4erk@gmail.com',
	'nadyai61@mail.ru',
	'alfencka@mail.ru',
	'2fsintez@bk.ru',
	'gsboris@mail.ru',
	'data21@mail.ru',
	'2009_andrei@mail.ru',
	'topalovanv85@mail.ru',
	'sinkevich.lina@mail.ru',
	'keet.07@mail.ru',
	'helgast1@gmail.com',
	'79215559916@mail.ru',
	'pligina_galina@mail.ru',
	'selectel56@mail.ru',
	'd.baranov88@mail.ru',
	'bondaoksa@mail.ru',
	'kve6767@mail.ru',
	'z1z7z7@mail.ru',
	'salamandra_83@mail.ru',
	'dimas20f@mail.ru',
	'ivan_gvr@mail.ru',
	'nastasyaya1@mail.ru',
	'boikova_katya@mail.ru',
	'atlanta@list.ru',
	'nastasyaya1@mail.ru',
	'gritsak.inna@mail.ru',
	'mantigr@mail.ru',
	'comss@list.ru',
	'ev.borch@mail.ru',
	'dobra@list.ru',
	'egorov.alexi@mail.ru',
	'bio.net@mail.ru',
	'andrey-rachinskiy@mail.ru',
	'linkin_89@mail.ru',
	'arfa@bk.ru',
	'yuki-86@mail.ru',
	'moskvaok@mail.ru',
	'zabeyka2908@mail.ru',
	'kirill_gorev@mail.ru',
	'dnadegda@mail.ru',
	'sergey_vlasov92@mail.ru',
	'ilona_2009_s@mail.ru',
	'margoshenka@mail.ru',
	'didi.77@list.ru',
	'dianas1971@mail.ru',
	'ryabovdn@mail.ru',
	'dmitriv_ira@mail.ru',
	'erypalov@mail.ru',
	'kamshat75@mail.ru',
	'olgaalikova@mail.ru',
	'bantyulya@mail.ru',
	'ozersk_sever@mail.ru',
	'veronika.cherkashina.86@mail.ru',
];

$mailer = new Mailer('test4send@mail.ru', '!@#$%^&*()');

foreach ($emails as $email) {
	set_time_limit(0);
	$msg = new Message();
	$msg->to($email)
		->subject('This test mail')
		->message('My test message with image number 2 {$testimage}');
	$msg->addImage(__DIR__ . '/testfile.png', 'testimage');
	$msg->addFile(__DIR__ . '/testfile.png');
	$mailer->sendMessage($msg);
	sleep(30);
}
echo '<pre>';
print_r($mailer->log);
echo  '</pre>';
