<template>
    <div>
        <div v-if="!isLoading" class="mb-3">
            <h1 class="h1 d-inline align-middle">Προγράμματα Συνδρομής</h1>
        </div>

        <!-- loader -->
        <div v-if="isLoading" class="row p-7">
            <b-spinner class="spinner-size-default" variant="info"></b-spinner>
        </div>

        <div v-show="!isLoading" class="row mt-4 mb-4 justify-content-end">
            <div class="col-lg-3 pt-1">
                <div class="float-right">
                    <label class="form-check">
                    <span class="form-check-label">
                        <b>Only active subscriptions</b>
                    </span>
                        <!--                    <input v-model="form.only_active_subscriptions" @change="toggleUnlimitedSessionsCheckBox" id="unlimited_sessions" name="unlimited_sessions" class="form-check-input wave-check-input" type="checkbox" :checked="only_active_subscriptions === true">-->
                        <input v-model="form.only_active_subscriptions" @change="getSubscriptions()" id="only_active_subscriptions" name="only_active_subscriptions" class="form-check-input wave-check-input" type="checkbox">
                    </label>
                </div>
            </div>

            <div class="col-lg-3 ms-2">
                <Dropdown
                    class="autocomplete-dropdown autocomplete-dropdown-subscriptions"
                    :options="usersList"
                    @selected="selectOption"
                    :disabled="false"
                    :maxItem="10"
                    placeholder="Search by name or email">
                </Dropdown>
            </div>
        </div>

        <div v-if="!isLoading" class="row table-responsive">
            <table class="table table-hover">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Όνομα</th>
                    <th scope="col">Επίθετο</th>
                    <th scope="col">Email</th>
                    <th scope="col">Τηλέφωνο</th>
                    <th class="text-center" scope="col">Τιμή</th>
                    <th class="text-center" scope="col">Υπόλοιπες επισκέψεις </th>
                    <th class="text-center" scope="col">Επισκέψεις ανά εβδομάδα</th>
                    <th class="text-center" scope="col">Έναρξη</th>
                    <th class="text-center" scope="col">Λήξη</th>
                    <th class="text-center" scope="col">Απεριόριστο</th>
                    <th class="text-center" scope="col">Ενεργή</th>
                    <th class="text-center" scope="col">Delete</th>
                </tr>
                </thead>

                <tbody>
                <tr v-for="subscription in subscriptions" @click="updateSubscription(subscription.id, $event)">
                    <th scope="row">{{ subscription.id }}</th>
                    <td>{{ subscription.user_name }}</td>
                    <td>{{ subscription.user_surname }}</td>
                    <td>{{ subscription.user_email }}</td>
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
                    <td class="text-center">
                        <b-button @click="deleteSubscription(subscription)" class="btn btn-primary danger-button-color-wave">
                            <font-awesome-icon icon="fa-regular fa-trash-can"/>
                        </b-button>
                    </td>
                </tr>
                </tbody>
            </table>

            <nav aria-label="Subscriptions page navigation">
                <ul class="pagination justify-content-end mt-5">
                    <li class="page-item" :class="{'disabled':form.page === 1}" @click="changePage(form.page - 1)">
                        <a class="page-link">Previous</a>
                    </li>
                    <li v-for="page_number in lastPage" @click="changePage(page_number)" class="page-item" :class="{'active':form.page === page_number}">
                        <a class="page-link">{{page_number}}</a>
                    </li>
                    <li class="page-item" :class="{'disabled':form.page === lastPage}" @click="changePage(form.page + 1)">
                        <a class="page-link">Next</a>
                    </li>
                </ul>
            </nav>
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
                    items_per_page: 15,
                    page: 1,
                },
                usersList: [{id: null, name: 'All users'}],
                subscriptions: [],
                isLoading: true,
                lastPage: null,
            }
        },
        created() {
            this.getSubscriptions();
            this.getUsers();
        },
        methods:{
            selectOption(option) {
                if (option.id !== undefined && this.form.user_id !== option.id) {
                    this.form.user_id = option.id;
                    this.form.page = 1;

                    this.getSubscriptions();
                }
            },
            changePage(pageNumber) {
                // check if the requested page number is between 1 and last page number
                if (pageNumber >= 1 && pageNumber <= this.lastPage) {
                    this.form.page = pageNumber;
                    this.getSubscriptions();
                }
            },
            updateSubscription(subscriptionId, event) {
                if (["button", "svg", "path"].includes(event.target.tagName.toLowerCase())) {
                    return;
                }

                this.$router.push({ name: 'UpdateSubscriptions', params: { id: subscriptionId } })
            },
            getSubscriptions() {
                // loader
                this.isLoading = true;
                this.subscriptions = [];

                // cast bool value to integer, because laravel validator can not validate string "true" and "false" values
                this.form.only_active_subscriptions = this.form.only_active_subscriptions ? 1 : 0;

                axios.get('/admin/subscriptions', {params: this.form})
                    .then((results) => {
                        results.data.data.forEach((value, index) => {
                            this.subscriptions.push(value);
                        });

                        this.lastPage = results.data.last_page;

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
            deleteSubscription(subscription) {
                if (confirm('Every pending reservation for this subscription is going to be declined and user (' + subscription.user_surname + ' ' + subscription.user_name + ' - ' + subscription.user_email + ') will receive an email for declined class! Are you sure, you want to delete this user subscription for date range ' + subscription.starts_at + ' - ' + subscription.expires_at + '?' )) {
                    axios.delete('/admin/subscriptions/' + subscription.id)
                        .then((result) => {
                            //Perform Success Action
                            // loader
                            this.isLoading = true;

                            // display success message
                            this.$alertHandler.showAlert('User subscription deleted successfully', result.status);
                        })
                        .catch((error) => {
                            // display error message
                            this.$alertHandler.showAlert(error.response.data.message || error.message, error.response.status);
                        }).finally(() => {
                        //Perform action in always
                    });

                    this.getSubscriptions();
                }
            }
        }
    }
</script>
