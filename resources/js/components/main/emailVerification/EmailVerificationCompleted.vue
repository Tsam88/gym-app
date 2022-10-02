<template>
    <div>
        <div class="mb-3">
            <h1 class="h1 d-inline align-middle">Email</h1>
        </div>

        <p>Email verification completed successfully</p>
    </div>
</template>

<script>
    export default {
        mounted() {
            axios.get('/users/profile')
                .then(({data}) => {
                    console.log(data);
                    console.log(data.email_verified_at);
                    if (this.auth.isAuthorized()) {
                        this.auth.user.email_verified_at = data.email_verified_at;
                    } else {
                        this.$router.push({ name: 'Login' })
                    }

                    console.log(this.auth.user);
                })
                .catch((error) => {
                    // error.response.status Check status code
                    // for each errors -> display
                    console.log(error);
                    // console.log(error.response.data.errors['name'][0]);
                }).finally(() => {
                //Perform action in always
            });
        },
    }
</script>
