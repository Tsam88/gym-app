<template>
    <div>
        <div class="mb-4 text-center">
            <h1 class="h1 d-inline align-middle color-wave">Email verification is required</h1>
        </div>

<!--        <p>In order to book a class, you need to verify your email address first</p>-->
        <p>In order to access calendar, you need to verify your email address first</p>

        <b-button @click="resendVerificationEmail()" class="button-color-wave" type="button" variant="primary">Resend email</b-button>
    </div></template>

<script>
    export default {
        methods:{
            resendVerificationEmail() {
                axios.post('/email/resend-verification-email')
                    .then((result) => {
                        this.$alertHandler.showAlert('We emailed you a new verification link', 451);
                    })
                    .catch((error) => {
                        console.log(error);
                    }).finally(() => {
                    //Perform action in always
                });
            }
        }
    }
</script>
