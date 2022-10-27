<template>
    <div class="wave-content-padding-y">
        <div class="col-12 col-sm-8 col-md-7 col-lg-5 col-xl-4 col-xxl-3 m-auto p-4">
            <div class="mb-4 text-center">
                <h1 class="h1 d-inline align-middle color-wave">Change Password</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <form @submit.prevent="submitForm">
                        <b-input-group>
                            <b-input-group-prepend is-text>
                                <font-awesome-icon icon='fa-solid fa-lock' class="m-auto"/>
                            </b-input-group-prepend>
                            <b-form-input v-model="form.old_password" id="old_password" name="old_password" type="password" class="wave-input mb-3" placeholder="Old password" minlength="8" required></b-form-input>
                        </b-input-group>

                        <b-input-group>
                            <b-input-group-prepend class="text-center" is-text>
                                <font-awesome-icon icon='fa-solid fa-lock' class="m-auto"/>
                            </b-input-group-prepend>
                            <b-form-input v-model="form.password" id="new_password" name="new_password" type="password" class="wave-input mb-3" placeholder="New password" minlength="8" required></b-form-input>
                            <b-form-input v-model="confirm_new_password" id="confirm_new_password" name="confirm_new_password" type="password" class="wave-input mb-3" placeholder="Confirm new password" minlength="8" required></b-form-input>
                        </b-input-group>

                        <div class="row m-auto">
                            <b-button class="button-color-wave mt-2" type="submit" variant="primary" :disabled="disabled">Reset password</b-button>
                        </div>
                    </form>

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
                    old_password: null,
                    password: null,
                },
                confirm_new_password: null,
                disabled: false,
            }
        },
        methods: {
            submitForm() {
                // validate new password fields
                if (this.form.password !== this.confirm_new_password) {
                    // display error message
                    this.$alertHandler.showAlert('New passwords do not match', 422);
                    return;
                }

                this.disabled = true;

                axios.patch('/users/password', this.form)
                    .then((result) => {
                        // display success message
                        this.$alertHandler.showAlert('Your password has been changed successfully', result.status);

                        // we need to login again with the new token
                        this.auth.login(result.data.token, result.data.user);

                        window.location.replace("/");
                        // this.$router.push({ name: 'HomeContent' })
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
