<?php

declare(strict_types=1);

namespace Base\User\Enums;

enum Role: string
{
    case Penumpang = 'penumapang';
    case Admin = 'admin';
    case Pimpinan = 'pimpinan';

    public function label(): string
    {
        return match ($this) {
            Role::Admin => 'Admin',
            Role::Pimpinan => 'Pimpinan',
            Role::Penumpang => 'Penumpang',
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

    public function isAdmin(): bool
    {
        return $this === Role::Admin;
    }

    public function isPimpinan(): bool
    {
        return $this === Role::Pimpinan;
    }

    public function isPenumpang(): bool
    {
        return $this === Role::Penumpang;
    }
}
