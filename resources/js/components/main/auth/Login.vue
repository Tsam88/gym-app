<template>

    <div class="wave-content-padding-y">
        <div class="col-12 col-sm-7 col-md-6 col-lg-4 col-xxl-3 m-auto p-4">
            <div class="mb-4 text-center">
                <h1 class="h1 d-inline align-middle color-wave">Sign in</h1>
            </div>

            <div class="card">
                <div class="card-body">

                    <form @submit.prevent="login">
                        <b-input-group>
                            <b-input-group-prepend is-text>
                                <font-awesome-icon icon='fa-solid fa-envelope' class="m-auto"/>
                            </b-input-group-prepend>
                            <b-form-input v-model="email" id="email" name="email" type="email" class="wave-input mb-3" placeholder="Email" required></b-form-input>
                        </b-input-group>

                        <b-input-group>
                            <b-input-group-prepend is-text>
                                <font-awesome-icon icon='fa-solid fa-lock' class="m-auto"/>
                            </b-input-group-prepend>
                            <b-form-input v-model="password" id="password" name="password" type="password" class="wave-input mb-3" placeholder="Password" required></b-form-input>
                        </b-input-group>

                        <div class="row m-auto">
                            <b-button class="button-color-wave mt-2" type="submit" variant="primary">Sign in</b-button>
                        </div>
                    </form>

                </div>
            </div>

            <p class="text-center">
                <a href="/sign-up" class="d-inline align-middle color-wave">New around here? Sign up</a>
            </p>

            <p class="text-center">
                <a href="/forgot-password" class="d-inline align-middle color-wave">Forgot password?</a>
            </p>

        </div>
    </div>

</template>

<script>
    export default {
        data() {
            return {
                email: '',
                password: '',
            };
        },
        methods: {
            login() {
                let data = {
                    email: this.email,
                    password: this.password
                };

                axios.post('/users/login', data)
                    .then(({data}) => {
                        this.auth.login(data.token, data.user);

                        if (!this.auth.isAdmin()) {
                            // this.$router.push({ name: 'StudentCalendar' });
                            window.location.replace("/student-calendar");
                        } else {
                            this.$router.push({ name: 'AdminHome' });
                        }

                        // reload page, so the component DropDownLogin will be refreshed
                        // this.$router.go();
                        // location.reload();
                    })
                    .catch((error) => {
                        // display error message
                        if (error.response.status === 401) {
                            this.$alertHandler.showAlert('Email or/and Password are invalid', 422);
                        } else {
                            this.$alertHandler.showAlert(error.response.data.message || error.message, error.response.status);
                        }
                    });
            },
        }
    }
</script>
