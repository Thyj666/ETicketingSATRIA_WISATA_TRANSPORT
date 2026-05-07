<?php

declare(strict_types=1);

namespace Base\Armada\Enums;

enum Status: string
{
    case Tersedia = 'tersedia';
    case TidakTersedia = 'tidak_tersedia';

    public function label(): string
    {
        return match ($this) {
            Status::Tersedia => 'Tersedia',
            Status::TidakTersedia => 'Tidak Tersedia',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return array_map(fn($r) => ['value' => $r->value, 'label' => $r->label()], self::cases());
    }

    public function isTersedia(): bool
    {
        return $this === Status::Tersedia;
    }

    public function isTidakTersedia(): bool
    {
        return $this === Status::TidakTersedia;
    }
}
