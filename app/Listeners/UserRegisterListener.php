<?php

declare(strict_types = 1);

namespace App\Listeners;

use App\Events\UserRegisterEvent;
use App\Libraries\Mandrill;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Queue\InteractsWithQueue;

class UserRegisterListener implements ShouldQueue
{
    use InteractsWithQueue;

    private const EMAIL_TEMPLATE = 'agroestate-register-user';
    private const EMAIL_TRANSLATION_PREFIX = 'notification.user_auth.register';

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string
     */
    public $queue = 'user-auth';

    /**
     * The time (seconds) before the job should be processed.
     *
     * @var int
     */
    public $delay = 10;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * Mandrill library.
     *
     * @var Mandrill $mandrill
     */
    private $mandrill;

    /**
     * Url of web app that user can verify email.
     *
     * @var string
     */
    private $webUrl;

    /**
     * Log Channel
     *
     * @var string
     */
    private $logChannel;

    /**
     * @var string
     */
    private $logoImage;

    /**
     * @var string
     */
    private $brandUrl;

    /**
     * Create the event listener.
     *
     * @param Mandrill $mandrill
     *
     * @return void
     */
    public function __construct(Mandrill $mandrill)
    {
        $this->mandrill = $mandrill;
        $this->webUrl = Config::get('auth.verification.user_auth_web_url');
        $this->logChannel = Config::get('logging.queue_default');
        $this->logoImage = Config::get('services.mandrill.logo_image');
        $this->brandUrl = Config::get('services.mandrill.brand_url');
    }

    /**
     * Handle the event.
     *
     * @param UserRegisterEvent $event
     *
     * @return void
     */
    public function handle(UserRegisterEvent $event)
    {
        $user = $event->getUser();
        $password = $event->getPassword();
        Log::channel($this->logChannel)->info("User {$user->id} UserRegisterEvent start...");

        $url = $this->webUrl.$this->verificationUrl($user->id);

        $vars = $this->buildEmailVars($user, $password);
        $subject = $vars['subject'];

        $this->mandrill->setTemplate(static::EMAIL_TEMPLATE);
        $this->mandrill->setRecipient($user->email, $user->name);
        /** @todo use translations */
        $this->mandrill->setSubject($subject);
        $this->mandrill->setVars($vars);
        $this->mandrill->send();

        $user->push();

        Log::channel($this->logChannel)->info("User {$user->id} UserRegisterEvent end...");
    }

    /**
     * Handle failed events
     *
     * @param UserRegisterEvent $event
     * @param mixed $exception
     *
     * @return void
     */
    public function failed(UserRegisterEvent $event, $exception)
    {
        $user = $event->getUser();
        Log::channel($this->logChannel)->critical("User {$user->id} UserRegisterEvent failed: $exception");
    }

    /**
     * Get the verification URL for the given user.
     *
     * @param int $userId
     *
     * @return string
     */
    private function verificationUrl(int $userId): string
    {
        $url = URL::signedRoute(
            'user.verify-email',
            ['id' => $userId],
            null,
            false
        );

        $signature = \str_replace("?signature=", "", \strstr($url, '?signature='));

        $returnedUrl = '/confirm-email?userId=' . $userId . '&signature=' . $signature;

        return $returnedUrl;
    }

    /**
     * Build email vars.
     *
     * @param User        $user
     * @param string|null $password
     *
     * @return array
     */
    private function buildEmailVars(User $user, ?string $password): array
    {
        $locale = $user->preferredLocale();

        $vars = [];
        $vars['subject'] = Lang::get(\sprintf('%s.subject', static::EMAIL_TRANSLATION_PREFIX), [], $locale);
        $vars['title'] = $vars['subject'];
        $vars['salutation'] = Lang::get(\sprintf('%s.salutation', static::EMAIL_TRANSLATION_PREFIX), ['name' => $user->name], $locale);
        $vars['welcome1'] = Lang::get(\sprintf('%s.welcome1', static::EMAIL_TRANSLATION_PREFIX), [], $locale);

        $vars['goodbye'] = Lang::get(\sprintf('%s.goodbye', static::EMAIL_TRANSLATION_PREFIX), [], $locale);
        $vars['login_url'] = $this->webUrl;

        if (null !== $password) {
            $vars['login_info'] = Lang::get(\sprintf('%s.login_info_password', static::EMAIL_TRANSLATION_PREFIX), ['email' => $user->email, 'password' => $password], $locale);
        } else {
            $vars['login_info'] = Lang::get(\sprintf('%s.login_info', static::EMAIL_TRANSLATION_PREFIX), ['email' => $user->email], $locale);
        }

        $vars['confirm_button'] = Lang::get(\sprintf('%s.confirm_button', static::EMAIL_TRANSLATION_PREFIX), [], $locale);
        $vars['confirm_info'] = Lang::get(\sprintf('%s.confirm_info', static::EMAIL_TRANSLATION_PREFIX), ['email' => $user->email, 'password' => $password], $locale);
        $vars['confirm_url'] = $this->webUrl . $this->verificationUrl($user->id);

        $vars['logo_image'] = $this->logoImage;
        $vars['brand_url'] = $this->brandUrl;

        return $vars;
    }
}
