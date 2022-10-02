<?php

declare(strict_types = 1);

namespace App\Services;

use App\Exceptions\DeleteUserHasParcelsException;
use App\Exceptions\InternalServerErrorException;
use App\Exceptions\NotActiveUserException;
use App\Exceptions\NotFoundException;
use App\Exceptions\NotVerifiedUserException;
use App\Libraries\Constants\LocationConstants;
use App\Models\Language;
use App\Models\User;
use App\Models\UserSetting;
use App\Models\UserType;
use App\Rules\CheckPassword;
use App\Validators\UserValidation;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UserService
{
    /**
     * The default number of items per page
     *
     * @var int
     */
    private const DEFAULT_ITEMS_PER_PAGE = 0;

    /**
     * @var UserValidation
     */
    private $userValidation;

    public function __construct(UserValidation $userValidation)
    {
        $this->userValidation = $userValidation;
    }

    /**
     * Return all users.
     *
     * @param array $input
     * @param array $loadModels Relations that should be included in response
     *
     * @return LengthAwarePaginator
     */
    public function getUsers(array $input, array $loadModels = []): LengthAwarePaginator
    {
        // data validation
        $data = $this->userValidation->userGetUsers($input);

        $itemsPerPage = $data['items_per_page'] ?? self::DEFAULT_ITEMS_PER_PAGE;
        $users = User::orderBy('name', 'ASC')
            ->orderBy('surname', 'ASC')
            ->paginate($itemsPerPage);

        $users->load($loadModels);

        return $users;
    }

    /**
     * Register a user.
     *
     * @param array $input User data
     *
     * @return array
     */
    public function register(array $input): array
    {
        // we need to lowercase email if exists, to validate uniqueness later
        if (!empty($input['email'])) {
            $input['email'] = \mb_convert_case(\trim($input['email']), MB_CASE_LOWER, 'UTF-8');
        }

        // data validation
        $data = $this->userValidation->userRegister($input);

        // start db transaction
        DB::beginTransaction();

        try {
            $data['password'] = bcrypt($data['password']);

            $user = User::create($data);

            event(new Registered($user));

            auth()->login($user);

            $token = $user->createToken('API Token')->accessToken;
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();

//        UserRegisterEvent::dispatch($user);

        return [
            'token' => $token,
            'user' => $user
        ];
    }

//    /**
//     * Register a user by Admin.
//     *
//     * @param array $input User data
//     *
//     * @return User
//     */
//    public function registerUser(array $input): User
//    {
//        $userTypesIdentifiers = $this->getModelIdentifiers(new UserType());
//        $languageIdentifiers = $this->getModelIdentifiers(new Language(), null, null, 'locale');
//
//        // we need to lowercase email if exists, to validate uniqueness later
//        if (!empty($input['email'])) {
//            $input['email'] = \mb_convert_case(\trim($input['email']), MB_CASE_LOWER, 'UTF-8');
//        }
//
//        // build the rules for registering
//        $validationRules = [
//            'name' => $this->getRule(User::VALIDATION_RULES, 'name', []),
//            'surname' => $this->getRule(User::VALIDATION_RULES, 'surname', []),
//            'email' => $this->getRule(User::VALIDATION_RULES, 'email', ['unique:users']),
//            'user_types' => $this->getRule(User::VALIDATION_RULES, 'user_types', []),
//            'user_types.*' => $this->getRule(User::VALIDATION_RULES, 'user_types.*', [Rule::in($userTypesIdentifiers)]),
//            'language' => $this->getRule(User::VALIDATION_RULES, 'language', [Rule::in($languageIdentifiers)]),
//        ];
//
//        $validator = $this->getValidator($input, $validationRules);
//        $data = $validator->validate();
//
//        // start db transaction
//        DB::beginTransaction();
//
//        try {
//            // set default timezone
//            $tz = LocationConstants::DEFAULT_TIMEZONE;
//
//            // get language from payload and remove it.
//            $locale = $data['language'] ?? App::getLocale();
//            unset($data['language']);
//
//            // random password
//            $data['password'] = Str::random(8);
//
//            $user = new User($data);
//            $user->email = $data['email'];
//            $user->password = $data['password'];
//
//            $user->save();
//
//            $userTypeIds = $input['user_types'] ?? [];
//            $user->userTypes()->attach($userTypeIds);
//
//            $userSettings = new UserSetting();
//            $userSettings->timezone = $tz;
//            $language = Language::where('locale', $locale)->first();
//            $userSettings->language()->associate($language);
//
//            $user->userSettings()->save($userSettings);
//            $user->save();
//        } catch (\Exception $e) {
//            // something went wrong, rollback and throw same exception
//            DB::rollBack();
//
//            throw $e;
//        }
//
//        // commit database changes
//        DB::commit();
//
//        UserRegisterEvent::dispatch($user, $data['password']);
//
//        return $user;
//    }

//    /**
//     * Update fully a user.
//     * Caution: Call this function only for admin requests.
//     *
//     * @param array $input User data
//     * @param User  $user
//     *
//     * @return void
//     */
//    public function update(array $input, User $user)
//    {
//        $userTypesIdentifiers = $this->getModelIdentifiers(new UserType());
//        $languageIdentifiers = $this->getModelIdentifiers(new Language(), null, null, 'locale');
//
//        // build the rules for registering
//        $validationRules = [
//            'name' => $this->getRule(User::VALIDATION_RULES, 'name', ['sometimes']),
//            'surname' => $this->getRule(User::VALIDATION_RULES, 'surname', ['sometimes']),
//            'email' => $this->getRule(User::VALIDATION_RULES, 'email', ['sometimes', Rule::unique('users')->ignoreModel($user)]),
//            'telephone' => $this->getRule(User::VALIDATION_RULES, 'telephone'),
//            'address' => $this->getRule(User::VALIDATION_RULES, 'address'),
//            'active' => $this->getRule(User::VALIDATION_RULES, 'active', ['sometimes']),
//            'agency' => $this->getRule(User::VALIDATION_RULES, 'agency', ['sometimes']),
//            'user_types' => $this->getRule(User::VALIDATION_RULES, 'user_types'),
//            'user_types.*' => $this->getRule(User::VALIDATION_RULES, 'user_types.*', [Rule::in($userTypesIdentifiers)]),
//            'language' => $this->getRule(User::VALIDATION_RULES, 'language', [Rule::in($languageIdentifiers)]),
//        ];
//
//        $validator = $this->getValidator($input, $validationRules);
//        $data = $validator->validate();
//
//        // start db transaction
//        DB::beginTransaction();
//
//        try {
//            $user->fill($data);
//
//            // give value to properties that are not mass assignable
//            $user->active = $data['active'] ?? $user->active;
//            $user->agency = $data['agency'] ?? $user->agency;
//
//            // user type
//            $this->setUserTypes($data, $user);
//
//            // language
//            $this->setLanguage($data, $user);
//
//            $user->save();
//
//            // in case of email change
//            if (!empty($data['email']) && $data['email'] !== $user->email) {
//                $user->email = $data['email'];
//                $user->email_verified_at = null;
//                $user->save();
//
//                UserRegisterEvent::dispatch($user, null);
//            }
//        } catch (\Exception $e) {
//            // something went wrong, rollback and throw same exception
//            DB::rollBack();
//
//            throw $e;
//        }
//
//        // commit database changes
//        DB::commit();
//    }

    /**
     * Update a user profile.
     *
     * @param array $input User data
     * @param User  $user
     *
     * @return void
     */
    public function updateProfile(array $input, User $user)
    {
        // we need to lowercase email if exists, to validate uniqueness later
        if (!empty($input['email'])) {
            $input['email'] = \mb_convert_case(\trim($input['email']), MB_CASE_LOWER, 'UTF-8');
        }

        // data validation
        $data = $this->userValidation->userUpdate($input);

//        // in case of email change
//        if (!empty($data['email']) && $data['email'] !== $user->email) {
//            $user->email = $data['email'];
//            $user->email_verified_at = null;
//            $user->save();
//
//            UserRegisterEvent::dispatch($user, null);
//        }

        // start db transaction
        DB::beginTransaction();

        try {
            $user->fill($data);
            $user->save();
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();
    }

    /**
     * Login user to app.
     * Return an array that contains an authorization token.
     *
     * @param array $input
     *
     * @return array
     */
    public function login(array $input)
    {
        // data validation
        $data = $this->userValidation->userLogin($input);

        $data['email'] = \mb_convert_case($input['email'], MB_CASE_LOWER, 'UTF-8');

        $user = User::where('email', $input['email'])->first();

        if (null == $user || !Hash::check($input['password'], $user->password)) {
            throw new AuthenticationException();
        }

//        if (!$user->hasVerifiedEmail()) {
//            // delete all tokens
//            $user->tokens()->delete();
//            throw new NotVerifiedUserException();
//        }

//        if (!$user->active) {
//            // delete all tokens
//            // we could revoke tokens here instead, but no need to over complicate it
//            $user->tokens()->delete();
//            throw new NotActiveUserException();
//        }

//        $token = $user->createToken('token')->accessToken;
        $token = $user->createToken('API Token')->accessToken;

        return [
            'token' => $token,
            'user' => $user
        ];
    }

    /**
     * Delete user.
     *
     * @param User $user
     *
     * @return void
     */
    public function delete(User $user)
    {
        // start db transaction
        DB::beginTransaction();

        try {
            if (0 < $user->parcels->count()) {
                throw new DeleteUserHasParcelsException();
            }

            $user->delete();
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function verifyEmail(Request $request)
    {
        // if user does not exist return 401 instead of 404
        // don't let anyone to guess if user does not exist or signature is invalid.
        $user = User::find($request->route('id'));
        if (null === $user) {
            throw new AuthenticationException();
        }

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($user->hasVerifiedEmail()) {
            return $user->email_verified_at;
        }

        $user->markEmailAsVerified();

        return $user->email_verified_at;
    }

    /**
     * Update a user password.
     *
     * @param array $input password
     * @param User  $user
     *
     * @return void
     */
    public function updatePassword(array $input, User $user)
    {
        // build the rules for registering
        $validationRules = [
            'old_password' => $this->getRule(User::VALIDATION_RULES, 'password', ['required', new CheckPassword($user)]),
            'password' => $this->getRule(User::VALIDATION_RULES, 'password', []),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        // start db transaction
        DB::beginTransaction();

        try {
            $user->password = $data['password'];
            $user->save();

            // delete all tokens
            $user->tokens()->delete();
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();
    }

    /**
     * Send reset password link on a user.
     *
     * @param array $input email
     *
     * @return void
     */
    public function sendResetPasswordLinkEmail(array $input)
    {
        // build the rules for registering
        $validationRules = [
            'email' => $this->getRule(User::VALIDATION_RULES, 'email', []),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        // start db transaction
        DB::beginTransaction();

        try {
            $status = Password::sendResetLink(
                ['email' => $data['email']]
            );

            switch ($status) {
                case Password::RESET_LINK_SENT:
                    break;
                case Password::INVALID_USER:
                    throw new NotFoundException($status);
                default:
                    throw new InternalServerErrorException();
            }
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();
    }

    /**
     * Reset email on a user.
     *
     * @param array $input
     *
     * @return void
     */
    public function resetPassword(array $input)
    {
        // build the rules for registering
        $validationRules = [
            'token' => $this->getRule(User::VALIDATION_RULES, 'token', ['required']),
            'email' => $this->getRule(User::VALIDATION_RULES, 'email', []),
            'password' => $this->getRule(User::VALIDATION_RULES, 'password', []),
        ];

        $validator = $this->getValidator($input, $validationRules);
        $data = $validator->validate();

        // start db transaction
        DB::beginTransaction();

        try {
            $status = Password::reset(
                ['email' => $data['email'], 'password' => $data['password'], 'token' => $data['token']],
                function ($user, $password) {
                    $user->password = $password;
                    $user->tokens()->delete();

                    $user->save();
                }
            );

            switch ($status) {
                case Password::PASSWORD_RESET:
                    break;
                case Password::INVALID_USER:
                case Password::INVALID_TOKEN:
                    throw new NotFoundException($status);
                default:
                    throw new InternalServerErrorException();
            }
        } catch (\Exception $e) {
            // something went wrong, rollback and throw same exception
            DB::rollBack();

            throw $e;
        }

        // commit database changes
        DB::commit();
    }
}
