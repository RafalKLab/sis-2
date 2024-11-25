<?php

namespace App\Business\Order\Score;
use App\Models\Order\Order;

class OrderScoreCalculator
{
    public function calculateInitScores(): void
    {
        $orders = Order::where('parent_id', null)->get();

        foreach ($orders as $order) {
            $score = $order->id;

            $order->score = $score;
            $order->save();

            $this->calculateChildScores($order);
        }
    }

    public function calculateOrderScore(Order $order): void
    {
        if ($order->parent === null) {
            $score = $order->id;

            $order->score = $score;
            $order->save();
        } else {
            $parentOrder = $order->parent;
            $parentScore = $parentOrder->score;

            $generations = substr_count($parentOrder->getKeyField(),'-');
            $multiplier = 0.01;

            for ($i = 1; $i <= $generations; $i++) {
                if ($i > 1) {
                    $multiplier /= 10;
                }
            }

            $amountOfChildren = $parentOrder->children()->count();

            $score = $parentScore + $multiplier * $amountOfChildren;

            $order->score = $score;
            $order->save();
        }
    }

    protected function calculateChildScores(Order $order): void
    {
        if ($order->children()->count() === 0) {
            return;
        }

        $parentScore = $order->score;
        $generations = substr_count($order->getKeyField(),'-');
        $multiplier = 0.01;

        for ($i = 1; $i <= $generations; $i++) {
            if ($i > 1) {
                $multiplier /= 10;
            }
        }

        foreach ($order->children as $index => $childOrder) {
            $score = $parentScore + $multiplier * ($index + 1);

            $childOrder->score = $score;
            $childOrder->save();

            $this->calculateChildScores($childOrder);
        }
    }
}
