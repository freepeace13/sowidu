<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PasswordResetsRepository
{
    protected $table;

    protected $expires;

    public function __construct($table, $expires = 3)
    {
        $this->table = $table;
        $this->expires = $expires;
    }

    public function create(string $recipient)
    {
        $this->deleteExisting($recipient);

        $token = Str::random(40);

        $this->getTable()->insert([
            'recipient' => $recipient,
            'token' => $token,
            'created_at' => new Carbon,
        ]);

        return $token;
    }

    public function exists($recipient)
    {
        $record = $this->getTable()
            ->where('recipient', $recipient)
            ->first();

        return $record && !$this->isTokenExpired($record->created_at);
    }

    public function deleteExpired()
    {
        $expiredAt = Carbon::now()->subMinutes($this->expires);

        $this->getTable()->where('created_at', '<', $expiredAt)->delete();
    }

    public function findByToken(string $token)
    {
        return $this->getTable()
            ->where('token', $token)
            ->first();
    }

    public function hasValidToken(string $token)
    {
        if (!$record = $this->findByToken($token)) {
            return false;
        }

        return !$this->isTokenExpired($record->created_at);
    }

    public function deleteByToken($token)
    {
        return $this->getTable()
            ->where('token', $token)
            ->delete();
    }

    protected function deleteExisting($recipient)
    {
        return $this->getTable()->where('recipient', $recipient)->delete();
    }

    protected function isTokenExpired($createdAt)
    {
        return Carbon::parse($createdAt)->addMinutes($this->expires)->isPast();
    }

    /**
     * Begin a new database query against the table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getTable()
    {
        return DB::table($this->table);
    }
}
