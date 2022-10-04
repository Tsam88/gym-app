<template>
    <div>
        <div class="mb-3">
            <h1 class="h1 d-inline align-middle">Forgot Password</h1>
        </div>

        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="submitForm">
                            <b-input-group class="mb-3">
                                <b-input-group-prepend is-text>
                                    <b-icon icon="envelope" scale="1"></b-icon>
                                </b-input-group-prepend>
                                <b-form-input v-model="form.email" id="email" name="email" type="email" class="register-input" placeholder="Email" required></b-form-input>
                            </b-input-group>

                            <div class="row m-auto">
                                <b-button class="button-color-wave" type="submit" variant="primary" :disabled="disabled">Send password reset link</b-button>
                            </div>
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
                    email: null,
                },
                disabled: false,
            }
        },
        methods:{
            submitForm() {
                this.disabled = true;

                axios.post('/users/forgot-password', this.form)
                    .then((result) => {
                        // display success message
                        this.$alertHandler.showAlert('We have emailed your password reset link', result.status);
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
