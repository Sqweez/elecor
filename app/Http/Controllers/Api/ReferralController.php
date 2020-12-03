<?php

namespace App\Http\Controllers\Api;

use App\Client;
use App\Connection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\CryptService;
use App\ReferralSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ReferralController extends Controller
{
    protected $FROM_SITE = 'elecor.web';
    protected $FROM_MOBILE_APP = 'elecor.mobile';
    protected $FROM_ADMIN = 'elecor.admin';

    // Получение настроек
    public function getSettings() {
        return ReferralSettings::select(['message_template', 'base_url', 'discount', 'hash_ref'])->first();
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

        $connection = Connection::account($personal_account)->first();

        if (!$connection) {
            return $this->errorResponse('Некорректная реферальная ссылка');
        }

        return response()->json([
            'client_id' => $connection->client_id,
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

        $connection = Connection::account($request->get('promocode'))->first();
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

    // Зачисляем бонусы при успешной оплате по реферальной ссылке
    public function creditBonuses(Request $request) {
        // @TODO зачислеяем бонусы, в транзакцию пишем максимальное количество данных
        // @TODO принимать будем client_id, personal_account? и какие-нибудь данные с сайта
        // @TODO если можно будет принимать фамилию, имя, отчество будет здорово
    }

    // Списываем бонусы при успешной трате
    public function debitBonuses() {
        // @TODO списываем бонусы, в транзакцию пишем максимальное количество данных
        // @TODO принимать будем client_id, personal_account, connection_id?, type?, amount?,
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

        $connection = Connection::account($account)->first();
        if (!$connection) {
            return $this->errorResponse('Данный лицевой счет не зарегистрирован! Обратитесь к менеджерам в случае ошибки.');
        }

        // @TODO: возвращать будем бонусы, клиент ид и лиц счет
        return [
            'bonuses' => 100,
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

        // @TODO продумать как с бонусами быть

        return [
            'client_id' => $client->id,
            'client_name' => $client->name,
            'bonuses' => 100
        ];
    }

    // Генерация QR-кода
    public function getQRCode($client, Request $request) {
        // Получаем клиента по ID
        // Получаем его реф ссылку
        return $this->getReferralUrl($client);
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
