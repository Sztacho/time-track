<?php
declare(strict_types=1);

namespace App\Application\Shared\Traits;

trait EnumTrait
{
    public static function toArray(): array
    {
        foreach (self::cases() as $case) {
            $map[$case->name] = $case->value;
        }

        return $map ?? [];
    }

    public static function getCaseByKey($key): self | null
    {
        $enum = null;
        foreach (self::cases() as $case) {
            if(strtolower($case->name) == $key) {
                $enum = $case;
            }
        }

        return $enum;
    }
}