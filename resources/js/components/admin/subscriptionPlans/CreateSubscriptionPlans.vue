<template>

    <div>
        <div class="mb-3">
            <h1 class="h1 d-inline align-middle">Δημιουργία Προγράμματος Συνδρομής</h1>
        </div>

        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-body">

                        <form v-on:submit.prevent="submitForm">
                            <div class="form-group">

                                <div class="mb-3">
                                    <label for="name">Όνομα</label>
                                    <input v-model="form.name" id="name" name="name" type="text" class="form-control" placeholder="Όνομα">
                                </div>

                                <div class="my-3">
                                    <label for="plan_price">Τιμή</label>
                                    <input v-model="form.plan_price" id="plan_price" name="plan_price" type="number" min="0" class="form-control" placeholder="Τιμή">
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
                                    <label class="form-check">
                                        <input v-model="form.display_on_page" id="display_on_page" name="display_on_page" class="form-check-input wave-check-input" type="checkbox" value="true">
                                        <span class="form-check-label">
                                              Εμφάνιση στη σελίδα
                                        </span>
                                    </label>
                                </div>

                                <div class="mt-3">
                                    <input id="submit_subscription_plan" name="submit_subscription_plan" class="btn btn-primary button-color-wave" type="submit" value="Δημιουργία">
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
        name: 'PostFormAxios',
        data() {
            return {
                form: {
                    name: '',
                    plan_price: 0,
                    unlimited_sessions: false,
                    display_on_page: true
                }
            }
        },
        methods:{
            submitForm() {
                this.form.plan_price = parseInt(this.form.plan_price);

                axios.post('/admin/subscription-plans', this.form)
                    .then((res) => {
                        //Perform Success Action
                        console.log(res);
                        console.log(res.response.message);
                    })
                    .catch((error) => {
                        // error.response.status Check status code
                        alert(error.response.data.errors['name'][0]);
                        // for each errors -> display
                        console.log(error.response.status);
                        console.log(error.response.data.errors['name'][0]);
                    }).finally(() => {
                    //Perform action in always
                });
            }
        }
    }
</script>
