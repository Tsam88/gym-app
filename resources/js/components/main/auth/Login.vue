<template>

    <div>
        <div class="mb-3">
            <h1 class="h1 d-inline align-middle">Login</h1>
        </div>

        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-body">

                        <form v-on:submit.prevent="login">
                            <div class="form-group">

                                <div class="mb-3">
                                    <label for="email">Email</label>
                                    <input v-model="email" id="email" name="email" type="text" class="form-control" placeholder="Email">
                                </div>

                                <div class="my-3">
                                    <label for="password">password</label>
                                    <input v-model="password" id="password" name="password" type="password" class="form-control" placeholder="password">
                                </div>

                                <div class="mt-3">
                                    <input id="login" name="login" class="btn btn-primary button-color-wave" type="submit" value="login">
                                </div>
                                <div class="mt-3">
                                    <input @click="logout" id="logout" name="logout" class="btn btn-primary button-color-wave" type="button" value="logout">
                                </div>

                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    import Auth from '../../../auth.js';

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

                        Auth.login(data.token, data.user);
                        this.$router.push({ name: 'AdminHome' })
                    })
                    .catch((error) => {
                        console.log(error);
                    });
            },
            logout() {
                if (Auth.isAuthorized()) {
                    axios.post('/users/logout')
                        .then(({data}) => {
                            Auth.logout();
                            this.$router.push({ name: 'Home' })
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                }
            }
        }
    }
</script>
