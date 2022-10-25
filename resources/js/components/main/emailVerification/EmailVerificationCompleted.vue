<template>

    <div class="wave-content-padding-y">
        <div class="col-12 col-sm-7 col-md-6 col-lg-4 col-xxl-3 m-auto p-4">
            <div class="mb-4 text-center">
                <h1 class="h1 d-inline align-middle color-wave">Email verified</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <p>Email verification completed successfully. Now you are ready to book a class!</p>

                    <a href="/student-calendar">
                        <div class="row m-auto">
                                <b-button class="button-color-wave" type="button" variant="primary">Book a class</b-button>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        mounted() {
            axios.get('/users/profile')
                .then(({data}) => {
                    if (this.auth.isAuthorized()) {
                        this.auth.verifyEmail(data.email_verified_at);
                    } else {
                        this.$router.push({ name: 'Login' })
                    }
                })
                .catch((error) => {
                    console.log(error);
                }).finally(() => {
                //Perform action in always
            });
        },
    }
</script>
