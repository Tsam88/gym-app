<template>

    <div>
        <div class="mb-3">
            <h1 class="h1 d-inline align-middle">Επεξεργασία Συνδρομής Χρήστη</h1>
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
                                        <option v-for="user in users" :value="user.id">{{user.name}} {{user.surname}} - {{user.email}}</option>
                                    </select>
                                </div>

                                <div class="my-3">
                                    <label for="price">Τιμή</label>
                                    <input v-model="form.price" id="price" name="price" type="number" min="0" step="0.01" class="form-control" placeholder="Τιμή" required>
                                </div>

                                <div class="my-3">
                                    <label for="remaining_sessions">Υπόλοιπες επισκέψεις</label>
                                    <input v-model="form.remaining_sessions" id="remaining_sessions" name="remaining_sessions" type="number" min="0" step="1" class="form-control" placeholder="Υπόλοιπες επισκέψεις" required>
                                </div>

                                <div class="my-3">
                                    <label for="sessions_per_week">Αριθμός επισκέψεων ανά εβδομάδα</label>
                                    <input v-model="form.sessions_per_week" id="sessions_per_week" name="sessions_per_week" type="number" min="0" step="1" class="form-control" placeholder="Αριθμός επισκέψεων ανά εβδομάδα">
                                </div>

                                <div class="my-3">
                                    <label class="form-check">
                                        <input v-model="form.unlimited_sessions" id="unlimited_sessions" name="unlimited_sessions" class="form-check-input wave-check-input" type="checkbox" value="">
                                        <span class="form-check-label">
                                              Απεριόριστες επισκέψεις
                                        </span>
                                    </label>
                                </div>

                                <div class="my-3">
                                    <label for="starts_at">Ημερομηνία έναρξης</label>
                                    <input v-model="form.starts_at" id="starts_at" name="starts_at" type="date" class="form-control" placeholder="Ημερομηνία έναρξης" required>
                                </div>

                                <div class="my-3">
                                    <label for="expires_at">Ημερομηνία λήξης</label>
                                    <input v-model="form.expires_at" id="expires_at" name="expires_at" type="date" class="form-control" placeholder="Ημερομηνία λήξης" required>
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
                    price: null,
                    remaining_sessions: null,
                    sessions_per_week: null,
                    unlimited_sessions: null,
                    starts_at: null,
                    expires_at: null
                },
                users: [],
                subscriptions: [],
                id: this.$route.params.id,
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
                    // error.response.status Check status code
                    // alert(error.response.data.errors['name'][0]);
                    // for each errors -> display
                    console.log(error);
                    // console.log(error.response.data.errors['name'][0]);
                }).finally(() => {
                //Perform action in always
            });

            // get subscription
            axios.get('/admin/subscriptions/' + this.id)
                .then(({data}) => {
                    //Perform Success Action
                    this.form.user_id = data.user_id;
                    this.form.price = data.price;
                    this.form.remaining_sessions = data.remaining_sessions;
                    this.form.sessions_per_week = data.sessions_per_week;
                    this.form.unlimited_sessions = data.unlimited_sessions;
                    this.form.starts_at = data.starts_at;
                    this.form.expires_at = data.expires_at;
                })
                .catch((error) => {
                    // for each errors -> display
                    console.log(error);
                    // console.log(error.response.data.errors);
                }).finally(() => {
                //Perform action in always
            });
        },
        methods:{
            submitForm() {
                axios.patch('/admin/subscriptions/' + this.id, this.form)
                    .then((res) => {
                        //Perform Success Action
                        this.$router.push({ name: 'ShowSubscriptions' });
                    })
                    .catch((error) => {
                        // error.response.status Check status code
                        // for each errors -> display
                        console.log(error.response);
                        // console.log(error.response.data.errors['name'][0]);
                    }).finally(() => {
                    //Perform action in always
                });
            }
        }
    }
</script>
