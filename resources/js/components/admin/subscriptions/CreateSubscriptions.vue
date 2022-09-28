<template>

    <div>
        <div class="mb-3">
            <h1 class="h1 d-inline align-middle">Δημιουργία Συνδρομής Χρήστη</h1>
        </div>

        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-body">

                        <form v-on:submit.prevent="submitForm">
                            <div class="form-group">

                                <div class="mb-3">
                                    <label for="user_id">Χρήστης</label>
                                    <select v-model="form.user_id" id="user_id" name="user_id" class="form-select mb-3" required>
                                        <option value=0 selected>Επιλογή χρήστη</option>
                                        <option v-for="user in users" :value="user.id">{{user.name}} {{user.surname}} - {{user.email}}</option>
                                    </select>
                                </div>

                                <div class="my-3">
                                    <label for="subscription_plan_id">Συνδρομή</label>
                                    <select v-model="form.subscription_plan_id" id="subscription_plan_id" name="subscription_plan_id" class="form-select mb-3" required>
                                        <option v-for="subscription in subscriptions" :value="subscription.id">{{subscription.name}}</option>
                                    </select>
                                </div>

                                <div class="my-3">
                                    <label for="starts_at">Ημερομηνία έναρξης</label>
                                    <input v-model="form.starts_at" id="starts_at" name="starts_at" type="date" class="form-control" placeholder="Ημερομηνία έναρξης" required>
                                </div>

                                <div class="mt-3">
                                    <input id="submit_subscription" name="submit_subscription" class="btn btn-primary button-color-wave" type="submit" value="Αποθήκευση">
                                </div>

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
                    user_id: null,
                    subscription_plan_id: null,
                    starts_at: null
                },
                users: [],
                subscriptions: []
            }
        },
        mounted() {
            // get users
            axios.get('/admin/users', this.form)
                .then((results) => {
                    results.data.data.forEach((value, index) => {
                        this.users.push(value);
                    });
                })
                .catch((error) => {
                    console.log(error);
                }).finally(() => {
                //Perform action in always
            });

            // get subscriptions
            axios.get('/admin/subscription-plans', this.form)
                .then((results) => {
                    results.data.data.forEach((value, index) => {
                        this.subscriptions.push(value);
                    });
                })
                .catch((error) => {
                    console.log(error);
                }).finally(() => {
                //Perform action in always
            });
        },
        methods:{
            submitForm() {
                axios.post('/admin/subscriptions', this.form)
                    .then((result) => {
                        //Perform Success Action
                        this.$router.push({ name: 'ShowSubscriptions' });

                        // display success message
                        this.$alertHandler.showAlert('Subscription created successfully', result.status);
                    })
                    .catch((error) => {
                        // display error message
                        this.$alertHandler.showAlert(error.response.data.message || error.message, error.response.status);
                    }).finally(() => {
                    //Perform action in always
                });
            }
        }
    }
</script>
