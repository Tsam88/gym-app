<template>
    <div>
        <div class="mb-4 text-center">
            <h1 class="h1 d-inline align-middle color-wave">Email</h1>
        </div>

        <p>Email verification completed successfully</p>
    </div>
</template>

<script>
    export default {
        mounted() {
            axios.get('/users/profile')
                .then(({data}) => {
                    if (this.auth.isAuthorized()) {
                        this.auth.user.email_verified_at = data.email_verified_at;
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
