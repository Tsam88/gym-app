<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
         'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->greeting('Γεια σας,')
                ->subject('Wave: επιβεβαίωση email')
                ->line('Παρακαλούμε επιβεβαιώστε το email σας κάνοντας Click στον παρακάτω κουμπί.')
                ->action('Επιβεβαίωση Email', $url)
                ->line('Αν δε δημιουργήσατε εσείς αυτό το λογαριασμό, δε χρειάζεται να κάνετε κάποια άλλη ενέργεια.')
                ->salutation('Ευχαριστούμε, Wave')
                ->with(['test1', 'test2']);
//                ->outroLines("ΕυχαριστούμεWave")
//                ->action('Αν αντιμετωπίζετε κάποιο πρόβλημα με το κουμπί "Επιβεβαίωση Email", κάντε αντιγραφή και επικόλλιση τον παρακάτω σύνδεσμο στον web browser σας: ', $url);
//                ->lines(['Ευχαριστούμε,', 'Wave']);
//                ->with('Αν αντιμετωπίζετε κάποιο πρόβλημα με το κουμπί "Επιβεβαίωση Email", κάντε αντιγραφή και επικόλλιση τον παρακάτω σύνδεσμο στον web browser σας:');
        });
    }
}
