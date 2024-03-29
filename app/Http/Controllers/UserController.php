<?php

namespace App\Http\Controllers;

use App\Http\Resources\User\UserProfileSingle;
use App\Libraries\ReservationSubscriptionHelper;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var ReservationSubscriptionHelper
     */
    private $reservationSubscriptionHelper;

    public function __construct(UserService $userService, ReservationSubscriptionHelper $reservationSubscriptionHelper)
    {
        $this->userService = $userService;
        $this->reservationSubscriptionHelper = $reservationSubscriptionHelper;
    }

    /**
     * Display users.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $data = $request->query();

        $users = $this->userService->getUsers($data);

//        $usersCollection = new AdminUserCollection($users->appends($data));

        $response = new Response($users, Response::HTTP_OK);

        return $response;
    }

    /**
     * Return Authorized user profile
     *
     * @param Request $request
     *
     * @return UserProfileSingle
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        // get user's active subscription if exists
        $user->active_subscription = $this->reservationSubscriptionHelper->getClosestActiveSubscription($user->id) ?? null;

        return new UserProfileSingle($user);
    }

    /**
     * Update Authorized user profile
     *
     * @param Request $request
     *
     * @return Response
     */
    public function update(Request $request)
    {
        // get the payload
        $data = $request->post();

        // if data is empty nothing to update
        if (empty($data)) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        // get user
        $user = $request->user();

        // update user profile
        $this->userService->updateProfile($data, $user);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Delete Authorized user profile
     *
     * @param Request $request
     *
     * @return Response
     */
    public function delete(Request $request)
    {
        $user = $request->user();

        $this->userService->delete($user);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Update Authorized user password
     *
     * @param Request $request
     *
     * @return Response
     */
    public function updatePassword(Request $request)
    {
        // get the payload
        $data = $request->post();

        // if data is empty nothing to update
        if (empty($data)) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        // get user
        $user = $request->user();

        $token = $this->userService->updatePassword($data, $user);

        return new Response($token, Response::HTTP_OK);
    }

    /**
     * Update Authorized user email
     *
     * @param Request $request
     *
     * @return Response
     */
    public function updateEmail(Request $request)
    {
        // get the payload
        $data = $request->post();

        // if data is empty nothing to update
        if (empty($data)) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        // get user
        $user = $request->user();

        $token = $this->userService->updateEmail($data, $user);

        return new Response($token, Response::HTTP_OK);
    }
}
