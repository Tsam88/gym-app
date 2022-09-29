<template>

    <div>
        <div class="mb-3">
            <h1 class="h1 d-inline align-middle">Sign in</h1>
        </div>

        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-body">

                        <form @submit.prevent="login">
                            <b-form-group label="Email" label-for="email">
                                <b-form-input v-model="email" id="email" name="email" type="email" class="mb-3" placeholder="Email" required></b-form-input>
                            </b-form-group>

                            <b-form-group label="Password" label-for="password">
                                <b-form-input v-model="password" id="password" name="password" type="password" class="mb-3" placeholder="Password" required></b-form-input>
                            </b-form-group>

                            <b-button class="button-color-wave" type="submit" variant="primary">Submit</b-button>
                        </form>

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

                axios.post('/login', data)
                    .then(({data}) => {
                        console.log(data.user);

                        this.auth.login(data.token, data.user);
                        this.$router.push({ name: 'AdminHome' })
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
            logout() {
                if (this.auth.isAuthorized()) {
                    axios.post('/users/logout')
                        .then(({data}) => {
                            this.auth.logout();
                            this.$router.push({ name: 'Home' })
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                }
            },
        }
    }
</script>
