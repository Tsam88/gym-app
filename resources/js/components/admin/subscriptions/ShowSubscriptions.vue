<template>
    <div>
        <div class="mb-3">
            <h1 class="h1 d-inline align-middle">Προγράμματα Συνδρομής</h1>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Όνομα</th>
                    <th scope="col">Επίθετο</th>
                    <th scope="col">Email</th>
                    <th class="text-center" scope="col">Τιμή</th>
                    <th class="text-center" scope="col">Υπόλοιπες επισκέψεις </th>
                    <th class="text-center" scope="col">Επισκέψεις ανά εβδομάδα</th>
                    <th class="text-center" scope="col">Έναρξη</th>
                    <th class="text-center" scope="col">Λήξη</th>
                    <th class="text-center" scope="col">Απεριόριστο</th>
                </tr>
                </thead>

                <tbody>
                    <tr v-for="subscription in subscriptions" @click="updateSubscription(subscription.id)">
                        <th scope="row">{{ subscription.id }}</th>
                        <td>{{ subscription.user.name }}</td>
                        <td>{{ subscription.user.surname }}</td>
                        <td>{{ subscription.user.email }}</td>
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
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'PostFormAxios',
        data() {
            return {
                subscriptions: []
            }
        },
        mounted() {
            axios.get('/admin/subscriptions', this.form)
                .then((results) => {
                    results.data.data.forEach((value, index) => {
                        this.subscriptions.push(value);
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
        },
        methods:{
            updateSubscription(subscriptionId) {
                this.$router.push({ name: 'UpdateSubscriptions', params: { id: subscriptionId } })
            }
        }
    }
</script>
