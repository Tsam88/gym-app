<template>
    <div>
        <div class="mb-3">
            <h1 class="h1 d-inline align-middle">Email verification is required</h1>
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
                        // error.response.status Check status code
                        // for each errors -> display
                        console.log(error);
                        // console.log(error.response.data.errors['name'][0]);
                    }).finally(() => {
                    //Perform action in always
                });
            }
        }
    }
</script>
