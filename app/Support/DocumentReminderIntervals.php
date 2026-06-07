<?php

namespace App\Support;

class DocumentReminderIntervals
{
    /** @var list<int> */
    public const ALLOWED_MONTHS = [1, 3, 6, 9, 12];

    /**
     * @return array{neutral: int, green: int, yellow: int}|null
     */
    private static function thresholds(int $reminderMonths): ?array
    {
        return match ($reminderMonths) {
            1 => ['neutral' => 30, 'green' => 15, 'yellow' => 7],
            3 => ['neutral' => 90, 'green' => 45, 'yellow' => 18],
            6 => ['neutral' => 180, 'green' => 90, 'yellow' => 36],
            9 => ['neutral' => 270, 'green' => 135, 'yellow' => 54],
            12 => ['neutral' => 360, 'green' => 180, 'yellow' => 72],
            default => null,
        };
    }

    public static function resolveStatus(int $reminderMonths, int $daysLeft): string
    {
        if ($daysLeft < 0) {
            return 'expired';
        }

        $thresholds = self::thresholds($reminderMonths);

        if ($thresholds === null) {
            $totalDays = max(30, $reminderMonths * 30);
            $thresholds = [
                'neutral' => $totalDays,
                'green' => (int) round($totalDays * 0.5),
                'yellow' => (int) round($totalDays * 0.25),
            ];
        }

        if ($daysLeft > $thresholds['neutral']) {
            return 'neutral';
        }

        if ($daysLeft >= $thresholds['green']) {
            return 'green';
        }

        if ($daysLeft >= $thresholds['yellow']) {
            return 'yellow';
        }

        return 'red';
    }

    /**
     * @return array{state: string, label: string, days_left: int}
     */
    public static function resolveState(int $reminderMonths, int $daysLeft): array
    {
        $state = self::resolveStatus($reminderMonths, $daysLeft);

        $label = match ($state) {
            'expired' => 'Expired',
            'yellow', 'red' => 'Mendekati expired',
            default => 'Reminder aktif',
        };

        return [
            'state' => $state,
            'label' => $label,
            'days_left' => $daysLeft,
        ];
    }
}
