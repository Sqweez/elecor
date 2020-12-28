<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    public function getReferralInformation() {
        return "
            <h1>Информация по реферральной программе:</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab alias commodi consequatur consequuntur dignissimos distinctio dolore dolorem doloremque dolorum eaque, est explicabo impedit laborum mollitia obcaecati officia pariatur quos repellat sit sunt suscipit ullam veniam veritatis voluptas voluptatem? At aut commodi ea eius expedita nihil placeat, tempore temporibus unde voluptatibus? Atque autem cumque delectus, hic inventore nisi nobis nulla possimus quae quasi quo repellat repellendus veniam? Accusamus amet assumenda commodi excepturi exercitationem fugiat id magni molestiae nulla placeat porro repellat rerum sequi sit sunt ut, veritatis. Ad, assumenda cupiditate ea earum eligendi exercitationem iste maxime perferendis quo rem tempore vel.</p>
        ";
    }

    public function getQRInformation() {
        return "Поделитесь QR-кодом, чтобы получить бонусы!";
    }

    public function getPaymentInformation() {
        return [
            'max_percent' => 95,
            'services' => [1, 2, 3, /*4,*/ 5, 6],
            'message' => "Вы можете оплатить до 95% от стоимости услуги бонусами!"
        ];
    }

    public function getRecurrentInformation() {
        return " http://docs.google.com/gview?embedded=true&url=" . url('/') . \Storage::url('public/documents/recurrent_rules.pdf');
    }
}
