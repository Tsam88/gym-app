<template>

    <div class="wave-content-padding-y">
        <div class="col-12 col-sm-7 col-md-6 col-lg-4 col-xxl-3 m-auto p-4">
            <div class="mb-4 text-center">
                <h1 class="h1 d-inline align-middle color-wave">Email verification required</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <p>In order to book a class, you need to verify your email address first. Please check your email inbox.</p>
                    <p>If you did not receive a verification email, you can resend it by pressing the button below.</p>

                    <div class="row m-auto">
                        <b-button @click="resendVerificationEmail()" class="button-color-wave" type="button" variant="primary" :disabled="disabled">Resend email</b-button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        data() {
            return {
                disabled: false,
            }
        },
        methods: {
            resendVerificationEmail() {
                this.disabled = true;

                axios.post('/email/resend-verification-email')
                    .then((result) => {
                        this.$alertHandler.showAlert('We emailed you a new verification link', 451);
                    })
                    .catch((error) => {
                        this.disabled = false;
                        console.log(error);
                    }).finally(() => {
                    //Perform action in always
                });
            }
        }
    }
</script>
