<template>

    <div>
        <b-dropdown id="dropdown-form" text="Dropdown with form" ref="dropdown" class="m-2">
            <b-dropdown-form @submit.stop.prevent>
                <b-form-group label="Email" label-for="dropdown-form-email" class="mb-2">
                    <b-form-input v-model="email" id="email" name="email" size="sm" placeholder="Email" ></b-form-input>
                </b-form-group>

                <b-form-group label="Password" label-for="dropdown-form-password">
                    <b-form-input v-model="password" id="password" name="password" type="password" size="sm" placeholder="Password"></b-form-input>
                </b-form-group>

                <b-form-checkbox class="mb-3">Remember me</b-form-checkbox>
                <b-button variant="primary" size="sm" @click="login">Sign In</b-button>
            </b-dropdown-form>
            <b-dropdown-divider></b-dropdown-divider>
            <b-dropdown-item-button>New around here? Sign up</b-dropdown-item-button>
            <b-dropdown-item-button>Forgot Password?</b-dropdown-item-button>
        </b-dropdown>
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
                        console.log(error);
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
            }
        }
    }
</script>
