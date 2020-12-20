<?php

namespace App\Http\Controllers\Api;

use App\BonusTransaction;
use App\Client;
use App\Connection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\CryptService;
use App\ReferralSettings;
use App\Service;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ReferralController extends Controller
{
    protected $FROM_SITE = 'elecor.web';
    protected $FROM_MOBILE_APP = 'elecor.mobile';
    protected $FROM_ADMIN = 'elecor.admin';
    protected $QR_CODE_SIZE = 200;
    protected $PAYBOX_RESULT_OK = 1;

    // Получение настроек
    public function getSettings() {
        return ReferralSettings::select(['message_template', 'base_url', 'discount', 'hash_ref', 'cashback'])->first();
    }
    // Обновление настроек
    public function updateSettings(Request $request) {
        ReferralSettings::first()->update($request->all());
        return $this->getSettings();
    }
    // Валидация реферальной ссылки
    public function validateReferral(Request $request) {
        $ref = $request->get('ref', null);
        $settings = $this->getSettings();
        try {
            $personal_account = $settings->hash_ref === true ? $this->decryptReferral($ref) : $ref;
        } catch (\Exception $e) {
            return $this->errorResponse('Некорректная реферальная ссылка');
        }

        if (!$this->validatePersonalAccount($personal_account)) {
            return $this->errorResponse('Некорректная реферальная ссылка');
        }

        $connection = Connection::account($personal_account)->where('is_deleted', false)->first();

        if (!$connection) {
            return $this->errorResponse('Некорректная реферальная ссылка');
        }

        return response()->json([
            'client_id' => $connection->client_id,
            'account' => $connection->personal_account,
            'discount' => $settings->discount,
        ], 200);
    }

    // Валидация промокода
    public function validateCoupon(Request $request) {
        $validator = \Validator::make($request->all(), [
            'promocode' => 'required|regex:#\d{6}#'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Неверный промокод!');
        }

        $connection = Connection::account($request->get('promocode'))->where('is_deleted', false)->first();
        if (!$connection) {
            return $this->errorResponse('Неверный промокод!');
        }

        $settings = $this->getSettings();

        return [
            'client_id' => $connection->client_id,
            'discount' => $settings->discount,
        ];
    }

    // Получение реферальной ссылки
    public function getReferralUrl($client_id) {
        $settings = $this->getSettings();
        $connection = Connection::where('client_id', $client_id)->where('is_deleted', false)->select('personal_account')->first();
        if (!$connection) {
            return response()->json([
                'message' => 'Невозможно сформировать ссылку для приглашения. Пользователь с вашими данными не найден или не имеет подключенных услуг'
            ], 500);
        }

        $ref = $settings->hash_ref === true ? $this->encryptReferral($connection->personal_account) : $connection->personal_account;

        return "{$settings->base_url}?ref={$ref}";
    }

    public function getReferralMessage($client_id) {
        $settings = $this->getSettings();
        $url = $this->getReferralUrl($client_id);
        return "{$settings->message_template}\n{$url}";
    }

    // Сохраняем бонусную операцию
    public function storeBonuses(Request $request) {
        $operation_type = $request->get('operation_type');
        $connection_id = $request->get('connection_id', -1);
        $user_id = $request->get('user_id', -1);
        $client_id = $request->get('client_id');
        $amount = $request->get('amount');
        $comment= $request->get('comment');
        return $this->createBonusOperation($operation_type, $connection_id, $user_id, $client_id, $amount, $comment);

    }

    public function getReferralInformationMobile() {
        return "
            <h1>Информация по реферральной программе:</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab alias commodi consequatur consequuntur dignissimos distinctio dolore dolorem doloremque dolorum eaque, est explicabo impedit laborum mollitia obcaecati officia pariatur quos repellat sit sunt suscipit ullam veniam veritatis voluptas voluptatem? At aut commodi ea eius expedita nihil placeat, tempore temporibus unde voluptatibus? Atque autem cumque delectus, hic inventore nisi nobis nulla possimus quae quasi quo repellat repellendus veniam? Accusamus amet assumenda commodi excepturi exercitationem fugiat id magni molestiae nulla placeat porro repellat rerum sequi sit sunt ut, veritatis. Ad, assumenda cupiditate ea earum eligendi exercitationem iste maxime perferendis quo rem tempore vel.</p>
        ";
    }

    public function getPaymentInformation(){
        return [
            'max_percent' => 100,
            'services' => [1, 2, 3, 4, 5, 6],
        ];
    }

    public function getQRInformation(){
        return "Поделитесь QR-кодом, чтобы получить бонусы!";
    }

    public function createBonusOperation(int $operation_type, int $connection_id, int $user_id, int $client_id, int $amount, string $comment) {
        $operation = [
            'client_id' => $client_id,
            'amount' => $amount,
            'comment' => $comment,
            'user_id' => $user_id
        ];
        switch ($operation_type) {
            case BonusTransaction::OPERATION_TYPE_CREDIT:
                return $this->credit($operation);
                break;
            case BonusTransaction::OPERATION_TYPE_DEBIT:
                $operation['connection_id'] = $connection_id;
                $operation['amount'] = $operation['amount'] * -1;
                return $this->debit($operation);
                break;
            case BonusTransaction::OPERATION_TYPE_CASH_OUT:
                $operation['amount'] = $operation['amount'] * -1;
                return $this->cashOut($operation);
                break;
            default:
                break;
        }
    }

    public function credit(array $operation) {
        try {
            \DB::beginTransaction();
            $operation['operation_type'] = BonusTransaction::OPERATION_TYPE_CREDIT;
            $bonus = BonusTransaction::create($operation);
            \DB::commit();
            return $bonus;
        } catch (\Exception $exception) {
            \DB::rollBack();
            return response()->json(['message' => $exception->getMessage()], 500);
        }

    }

    public function debit(array $operation) {
        try {
            \DB::beginTransaction();
            $operation['operation_type'] = BonusTransaction::OPERATION_TYPE_DEBIT;
            $bonus_transaction = BonusTransaction::create($operation);
            Transaction::create([
                'connection_id' => $bonus_transaction->connection_id,
                'balance_change' => $bonus_transaction->amount * -1,
                'user_id' => $bonus_transaction->user_id,
                'is_bonus' => true
            ]);
            \DB::commit();
            return $bonus_transaction;
        } catch (\Exception $exception) {
            \DB::rollBack();
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    private function cashOut(array $operation) {
        try {
            \DB::beginTransaction();
            $operation['operation_type'] = BonusTransaction::OPERATION_TYPE_CASH_OUT;
            $bonus_transaction = BonusTransaction::create($operation);
            \DB::commit();
            return $bonus_transaction;
        } catch (\Exception $exception) {
            \DB::rollBack();
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    // Зачисляем бонусы при успешной оплате по реферальной ссылке
    public function creditBonuses(Request $request) {
        // @TODO зачислеяем бонусы, в транзакцию пишем максимальное количество данных
        // @TODO принимать будем client_id, comment? и какие-нибудь данные с сайта
        // @TODO если можно будет принимать фамилию, имя, отчество будет здорово
        Log::debug(json_encode($request->all()));
        $hasResult = $request->has('pg_result');
        $resultCode = $request->get('pg_result', 0);
        if ($hasResult && intval($resultCode) !== $this->PAYBOX_RESULT_OK) {
            Log::debug('Оплата не прошла');
            return $this->errorResponse('Оплата не прошла!');
        }

        $validator = Validator::make($request->all(), [
            'client_id' => 'required',
        ]);

        if ($validator->fails()) {
            Log::debug('Некорректно переданы данные!');
            return $this->errorResponse('Некорректно переданы данные!');
        }

        $client_id = $request->get('client_id');
        $full_name = $request->get('full_name', '');

        $client = Client::find($client_id);

        if (!$client) {
            Log::debug('Клиент не найден!');
            return $this->errorResponse('Клиент не найден!');
        }

        $settings = $this->getSettings();

        return $this->credit(
            [
                'client_id' => $client->id,
                'amount' => $settings->cashback,
                'comment' => 'Начисление средств по реферральной ссылке ' . $full_name
            ]
        );

    }

    // Списываем бонусы при успешной трате
    public function debitBonuses(Request $request) {
        // @TODO принимать будем account, amount,
        $validator = \Validator::make($request->all(), [
            'account' => 'required|regex:#\d{6}#',
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Переданы некорретные данные');
        }

        $connection = Connection::where('personal_account', $request->get('account'))->where('is_deleted', false)->first();


        if (!$connection) {
            return $this->errorResponse('Подключение не найдено!');
        }

        $amount = $request->get('amount');


        $bonus_balance = BonusTransaction::where('client_id', $connection->client_id)->sum('amount');



        if ($bonus_balance < $amount) {
            return $this->errorResponse('Недостаточно средств');
        }

        $service = Service::find($connection->service_id);

        return $this->debit([
            'amount' => $amount * -1,
            'connection_id' => $connection->id,
            'comment' => 'Списание бонусных средств за пользование услугой ' . $service->name,
            'client_id' => $connection->client_id,
        ]);
    }

    public function getBonuses($client, Request $request) {
        $validator = \Validator::make($request->all(), [
            'type' => [
                'required',
                Rule::in([$this->FROM_ADMIN, $this->FROM_MOBILE_APP, $this->FROM_SITE])
            ],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Некорректный параметр type');
        }

        if ($request->get('type') === $this->FROM_SITE) {
            return $this->getBonusesByAccount($client);
        }

        return $this->getBonusesById($client);
    }

    private function getBonusesByAccount($account) {
        $validator = \Validator::make(['account' => $account], [
            'account' => 'required|regex:#\d{6}#'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Некорректный лицевой счет!');
        }

        $connection = Connection::account($account)->where('is_deleted', false)->first();
        if (!$connection) {
            return $this->errorResponse('Данный лицевой счет не зарегистрирован! Обратитесь к менеджерам в случае ошибки.');
        }

        return [
            'bonuses' => $connection->client->bonus_transactions->sum('amount'),
            'personal_account' => $connection->personal_account,
            'client_id' => $connection->client_id,
            'client_name' => $connection->client->name
        ];
    }

    private function getBonusesById($client_id) {
        $validator = \Validator::make(['client_id' => $client_id], [
            'client_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Некорретный параметр клиента');
        }

        $client = Client::find($client_id);

        if (!$client) {
            return $this->errorResponse('Клиент не найден');
        }

        return [
            'client_id' => $client->id,
            'client_name' => $client->name,
            'bonuses' => $client->bonus_transactions->sum('amount')
        ];
    }

    // Генерация QR-кода
    public function getQRCode($client, Request $request) {
        $qrImageFormat = $request->get('format', 'svg');
        $qrImageSize = $request->get('size', 200);
        $referralUrl = $this->getReferralUrl($client);
        if (gettype($referralUrl) !== "string") {
            return response()->json([
                'message' => 'Невозможно сформировать QR-код!'
            ], 422);
        }
        $qrImage = QrCode::format($qrImageFormat)->size($qrImageSize)->generate($referralUrl);
        return response($qrImage)->header('Content-type', "image/$qrImageFormat");
    }

    public function getOperationTypes() {
        return collect(BonusTransaction::OPERATION_TYPES)->values();
    }

    // Возвращаем ошибку
    private function errorResponse($message) {
        return response()->json([
            'message' => $message
        ], 418);
    }

    // Шифруем строку
    private function encryptReferral($referral) {
        $cryptService = new CryptService();
        return $cryptService->encryptString($referral);
    }

    // Расшифроваем строку
    private function decryptReferral($referral) {
        $cryptService = new CryptService();
        return $cryptService->decryptString($referral);
    }

    // Валидация лиц счета регулярным выражением
    private function validatePersonalAccount($decrypted) {
        // Валидируем персональный аккаунт, должен состоять из 6 цифр
        $pattern = '#\d{6}#';
        return preg_match($pattern, $decrypted);
    }
}
