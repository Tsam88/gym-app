<?php

namespace App\Http\Controllers;

use App\Http\Resources\User\UserProfileSingle;
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

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
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
    public function password(Request $request)
    {
        // get the payload
        $data = $request->post();

        // if data is empty nothing to update
        if (empty($data)) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        // get user
        $user = $request->user();

        $this->userService->updatePassword($data, $user);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Get user's permissions
     *
     * @param Request $request
     *
     * @return Response
     */
    public function permissions(Request $request)
    {
        /** @todo Keep it here until client is fixed */
        return new Response([], Response::HTTP_OK);
    }
}
