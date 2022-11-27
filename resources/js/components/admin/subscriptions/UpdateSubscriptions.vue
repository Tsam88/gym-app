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
                                    <v-select v-model="form.user_id" :options="users" :reduce="name => name.id" label="name" id="user_id" placeholder="Επιλογή χρήστη">
                                        <template #search="{attributes, events}">
                                            <input
                                                class="vs__search"
                                                :required="!form.user_id"
                                                v-bind="attributes"
                                                v-on="events"
                                            />
                                        </template>
                                    </v-select>
                                </div>

                                <div class="my-3">
                                    <label for="price">Τιμή</label>
                                    <input v-model="form.price" id="price" name="price" type="number" min="0" step="0.01" class="form-control" placeholder="Τιμή" required>
                                </div>

                                <div class="my-3">
                                    <label for="remaining_sessions">Υπόλοιπες επισκέψεις</label>
                                    <input v-model="form.remaining_sessions" id="remaining_sessions" name="remaining_sessions" :disabled="this.form.unlimited_sessions === true" type="number" min="0" step="1" class="form-control" placeholder="Υπόλοιπες επισκέψεις" required>
                                </div>

                                <div class="my-3">
                                    <label for="sessions_per_week">Αριθμός επισκέψεων ανά εβδομάδα</label>
                                    <input v-model="form.sessions_per_week" id="sessions_per_week" name="sessions_per_week" :disabled="this.form.unlimited_sessions === false" type="number" min="0" step="1" class="form-control" placeholder="Αριθμός επισκέψεων ανά εβδομάδα">
                                </div>

                                <div class="my-3">
                                    <label class="form-check">
                                        <input v-model="form.unlimited_sessions" @change="toggleUnlimitedSessionsCheckBox" id="unlimited_sessions" name="unlimited_sessions" class="form-check-input wave-check-input" type="checkbox">
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
            this.getUsers();

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
                    console.log(error);
                }).finally(() => {
                //Perform action in always
            });
        },
        methods:{
            getUsers() {
                axios.get('/admin/users')
                    .then(({data}) => {
                        data.data.forEach((value, index) => {
                            let item = value.name + ' ' + value.surname + ' - ' + value.email;

                            this.users.push({id: value.id, name: item});
                        });
                    })
                    .catch((error) => {
                        console.log(error);
                    }).finally(() => {
                    //Perform action in always
                });
            },
            submitForm() {
                axios.patch('/admin/subscriptions/' + this.id, this.form)
                    .then((result) => {
                        //Perform Success Action
                        this.$router.push({ name: 'ShowSubscriptions' });

                        // display success message
                        this.$alertHandler.showAlert('Subscription updated successfully', result.status);
                    })
                    .catch((error) => {
                        // display error message
                        this.$alertHandler.showAlert(error.response.data.message || error.message, error.response.status);
                    }).finally(() => {
                    //Perform action in always
                });
            },
            toggleUnlimitedSessionsCheckBox() {
                if (this.form.unlimited_sessions === true) {
                    this.form.remaining_sessions = null;
                } else {
                    this.form.sessions_per_week = null;
                }
            },
        }
    }
</script>
