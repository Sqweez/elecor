<?php

namespace App\Http\Controllers;

use App\Client;
use App\Connection;
use App\Http\Controllers\Service\PushService;
use App\Http\Resources\ConnectionResource;
use App\Payment;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CronController extends Controller
{
    public function monthlyBalanceChange() {
        $count = 0;
        $connections = Connection::where('is_active', true)->with(['payments', 'service'])->orderByDesc('id')->get();

        $connections = $connections->filter(function ($connection) {
            $payments = collect($connection['payments'])->filter(function ($i) {
                return $i['sale_id'] === null && $i['created_at'] >= Carbon::today()->startOfMonth();
            });
            return count($payments) === 0;
        });

        foreach ($connections as $connection) {
            $connection['service_name'] = $connection['service']['name'];
            $count++;
            $date_start = Carbon::parse($connection['date_start']);
            if ($date_start > Carbon::now()->startOfMonth() && $date_start <= Carbon::now()->endOfMonth()) {
                $connection['price'] = $connection['month_price'];
            }
            $payment = ['connection_id' => $connection['id'], 'price' => $connection['price']];

            $transaction = ['connection_id' => $connection['id'], 'balance_change' => $connection['price'] * -1, 'user_id' => 0];

            Payment::create($payment);
            Transaction::create($transaction);

            $message = ['title' => 'Внимание', 'body' => 'С Вашего баланса произошло списание ' . $connection['price'] . ' тг по услуге ' . $connection['service_name'] . '.!'];

            $token = PushService::getToken($connection['client_id']);

            if (strlen($token) > 0) {
                PushService::sendPush($message, $token);
            }
        }

        return 'success' . $count;
    }

    public function dailyBirthday() {
        $today = Carbon::now();
        $month = $today->month;
        $day = $today->day;
        $CONGRATULATION_MESSAGE = [
            'ru' => [
                'M' => 'Уважаемый, %ИМЯ%! Коллектив ОА  «ELECOR» сердечно поздравляет Вас с Днём Рождения! Улыбок, здоровья, радости желаем Вам! Удачи во всём и всегда хорошего настроения!!!',
                'F' => 'Уважаемая, %ИМЯ%! Коллектив ОА  «ELECOR» сердечно поздравляет Вас с Днём Рождения! Улыбок, здоровья, радости желаем Вам! Удачи во всём и всегда хорошего настроения!!!'
            ],
            'kz' => [
                'M' => 'құрметті, %ИМЯ%! Бiз сiздi туған күнiңiзбен шың жүректен құттықтаймыз. Сiзге мол бақыт, ашық аспан және зор денсаулық тілейміз. Сiздiң көңіл-күйiңiз әрқашан жоғары деңгейде болсын. Iзгi ниетпен Сiздiң "ELECOR" Күзет Агенттігінің ұжымы.',
                'F' => 'құрметті, %ИМЯ%! Бiз сiздi туған күнiңiзбен шың жүректен құттықтаймыз. Сiзге мол бақыт, ашық аспан және зор денсаулық тілейміз. Сiздiң көңіл-күйiңiз әрқашан жоғары деңгейде болсын. Iзгi ниетпен Сiздiң "ELECOR" Күзет Агенттігінің ұжымы.',
            ]
        ];

        $CONGRATULATION_TITLE = [
            'ru' => 'Внимание!',
            'kz' => 'Назар аударыңыз!'
        ];

        $clients = collect(Client::whereMonth('birth_date', $month)->whereDay('birth_date', $day)->select(['id', 'name', 'gender', 'lang', 'push_token'])->get());
        $clients->each(function ($client) use ($CONGRATULATION_MESSAGE, $CONGRATULATION_TITLE){
            $title = $CONGRATULATION_TITLE[$client['lang']];
            $message = $CONGRATULATION_MESSAGE[$client['lang']][$client['gender']];
            $message = str_replace('%ИМЯ%', $client['name'], $message);
            if ($client['push_token']) {
                PushService::sendPush([
                    'title' => $title,
                    'body' => $message
                ], $client['push_token']);
            }
        });
        return $clients;
    }
}
