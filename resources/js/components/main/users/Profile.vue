<template>
    <div class="wave-content-padding-y">
        <div class="col-12 col-sm-9 col-md-7 col-lg-6 col-xl-5 col-xxl-4 m-auto p-4">
            <div class="mb-3 text-center">
                <h1 class="h1 d-inline align-middle color-wave">Profile</h1>
            </div>

            <div class="card">
                <div class="card-body profile-section">

                    <b-row class="pt-2 mb-4">
                        <b-col class="col-5 col-sm-4 col-md-5 col-xl-4 col-xxl-5">
                        </b-col>
                        <b-col class="col-7 col-sm-8 col-md-7 col-xl-8 col-xxl-7">
                            <h4 class="secondary-color-wave">Personal info</h4>
                        </b-col>
                    </b-row>

                    <form @submit.prevent="submitForm" class="profile-form">
                        <b-row class="my-2">
                            <b-col class="col-5 col-sm-4 col-md-5 col-xl-4 col-xxl-5">
                                <label for="name">First name:</label>
                            </b-col>
                            <b-col class="col-7 col-sm-8 col-md-7 col-xl-8 col-xxl-7">
                                <b-form-input v-model="form.name" id="name" name="name" type="text" class="wave-input mb-3" placeholder="First name" required></b-form-input>
                            </b-col>
                        </b-row>

                        <b-row class="my-2">
                            <b-col class="col-5 col-sm-4 col-md-5 col-xl-4 col-xxl-5">
                                <label for="surname">Last name:</label>
                            </b-col>
                            <b-col class="col-7 col-sm-8 col-md-7 col-xl-8 col-xxl-7">
                                <b-form-input v-model="form.surname" id="surname" name="surname" type="text" class="wave-input mb-3" placeholder="Last name" required></b-form-input>
                            </b-col>
                        </b-row>

                        <b-row class="my-2">
                            <b-col class="col-5 col-sm-4 col-md-5 col-xl-4 col-xxl-5">
                                <label for="email">Email:</label>
                            </b-col>
                            <b-col class="col-7 col-sm-8 col-md-7 col-xl-8 col-xxl-7">
                                <b-form-input v-model="form.email" id="email" name="email" type="email" class="wave-input mb-3" placeholder="Email" required></b-form-input>
                            </b-col>
                        </b-row>

                        <b-row class="my-2">
                            <b-col class="col-5 col-sm-4 col-md-5 col-xl-4 col-xxl-5">
                                <label for="phone_number">Phone number:</label>
                            </b-col>
                            <b-col class="col-7 col-sm-8 col-md-7 col-xl-8 col-xxl-7">
                                <b-form-input v-model="form.phone_number" id="phone_number" name="phone_number" type="tel" class="wave-input mb-3" placeholder="Phone number (10-digit number)" pattern="[0-9]{10}"></b-form-input>
                            </b-col>
                        </b-row>

                        <b-row class="mt-2">
                            <b-col class="col-5 col-sm-4 col-md-5 col-xl-4 col-xxl-5">
                            </b-col>
                            <b-col class="col-7 col-sm-8 col-md-7 col-xl-8 col-xxl-7">
                                <div class="m-auto">
                                    <b-button class="button-color-wave mt-2" type="submit" variant="primary" :disabled="disabled">Save</b-button>
                                </div>
                            </b-col>
                        </b-row>
                    </form>

                    <hr class="secondary-color-wave mt-4 mb-3">

                    <b-row class="pt-1 mb-4">
                        <b-col class="col-5 col-sm-4 col-md-5 col-xl-4 col-xxl-5">
                        </b-col>
                        <b-col class="col-7 col-sm-8 col-md-7 col-xl-8 col-xxl-7">
                            <h4 class="secondary-color-wave">Subscription info</h4>
                        </b-col>
                    </b-row>

                    <div class="py-1">
                        <b-row class="mb-3">
                            <b-col class="col-5 col-sm-4 col-md-5 col-xl-4 col-xxl-5">
                                <span>Subscription: </span>
                            </b-col>
                            <b-col class="col-7 col-sm-8 col-md-7 col-xl-8 col-xxl-7">
                                <div v-if="subscription !== null" class="text-white">
                                    <b v-if="subscription.unlimited_sessions === true">
                                        with expiration date
                                    </b>
                                    <b v-else>
                                        with sessions
                                    </b>
                                </div>
                                <div v-else class="profile-subscription-info-no-subscription">
                                    <b>there is no active subscription</b>
                                </div>
                            </b-col>
                        </b-row>

                        <div v-if="subscription !== null">
                            <!-- Subscription with expiring date -->
                            <b-row v-if="subscription.unlimited_sessions === true" class="my-3">
                                <b-col class="col-5 col-sm-4 col-md-5 col-xl-4 col-xxl-5">
                                    <span>Starting date: </span>
                                </b-col>
                                <b-col class="col-7 col-sm-8 col-md-7 col-xl-8 col-xxl-7">
                                    <b class="text-white">
                                        {{displayDate(subscription.starts_at)}}
                                    </b>
                                </b-col>
                            </b-row>

                            <b-row v-if="subscription.unlimited_sessions === true" class="my-3">
                                <b-col class="col-5 col-sm-4 col-md-5 col-xl-4 col-xxl-5">
                                    <span>Expiring date: </span>
                                </b-col>
                                <b-col class="col-7 col-sm-8 col-md-7 col-xl-8 col-xxl-7">
                                    <b class="text-white">
                                        {{displayDate(subscription.expires_at)}}
                                    </b>
                                </b-col>
                            </b-row>

                            <b-row v-if="subscription.unlimited_sessions === true && subscription.sessions_per_week > 0" class="my-3">
                                <b-col class="col-5 col-sm-4 col-md-5 col-xl-4 col-xxl-5">
                                    <span>Sessions per week: </span>
                                </b-col>
                                <b-col class="col-7 col-sm-8 col-md-7 col-xl-8 col-xxl-7">
                                    <b class="text-white">
                                        {{subscription.sessions_per_week}}
                                    </b>
                                </b-col>
                            </b-row>

                            <!-- Subscription with sessions -->
                            <b-row v-if="subscription.unlimited_sessions === false" class="my-3">
                                <b-col class="col-5 col-sm-4 col-md-5 col-xl-4 col-xxl-5">
                                    <span>Sessions left: </span>
                                </b-col>
                                <b-col class="col-7 col-sm-8 col-md-7 col-xl-8 col-xxl-7">
                                    <b class="text-white">
                                        {{subscription.remaining_sessions}}
                                    </b>
                                </b-col>
                            </b-row>

                        </div>
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
                    phone_number: null,
                },
                subscription: null,
                disabled: false,
            }
        },
        mounted() {
            axios.get('/users/profile')
                .then(({data}) => {
                    this.form.name = data.name;
                    this.form.surname = data.surname;
                    this.form.email = data.email;
                    this.form.phone_number = data.phone_number;
                    if (data.subscription) {
                        this.subscription = data.subscription;
                    }

                    console.log(this.subscription);
                })
                .catch((error) => {
                    console.log(error);
                }).finally(() => {
                //Perform action in always
            });
        },
        methods:{
            submitForm() {
                this.disabled = true;

                axios.patch('/users/profile', this.form)
                    .then((result) => {
                        // display success message
                        this.$alertHandler.showAlert('Your profile has been updated', result.status);
                        this.disabled = false;
                    })
                    .catch((error) => {
                        this.disabled = false;

                        // display error message
                        this.$alertHandler.showAlert(error.response.data.message || error.message, error.response.status);
                    }).finally(() => {
                    //Perform action in always
                });
            },
            displayDate(date) {
                date = new Date(date);

                // date.toLocaleString('en-US', {
                //     weekday: 'short', // long, short, narrow
                //     day: 'numeric', // numeric, 2-digit
                //     year: 'numeric', // numeric, 2-digit
                //     month: 'long', // numeric, 2-digit, long, short, narrow
                //     hour: 'numeric', // numeric, 2-digit
                //     minute: 'numeric', // numeric, 2-digit
                //     second: 'numeric', // numeric, 2-digit
                // })

                return date.toLocaleDateString('default', {
                    day: 'numeric',
                    year: 'numeric',
                    month: 'short',
                });
            },
        }
    }
</script>
