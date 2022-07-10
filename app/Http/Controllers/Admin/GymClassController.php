<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\GymClass\GymClassSingle;
use App\Models\GymClass;
use App\Services\GymClassService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GymClassController extends Controller
{
    /**
     * @var GymClassService
     */
    private $gymClassService;

    public function __construct(GymClassService $gymClassService)
    {
        $this->gymClassService = $gymClassService;
    }

    /**
     * Display gym classes.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $data = $request->query();

        $gymClasses = $this->gymClassService->getGymClasses($data);

        $response = new Response($gymClasses, Response::HTTP_OK);

        return $response;
    }

    /**
     * Display single gym class.
     *
     * @param GymClass $gymClass
     *
     * @return Response
     */
    public function show(GymClass $gymClass)
    {
        $response = new Response(new GymClassSingle($gymClass), Response::HTTP_OK);

        return $response;
    }

    /**
     * Create gym class.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        // get the payload
        $data = $request->post();

        // create gym class
        $gymClass = $this->gymClassService->create($data);

        $response = new Response(null, Response::HTTP_CREATED);
        $response->headers->set('Location', route('admin.gym-classes.show', ['gymClass' => $gymClass]));

        return $response;
    }

    /**
     * Update gym class
     *
     * @param Request $request
     * @param GymClass $gymClass
     *
     * @return Response
     */
    public function update(Request $request, GymClass $gymClass)
    {
        // get the payload
        $data = $request->post();

        // if data is empty nothing to update
        if (empty($data)) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        // update gym class
        $this->gymClassService->update($data, $gymClass);

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
