<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class UserRegisterEvent
{
    use Dispatchable, SerializesModels;

    /**
     * User.
     *
     * @var User
     */
    private $user;

    /**
     * User password.
     *
     * @var string
     */
    private $password;

    /**
     * Create a new event instance.
     *
     * @param User        $user
     * @param string|null $password
     *
     * @return void
     */
    public function __construct(User $user, string $password = null)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Get password
     *
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }
}
