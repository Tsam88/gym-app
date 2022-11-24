<template>
    <div>
        <div v-if="!isLoading" class="mb-3">
            <h1 class="h1 d-inline align-middle">Προγράμματα Συνδρομής</h1>
        </div>

        <!-- loader -->
        <div v-if="isLoading" class="row p-7">
            <b-spinner class="spinner-size-default" variant="info"></b-spinner>
        </div>

        <Dropdown
            :options="usersList"
            @selected="selectOption"
            :disabled="false"
            :maxItem="10"
            placeholder="Please select an option">
        </Dropdown>

        <div v-if="!isLoading" class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Όνομα</th>
                    <th scope="col">Επίθετο</th>
                    <th scope="col">Τηλέφωνο</th>
                    <th class="text-center" scope="col">Τιμή</th>
                    <th class="text-center" scope="col">Υπόλοιπες επισκέψεις </th>
                    <th class="text-center" scope="col">Επισκέψεις ανά εβδομάδα</th>
                    <th class="text-center" scope="col">Έναρξη</th>
                    <th class="text-center" scope="col">Λήξη</th>
                    <th class="text-center" scope="col">Απεριόριστο</th>
                    <th class="text-center" scope="col">Ενεργή</th>
                </tr>
                </thead>

                <tbody>
                <tr v-for="subscription in subscriptions" @click="updateSubscription(subscription.id)">
                    <th scope="row">{{ subscription.id }}</th>
                    <td>{{ subscription.user_name }}</td>
                    <td>{{ subscription.user_surname }}</td>
                    <td>{{ subscription.user_phone_number }}</td>
                    <td class="text-center">{{ subscription.price }} </td>
                    <td class="text-center">{{ subscription.remaining_sessions }}</td>
                    <td class="text-center" >{{ subscription.sessions_per_week }}</td>
                    <td class="text-center" >{{ subscription.starts_at }}</td>
                    <td class="text-center" >{{ subscription.expires_at }}</td>
                    <td>
                        <label class="form-check d-flex justify-content-center">
                            <input class="form-check-input wave-check-input-disabled" type="checkbox" value="" :checked="subscription.unlimited_sessions == true" disabled>
                        </label>
                    </td>
                    <td>
                        <label class="form-check d-flex justify-content-center">
                            <input class="form-check-input wave-check-input-disabled" type="checkbox" value="" :checked="subscription.is_active == true" disabled>
                        </label>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                form: {
                    user_id: null,
                    only_active_subscriptions: true,
                },
                usersList: [],
                subscriptions: [],
                isLoading: true,
            }
        },
        mounted() {
            this.getSubscriptions();
            this.getUsers();
        },
        methods:{
            selectOption(option) {
                if (this.form.user_id !== option.id) {
                    this.form.user_id = option.id;

                    this.getSubscriptions();
                }
            },
            updateSubscription(subscriptionId) {
                this.$router.push({ name: 'UpdateSubscriptions', params: { id: subscriptionId } })
            },
            getSubscriptions() {
                // loader
                this.isLoading = true;
                this.subscriptions = [];

                let params = this.form.user_id ? '?user_id='+this.form.user_id : '';

                axios.get('/admin/subscriptions' + params)
                    .then((results) => {
                        results.data.data.forEach((value, index) => {
                            this.subscriptions.push(value);
                        });

                        // loader
                        this.isLoading = false;
                    })
                    .catch((error) => {
                        console.log(error);
                    }).finally(() => {
                    //Perform action in always
                });
            },
            getUsers() {
                axios.get('/admin/users')
                    .then(({data}) => {
                        data.data.forEach((value, index) => {
                            let item = value.name + ' ' + value.surname + ' - ' + value.email;

                            this.usersList.push({id: value.id, name: item});
                        });
                    })
                    .catch((error) => {
                        console.log(error);
                    }).finally(() => {
                    //Perform action in always
                });
            },
        }
    }
</script>
