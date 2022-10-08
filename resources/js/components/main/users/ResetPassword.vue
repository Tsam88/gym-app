<template>
    <div>
        <div class="mb-3">
            <h1 class="h1 d-inline align-middle">Reset Password</h1>
        </div>

        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="submitForm">
                            <b-input-group>
                                <b-input-group-prepend is-text>
                                    <font-awesome-icon icon='fa-solid fa-envelope' class="m-auto"/>
                                </b-input-group-prepend>
                                <b-form-input v-model="form.email" id="email" name="email" type="email" class="wave-input mb-3" placeholder="Email" disabled required></b-form-input>
                            </b-input-group>

                            <b-input-group>
                                <b-input-group-prepend class="text-center" is-text>
                                    <font-awesome-icon icon='fa-solid fa-lock' class="m-auto"/>
                                </b-input-group-prepend>
                                <b-form-input v-model="form.password" id="password" name="password" type="password" class="wave-input mb-3" placeholder="Password" minlength="8" required></b-form-input>
                                <b-form-input v-model="confirm_password" id="confirm_password" name="confirm_password" type="password" class="wave-input mb-3" placeholder="Confirm password" minlength="8" required></b-form-input>
                            </b-input-group>

                            <b-button class="button-color-wave" type="submit" variant="primary" :disabled="disabled">Reset password</b-button>
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
                form: {
                    token: this.$route.query.token,
                    email: this.$route.query.email,
                    password: null,
                },
                confirm_password: null,
                disabled: false,
            }
        },
        methods:{
            submitForm() {
                // validate password fields
                if (this.form.password !== this.confirm_password) {
                    // display error message
                    this.$alertHandler.showAlert('Passwords do not match', 422);
                    return;
                }

                this.disabled = true;

                axios.post('/users/reset-password', this.form)
                    .then((result) => {

                        // display success message
                        this.$alertHandler.showAlert('Your password has been reset', result.status);
                        this.$router.push({ name: 'Login' })
                    })
                    .catch((error) => {
                        this.disabled = false;

                        // display error message
                        this.$alertHandler.showAlert(error.response.data.message || error.message, error.response.status);
                    }).finally(() => {
                    //Perform action in always
                });
            },
        }
    }
</script>
