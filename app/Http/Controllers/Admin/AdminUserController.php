<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserProfileSingle;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Response;

class AdminUserController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display single user.
     *
     * @param User $user
     *
     * @return Response
     */
    public function show(User $user)
    {
        $response = new Response(new UserProfileSingle($user), Response::HTTP_OK);

        return $response;
    }
}
