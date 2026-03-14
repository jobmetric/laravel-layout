<?php

namespace JobMetric\Layout\Support;

use Illuminate\Support\Arr;
use InvalidArgumentException;

/**
 * Registry for layout applications. Each application has:
 * - key: English-only identifier for storage (a-z, 0-9, underscore).
 * - name: Translation key for display; output via trans().
 * - description: Translation key for description; output via trans().
 *
 * @package JobMetric\Layout
 *
 * @property array<string, array<string, mixed>> $applications
 * @property array<string, int> $registrationOrder
 * @property int $nextOrder
 */
class ApplicationRegistry
{
    /**
     * Registered applications: key => options (name, description, order, ...).
     *
     * @var array<string, array<string, mixed>>
     */
    protected array $applications = [];

    /**
     * Registration order for stable sort when order option is equal.
     *
     * @var array<string, int>
     */
    protected array $registrationOrder = [];

    /**
     * Next registration index.
     *
     * @var int
     */
    protected int $nextOrder = 0;

    /**
     * Register an application.
     *
     * @param string $key                   Storage key; must be English only (a-z, 0-9, _).
     * @param array<string, mixed> $options name (string, trans key), description (string, trans key), order (int), ...
     *
     * @return self
     * @throws InvalidArgumentException When key is not English-only.
     */
    public function register(string $key, array $options = []): self
    {
        $key = $this->normalizeKey($key);

        if (! $this->isValidKey($key)) {
            throw new InvalidArgumentException('Layout application key must be English only (a-z, 0-9, underscore).');
        }

        if (! isset($this->applications[$key])) {
            $this->registrationOrder[$key] = $this->nextOrder++;
        }

        $this->applications[$key] = array_merge($this->applications[$key] ?? [], $options);

        return $this;
    }

    /**
     * Remove an application from the registry.
     *
     * @param string $key Application key to remove.
     *
     * @return self
     */
    public function unregister(string $key): self
    {
        $key = $this->normalizeKey($key);
        unset($this->applications[$key], $this->registrationOrder[$key]);

        return $this;
    }

    /**
     * Check whether an application is registered.
     *
     * @param string $key Application key.
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->applications[$this->normalizeKey($key)]);
    }

    /**
     * Get options for a registered application.
     *
     * @param string $key Application key.
     *
     * @return array<string, mixed>|null
     */
    public function get(string $key): ?array
    {
        $key = $this->normalizeKey($key);

        return $this->applications[$key] ?? null;
    }

    /**
     * Get a single option for a registered application.
     *
     * @param string $key    Application key.
     * @param string $option Option key (e.g. order, name, description).
     * @param mixed $default Value when key or option is missing.
     *
     * @return mixed
     */
    public function getOption(string $key, string $option, mixed $default = null): mixed
    {
        return Arr::get($this->applications[$this->normalizeKey($key)] ?? [], $option, $default);
    }

    /**
     * Get all registered applications (key => options), sorted by order then registration order.
     *
     * @return array<string, array<string, mixed>>
     */
    public function all(): array
    {
        $keys = $this->values();

        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->applications[$key];
        }

        return $result;
    }

    /**
     * Get all applications with name and description resolved via trans() for output.
     *
     * @param string|null $locale Locale for trans; null = app locale.
     *
     * @return array<int, array{key: string, name: string, description: string, order: int, options: array<string,
     *                    mixed>}>
     */
    public function allTranslated(?string $locale = null): array
    {
        $keys = $this->values();
        $result = [];

        foreach ($keys as $key) {
            $opts = $this->applications[$key];
            $nameKey = $opts['name'] ?? $key;
            $descKey = $opts['description'] ?? '';

            $result[] = [
                'key'         => $key,
                'name'        => $locale ? trans($nameKey, [], $locale) : trans($nameKey),
                'description' => $descKey !== '' ? ($locale ? trans($descKey, [], $locale) : trans($descKey)) : '',
                'order'       => (int) ($opts['order'] ?? 0),
                'options'     => $opts,
            ];
        }

        return $result;
    }

    /**
     * Get application keys in order (by order option, then by registration order).
     *
     * @return array<int, string>
     */
    public function values(): array
    {
        $keys = array_keys($this->applications);

        usort($keys, function (string $a, string $b) {
            $orderA = (int) ($this->applications[$a]['order'] ?? $this->registrationOrder[$a] ?? 0);
            $orderB = (int) ($this->applications[$b]['order'] ?? $this->registrationOrder[$b] ?? 0);

            if ($orderA !== $orderB) {
                return $orderA <=> $orderB;
            }

            return ($this->registrationOrder[$a] ?? 0) <=> ($this->registrationOrder[$b] ?? 0);
        });

        return array_values($keys);
    }

    /**
     * Remove all registered applications.
     *
     * @return self
     */
    public function clear(): self
    {
        $this->applications = [];
        $this->registrationOrder = [];
        $this->nextOrder = 0;

        return $this;
    }

    /**
     * Normalize application key (lowercase, trimmed).
     *
     * @param string $key
     *
     * @return string
     */
    protected function normalizeKey(string $key): string
    {
        return strtolower(trim($key));
    }

    /**
     * Validate key is English only (a-z, 0-9, underscore).
     *
     * @param string $key
     *
     * @return bool
     */
    protected function isValidKey(string $key): bool
    {
        return $key !== '' && preg_match('/^[a-z0-9_]+$/', $key) === 1;
    }
}
