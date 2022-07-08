<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
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
     * Register new user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function register(Request $request)
    {
        // get the payload
        $data = $request->post();

        // register user
        $this->userService->register($data);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Login user (get token).
     *
     * @param Request $request
     *
     * @return Response
     */
    public function login(Request $request)
    {
        $data = $request->post();

        $token = $this->userService->login($data);

        return new Response($token, Response::HTTP_OK);
    }

    /**
     * Logout user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function logout(Request $request)
    {
        // get user
        $user = $request->user();

        try {
            $user->currentAccessToken()->delete();
        } catch (\Exception $e) {
            // do nothing
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Verify user (get token).
     *
     * @param Request $request
     *
     * @return Response
     */
    public function verify(Request $request, $id)
    {
        $this->userService->verify($id);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Send a reset link to the given user.
     *
     * @param Request        $request
     *
     * @return Response
     */
    public function sendResetPasswordLinkEmail(Request $request)
    {
        $data = $request->post();

        $this->userService->sendResetPasswordLinkEmail($data);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Reset the given user's password.
     *
     * @param ResetPassword  $request
     *
     * @return Response
     */
    public function resetPassword(Request $request)
    {
        $data = $request->post();

        $this->userService->resetPassword($data);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
