<template>

    <div>
<!--        <img :src="'/images/barengage-logo.jpg'" alt="jacket4">-->
        <font-awesome-icon v-if="user" icon='fa-regular fa-user' size="lg"/>
        <b-dropdown id="dropdown-form" right :text="user ? user.name : 'Sign in'" ref="dropdown" class="dropdown-login px-1">
            <b-dropdown-form v-if="!user" @submit.stop.prevent>
                <b-form-group label="Email" label-for="dropdown-form-email" class="mb-2">
                    <b-form-input v-model="dropDownEmail" id="dropDownEmail" name="dropDownEmail" type="email" size="sm" placeholder="Email" ></b-form-input>
                </b-form-group>

                <b-form-group label="Password" label-for="dropdown-form-password">
                    <b-form-input v-model="dropDownPassword" id="dropDownPassword" name="dropDownPassword" type="password" size="sm" placeholder="Password"></b-form-input>
                </b-form-group>

                <b-form-checkbox class="mb-3">Remember me</b-form-checkbox>
                <b-button class="button-color-wave" size="sm" @click="login">Sign In</b-button>
            </b-dropdown-form>

            <b-dropdown-divider></b-dropdown-divider>

            <div v-if="user">
                <b-dropdown-item-button @click="logout">Sign out</b-dropdown-item-button>
            </div>
            <div v-else>
                <router-link to="/sign-up">
                    <b-dropdown-item-button>New around here? Sign up</b-dropdown-item-button>
                </router-link>
                <router-link to="/forgot-password">
                    <b-dropdown-item-button>Forgot Password?</b-dropdown-item-button>
                </router-link>
            </div>
        </b-dropdown>
    </div>

</template>

<script>
    export default {
        data() {
            return {
                dropDownEmail: '',
                dropDownPassword: '',
                user: this.auth.user,
            };
        },
        methods: {
            login() {
                let data = {
                    email: this.dropDownEmail,
                    password: this.dropDownPassword
                };

                axios.post('/users/login', data)
                    .then(({data}) => {
                        this.auth.login(data.token, data.user);
                        this.user = this.auth.user;
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
                            this.user = this.auth.user;
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
