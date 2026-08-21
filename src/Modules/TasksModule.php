<?php

namespace Javaabu\DhivehiGpt\Modules;

/**
 * Browse available tasks and calculate their credit cost.
 */
class TasksModule extends Module
{
    /**
     * List the available tasks.
     *
     * Supported $params keys: search, per_page, page, sort, platform.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function list(array $params = [], ?float $timeout = null): array
    {
        return $this->client->get('tasks', $params, $timeout);
    }

    /**
     * Get a single available task identified by its slug.
     *
     * @return array<string, mixed>
     */
    public function get(string $slug, ?float $timeout = null): array
    {
        return $this->client->get("tasks/{$slug}", [], $timeout);
    }

    /**
     * Calculate the credits charged for a number of units of an available task.
     *
     * $body can be used to pass additional request body fields not yet covered by
     * this method's arguments; task/units always take precedence over $body.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function calculate(string $task, int $units, array $body = [], ?float $timeout = null): array
    {
        return $this->client->post('tasks/calculate', array_merge($body, [
            'task' => $task,
            'units' => $units,
        ]), $timeout);
    }
}
