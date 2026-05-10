<?php

declare(strict_types=1);

namespace Base\Tiket\Enums;

enum StatusPerjalanan: string
{
    case Berlangsung = 'berlangsung';
    case Selesai     = 'selesai';

    public function label(): string
    {
        return match ($this) {
            StatusPerjalanan::Berlangsung => 'Berlangsung',
            StatusPerjalanan::Selesai     => 'Selesai',
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

    public function isBerlangsung(): bool
    {
        return $this === StatusPerjalanan::Berlangsung;
    }

    public function isSelesai(): bool
    {
        return $this === StatusPerjalanan::Selesai;
    }
}
