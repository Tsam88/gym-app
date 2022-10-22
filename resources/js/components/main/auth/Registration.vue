<template>

    <div class="wave-content-padding-y">
        <div class="col-12 col-sm-9 col-md-7 col-lg-5 col-xxl-4 m-auto p-4">
            <div class="mb-3 text-center">
                <h1 class="h1 d-inline align-middle color-wave">Sign up</h1>
            </div>

            <div class="card">
                <div class="card-body">

                    <form @submit.prevent="submitForm">
                        <b-input-group>
                            <b-input-group-prepend class="text-center" is-text>
                                <font-awesome-icon icon='fa-solid fa-user' class="m-auto"/>
                            </b-input-group-prepend>
                            <b-form-input v-model="form.name" id="name" name="name" type="text" class="wave-input mb-3" placeholder="First name" required></b-form-input>
                            <b-form-input v-model="form.surname" id="surname" name="surname" type="text" class="wave-input mb-3" placeholder="Last name" required></b-form-input>
                        </b-input-group>

                        <b-input-group>
                            <b-input-group-prepend class="text-center" is-text>
                                <font-awesome-icon icon='fa-solid fa-phone' class="m-auto"/>
                            </b-input-group-prepend>
                            <b-form-input v-model="form.phone_number" id="phone_number" name="phone_number" type="tel" class="wave-input mb-3" placeholder="Phone number (10-digit number)" pattern="[0-9]{10}"></b-form-input>
                        </b-input-group>

                        <b-input-group>
                            <b-input-group-prepend is-text>
                                <font-awesome-icon icon='fa-solid fa-envelope' class="m-auto"/>
                            </b-input-group-prepend>
                            <b-form-input v-model="form.email" id="email" name="email" type="email" class="wave-input mb-3" placeholder="Email" required></b-form-input>
                        </b-input-group>

                        <b-input-group>
                            <b-input-group-prepend class="text-center" is-text>
                                <font-awesome-icon icon='fa-solid fa-lock' class="m-auto"/>
                            </b-input-group-prepend>
                            <b-form-input v-model="form.password" id="password" name="password" type="password" class="wave-input mb-3" placeholder="Password" minlength="8" required></b-form-input>
                            <b-form-input v-model="confirm_password" id="confirm_password" name="confirm_password" type="password" class="wave-input mb-3" placeholder="Confirm password" minlength="8" required></b-form-input>
                        </b-input-group>

                        <div class="row m-auto">
                            <b-button class="button-color-wave mt-2" type="submit" variant="primary" :disabled="disabled">Sign up</b-button>
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
                    name: null,
                    surname: null,
                    email: null,
                    password: null,
                    phone_number: null,
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

                axios.post('/users/register', this.form)
                    .then((result) => {
                        this.auth.login(result.data.token, result.data.user);

                        // display success message
                        // this.$alertHandler.showAlert('Registration created successfully', result.status);
                        this.$alertHandler.showAlert('Registration created successfully. \nIn order to book a class, you need to verify your email address first', 451);
                        // this.$alertHandler.showAlert('In order to book a class, you need to verify your email address first', 451);
                        // this.$alertHandler.showAlert('In order to access calendar, you need to verify your email address first', 451);
                        this.$router.push({ name: 'HomeContent' })
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
