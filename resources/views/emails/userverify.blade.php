@component('mail::message')
    {{-- Greeting --}}
        # Γεια σας,

    {{-- Intro Lines --}}
    Παρακαλούμε επιβεβαιώστε το email σας κάνοντας Click στον παρακάτω κουμπί.

    {{-- Action Button --}}
    @component('mail::button', ['url' => $actionUrl, 'color' => 'green'])
        Επιβεβαίωση Email
    @endcomponent

    {{-- Outro Lines --}}
    Αν δε δημιουργήσατε εσείς αυτό το λογαριασμό, δε χρειάζεται να κάνετε κάποια άλλη ενέργεια.

    {{-- Salutation --}}
    Ευχαριστούμε,
    Wave

    {{-- Subcopy --}}
    @slot('subcopy')
            'Αν αντιμετωπίζετε κάποιο πρόβλημα με το κουμπί "Επιβεβαίωση Email", κάντε αντιγραφή και επικόλλιση τον παρακάτω σύνδεσμο στον web browser σας:' {{$actionUrl}},
    @endslot
@endcomponent
