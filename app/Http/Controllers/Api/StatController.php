<?php

namespace App\Http\Controllers\Api;

use App\Connection;
use App\Http\Controllers\Controller;
use App\Payment;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return array
     */
    public function index(Request $request)
    {
        $date_start = Carbon::parse($request->get('start_date'));
        $date_end = Carbon::parse($request->get('end_date'));

        return [
            'checkoutCash' => $this->checkoutCash($date_start, $date_end),
            'debts' => $this->getDebts($date_start, $date_end),
            'increases' => $this->getIncreases($date_start, $date_end),
            'decreases' => $this->getDecreases($date_start, $date_end)
        ];

    }

    private function getDecreases($date_start, $date_end) {

        $result = [];

        $decreases = Connection::where('disable_date', '!=', null)
                                ->whereBetween('disable_date', [$date_start, $date_end])
                                ->get(['disable_date', 'price'])
                                ->toArray();

        $decreases = array_map(function ($i) {
            $i['disable_date'] = Carbon::parse($i['disable_date'])->format('Y-m');
            return $i;
        }, $decreases);

        foreach ($this->getMonths($date_start, $date_end) as $month) {
            $filtered = array_filter($decreases, function ($item) use ($month) {
                return $item['disable_date'] === $month;
            });

            $sum = array_reduce($filtered, function ($a, $c) {
                return $a + $c['price'];
            }, 0);

            array_push($result, [
                'key' => $this->getKey($month),
                'sum' => $sum
            ]);
        }

        return $result;

    }

    private function checkoutCash($date_start, $date_end) {
        $cash = $this->getCheckoutCash($date_start, $date_end);
        $months = $this->getMonths($date_start, $date_end);
        return $this->groupCheckoutCash($cash, $months);
    }

    private function getCheckoutCash($date_start, $date_end) {
        return Transaction::where('balance_change', '>', 0)
                        ->whereBetween('created_at', [$date_start, $date_end])
                        ->get(['created_at', 'balance_change'])
                        ->toArray();
    }

    private function groupCheckoutCash($cash = [], $months = []) {
        $mapped_cash = array_map(function ($i) {
            $i['created_at'] = Carbon::parse($i['created_at'])->format('Y-m');
            return $i;
        }, $cash);

        $result = [];

        foreach ($months as $month) {
            $filtered = array_filter($mapped_cash, function ($item) use ($month) {
                return $item['created_at'] === $month;
            });

            $sum = array_reduce($filtered, function ($a, $c) {
                return $a + $c['balance_change'];
            }, 0);

            array_push($result, [
                'key' => $this->getKey($month),
                'sum' => $sum
            ]);
        }
        return $result;

    }

    private function getIncreases($date_start, $date_end) {
        $result = [];
        $connections = Connection::whereBetween('date_start', [$date_start, $date_end])->get(['date_start', 'price'])->toArray();
        $connections = array_map(function ($i) {
            $i['date_start'] = Carbon::parse($i['date_start'])->format('Y-m');
            return $i;
        }, $connections);
        foreach ($this->getMonths($date_start, $date_end) as $month) {
            $filtered = array_filter($connections, function ($item) use ($month) {
                return $item['date_start'] === $month;
            });

            $sum = array_reduce($filtered, function ($a, $c) {
                return $a + $c['price'];
            }, 0);

            array_push($result, [
                'key' => $this->getKey($month),
                'sum' => $sum
            ]);
        }

        return $result;

    }

    private function getDebts($date_start, $date_end) {
        $months = $this->getEndOfMonths($date_start, $date_end);
        $debts = [];
        foreach ($months as $month) {
            $debt = Transaction::where('created_at', '<=', $month)->sum('balance_change');
            array_push($debts, [
                'key' => $this->getKey($month),
                'debt' => $debt * -1
            ]);
        }
        return $debts;
    }


    private function getKey($date) {
        $MONTHS = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
        $_date = explode('-', $date);
        return $MONTHS[intval($_date[1]) - 1] . ', ' . $_date[0];
    }

    private function getEndOfMonths($date_start, $date_end) {
        return array_map(function ($i) {
            return Carbon::parse($i)->endOfMonth();
        }, $this->getMonths($date_start, $date_end));
    }


    private function getMonths($date_start, $date_end) {
        $diffInMonths = $date_end->diffInMonths($date_start);

        $months = [];

        for ($i = 0; $i < $diffInMonths; $i++) {
            array_push($months, $date_start->addMonth()->format('Y-m'));
        }

        array_unshift($months, $date_start->subMonths($diffInMonths)->format('Y-m'));

        return $months;
    }
}
