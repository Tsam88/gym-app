<template>
    <div>
        <div v-if="!isLoading" class="mb-3">
            <h1 class="h1 d-inline align-middle">Προγράμματα Συνδρομής</h1>
        </div>

        <!-- loader -->
        <div v-if="isLoading" class="row p-7">
            <b-spinner class="spinner-size-default" variant="info"></b-spinner>
        </div>

        <div v-if="!isLoading" class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Τίτλος</th>
                    <th class="text-center" scope="col">Τιμή</th>
                    <th class="text-center" scope="col">Μήνες</th>
                    <th class="text-center" scope="col">Επισκέψεις</th>
                    <th class="text-center" scope="col">Επισκέψεις ανά εβδομάδα</th>
                    <th class="text-center" scope="col">Απεριόριστο</th>
                    <th class="text-center" scope="col">Εμφάνιση στη σελίδα</th>
                </tr>
                </thead>

                <tbody>
                <tr v-for="subscriptionPlan in subscriptionPlans" @click="updateSubscriptionPlan(subscriptionPlan.id)">
                    <th scope="row">{{ subscriptionPlan.id }}</th>
                    <td>{{ subscriptionPlan.name }}</td>
                    <td class="text-center">{{ subscriptionPlan.plan_price }} </td>
                    <td class="text-center">{{ subscriptionPlan.number_of_months }}</td>
                    <td class="text-center" >{{ subscriptionPlan.number_of_sessions }}</td>
                    <td class="text-center" >{{ subscriptionPlan.sessions_per_week }}</td>
                    <td>
                        <label class="form-check d-flex justify-content-center">
                            <input class="form-check-input wave-check-input-disabled" type="checkbox" value="" :checked="subscriptionPlan.unlimited_sessions == true" disabled>
                        </label>
                    </td>
                    <td>
                        <label class="form-check d-flex justify-content-center">
                            <input class="form-check-input wave-check-input-disabled" type="checkbox" value="" :checked="subscriptionPlan.display_on_page == true" disabled>
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
                subscriptionPlans: [],
                isLoading: true,
            }
        },
        mounted() {
            axios.get('/admin/subscription-plans', this.form)
                .then((results) => {
                    results.data.data.forEach((value, index) => {
                        this.subscriptionPlans.push(value);
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
        methods:{
            updateSubscriptionPlan(subscriptionPlanId) {
                this.$router.push({ name: 'UpdateSubscriptionPlans', params: { id: subscriptionPlanId } })
            }
        }
    }
</script>
