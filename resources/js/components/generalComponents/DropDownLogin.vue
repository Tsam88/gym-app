<template>

    <div>
        <b-dropdown id="dropdown-form" class="dropdown-login px-1" right :text="user ? user.name : 'Sign in'" ref="dropdown">
            <div v-if="user">
                <a v-if="auth.isAdmin()" href="/admin">
                    <b-dropdown-item-button class="color-wave">Admin</b-dropdown-item-button>
                </a>
                <a v-if="auth.isAdmin()" href="/">
                    <b-dropdown-item-button class="color-wave">Home</b-dropdown-item-button>
                </a>
                <a href="/profile">
                    <b-dropdown-item-button class="color-wave">Profile</b-dropdown-item-button>
                </a>
                <a href="/change-password">
                    <b-dropdown-item-button class="color-wave">Change password</b-dropdown-item-button>
                </a>
                <a href="/change-email">
                    <b-dropdown-item-button class="color-wave">Change email</b-dropdown-item-button>
                </a>
            </div>
            <div v-else>
                <b-dropdown-form class="px-2" @submit.prevent="login">
                    <b-input-group class="mt-2">
                        <b-input-group-prepend is-text>
                            <font-awesome-icon icon='fa-solid fa-envelope' class="m-auto"/>
                        </b-input-group-prepend>
                        <b-form-input v-model="dropDownEmail" id="dropDownEmail" name="dropDownEmail" type="email" size="sm" class="wave-input-dropdown-login mb-3" placeholder="Email" required></b-form-input>
                    </b-input-group>

                    <b-input-group class="mb-2">
                        <b-input-group-prepend is-text>
                            <font-awesome-icon icon='fa-solid fa-lock' class="m-auto"/>
                        </b-input-group-prepend>
                        <b-form-input v-model="dropDownPassword" id="dropDownPassword" name="dropDownPassword" type="password" size="sm" class="wave-input-dropdown-login mb-3" placeholder="Password" required></b-form-input>
                    </b-input-group>

                    <div class="row m-auto">
                        <b-button class="button-color-wave" type="submit" size="sm" variant="primary">Sign in</b-button>
                    </div>
                </b-dropdown-form>
            </div>

            <b-dropdown-divider></b-dropdown-divider>

            <div v-if="user">
                <b-dropdown-item-button @click="logout">Sign out</b-dropdown-item-button>
            </div>
            <div v-else>
                <a href="/sign-up">
                    <b-dropdown-item-button class="color-wave">New around here? Sign up</b-dropdown-item-button>
                </a>
                <a href="/forgot-password">
                    <b-dropdown-item-button class="secondary-color-wave">Forgot password?</b-dropdown-item-button>
                </a>
            </div>
        </b-dropdown>

        <span class="color-wave">
            <font-awesome-icon v-if="user" icon='fa-regular fa-user'/>
        </span>
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
                            // this.$router.push({ name: 'HomeContent' });
                            // this.$router.replace({ path: '/' })
                            window.location.replace("/");
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                }
            }
        }
    }
</script>
