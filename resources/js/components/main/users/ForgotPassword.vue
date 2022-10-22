<template>
    <div class="wave-content-padding-y">
        <div class="col-12 col-sm-7 col-md-6 col-lg-4 col-xxl-3 m-auto p-4">
            <div class="mb-3 text-center">
                <h1 class="h1 d-inline align-middle color-wave">Forgot Password</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <form @submit.prevent="submitForm">
                        <b-input-group>
                            <b-input-group-prepend is-text>
                                <font-awesome-icon icon='fa-solid fa-envelope' class="m-auto"/>
                            </b-input-group-prepend>
                            <b-form-input v-model="form.email" id="email" name="email" type="email" class="wave-input mb-3" placeholder="Email" required></b-form-input>
                        </b-input-group>

                        <div class="row m-auto">
                            <b-button class="button-color-wave mt-2" type="submit" variant="primary" :disabled="disabled">Send password reset link</b-button>
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
