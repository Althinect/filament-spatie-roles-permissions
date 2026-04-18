<?php

namespace Althinect\FilamentSpatieRolesPermissions\Support;

class Config
{
    public static function get(string $key, mixed $default = null): mixed
    {
        return config("filament-spatie-roles-permissions.{$key}", $default);
    }

    /**
     * @return array<class-string>
     */
    public static function resources(): array
    {
        return array_values(array_filter(self::get('resources', [])));
    }

    /**
     * @return array<string, string>
     */
    public static function guardOptions(): array
    {
        $guards = config('enum-permission.guards');

        if (is_array($guards) && $guards !== []) {
            $fallback = self::get('guards.fallback', ['web' => 'Web']);

            if (array_is_list($guards)) {
                $normalizedGuards = [];

                foreach ($guards as $guard) {
                    if (! is_string($guard) || $guard === '') {
                        continue;
                    }

                    $normalizedGuards[$guard] = $fallback[$guard] ?? $guard;
                }

                return $normalizedGuards;
            }

            return array_reduce(
                array_keys($guards),
                function (array $normalizedGuards, mixed $guard) use ($guards): array {
                    if (! is_string($guard) || $guard === '') {
                        return $normalizedGuards;
                    }

                    $normalizedGuards[$guard] = (string) $guards[$guard];

                    return $normalizedGuards;
                },
                [],
            );
        }

        return self::get('guards.fallback', ['web' => 'Web']);
    }

    public static function defaultGuard(): string
    {
        return (string) self::get('guards.default', array_key_first(self::guardOptions()) ?? 'web');
    }

    public static function guardBadgeColor(mixed $guardName): string
    {
        $colors = self::get('guards.colors', []);

        if (! is_array($colors)) {
            return 'info';
        }

        if (is_string($guardName) && $guardName !== '') {
            $configuredColor = $colors[$guardName] ?? null;

            if (is_string($configuredColor) && $configuredColor !== '') {
                return $configuredColor;
            }
        }

        $configuredColor = $colors[self::normalizeGuardName($guardName)] ?? null;

        return is_string($configuredColor) && $configuredColor !== '' ? $configuredColor : 'info';
    }

    public static function normalizeGuardName(mixed $guardName): string
    {
        $guardOptions = self::guardOptions();

        if (is_string($guardName) && array_key_exists($guardName, $guardOptions)) {
            return $guardName;
        }

        if (is_int($guardName) || (is_string($guardName) && is_numeric($guardName))) {
            $guardKeys = array_keys($guardOptions);

            return (string) ($guardKeys[(int) $guardName] ?? self::defaultGuard());
        }

        return self::defaultGuard();
    }
}
