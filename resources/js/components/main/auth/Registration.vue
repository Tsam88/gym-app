<template>

    <div>
        <div class="mb-3">
            <h1 class="h1 d-inline align-middle">Sign up</h1>
        </div>

        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="submitForm">
                            <b-input-group class="mb-3">
                                <b-input-group-prepend is-text>
                                    <b-icon icon="person" scale="1"></b-icon>
                                </b-input-group-prepend>
                                <b-form-input v-model="form.name" id="name" name="name" type="text" class="register-input" placeholder="First name" required></b-form-input>
                                <b-form-input v-model="form.surname" id="surname" name="surname" type="text" class="register-input" placeholder="Last name" required></b-form-input>
                            </b-input-group>

                            <b-input-group class="mb-3">
                                <b-input-group-prepend is-text>
                                    <b-icon icon="telephone" scale="1"></b-icon>
                                </b-input-group-prepend>
                                <b-form-input v-model="form.phone_number" id="phone_number" name="phone_number" type="tel" class="register-input" placeholder="Phone number (10-digit number)" pattern="[0-9]{10}"></b-form-input>
                            </b-input-group>

                            <b-input-group class="mb-3">
                                <b-input-group-prepend is-text>
                                    <b-icon icon="envelope" scale="1"></b-icon>
                                </b-input-group-prepend>
                                <b-form-input v-model="form.email" id="email" name="email" type="email" class="register-input" placeholder="Email" required></b-form-input>
                            </b-input-group>

                            <b-input-group class="mb-3">
                                <b-input-group-prepend is-text>
                                    <b-icon icon="lock" scale="1"></b-icon>
                                </b-input-group-prepend>
                                <b-form-input v-model="form.password" id="password" name="password" type="password" class="register-input" placeholder="Password" required></b-form-input>
                            </b-input-group>

                            <b-button class="button-color-wave" type="submit" variant="primary">Sign up</b-button>
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
                    name: null,
                    surname: null,
                    email: null,
                    password: null,
                    phone_number: null,
                }
            }
        },
        methods:{
            submitForm() {
                axios.post('/users/register', this.form)
                    .then((result) => {
                        this.auth.login(result.data.token, result.data.user);

                        // display success message
                        // this.$alertHandler.showAlert('Registration created successfully', result.status);
                        this.$alertHandler.showAlert('Registration created successfully. \nIn order to book a class, you need to verify your email address first', 451);
                        // this.$alertHandler.showAlert('In order to book a class, you need to verify your email address first', 451);
                        // this.$alertHandler.showAlert('In order to access calendar, you need to verify your email address first', 451);
                        this.$router.push({ name: 'Home' })
                    })
                    .catch((error) => {
                        // display error message
                        this.$alertHandler.showAlert(error.response.data.message || error.message, error.response.status);
                    }).finally(() => {
                    //Perform action in always
                    });
            },
        }
    }
</script>
