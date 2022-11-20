<template>

    <div>
        <div class="mb-3">
            <h1 class="h1 d-inline align-middle">Update Excluded Calendar Dates</h1>
        </div>

        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-body">

                        <form v-on:submit.prevent="submitForm">
                            <div class="form-group">

                                <div class="my-3">
                                    <label for="gym_class_ids">Classes</label>
                                    <select v-model="form.gym_class_ids" id="gym_class_ids" name="gym_class_ids" class="form-select mb-3" size="5" multiple required>
                                        <option value="-1" hidden>All classes</option>
                                        <option v-for="gymClass in gymClasses" :value="gymClass.id">{{gymClass.name}}</option>
                                    </select>
                                </div>

                                <div class="my-3">
                                    <label for="start_date">Start date</label>
                                    <input v-model="form.start_date" id="start_date" name="start_date" type="date" class="form-control" placeholder="Start date" required>
                                </div>

                                <div class="my-3">
                                    <label for="end_date">End date</label>
                                    <input v-model="form.end_date" id="end_date" name="starts_at" type="date" class="form-control" placeholder="End date" required>
                                </div>

                                <div class="my-3">
                                    <label class="form-check">
                                        <input v-model="form.extend_subscription" id="extend_subscription" name="extend_subscription" class="form-check-input wave-check-input" type="checkbox">
                                        <span class="form-check-label">
                                              Extend subscription
                                        </span>
                                    </label>
                                </div>

                                <div class="mt-3">
                                    <input id="submit_excluded_calendar_dates" name="submit_excluded_calendar_dates" class="btn btn-primary button-color-wave" type="submit" value="Save">
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
                    gym_class_ids: [],
                    start_date: null,
                    end_date: null,
                    extend_subscription: false,
                },
                gymClasses: [],
                id: this.$route.params.id,
            }
        },
        mounted() {
            // get gym classes
            axios.get('/admin/gym-classes', this.form)
                .then((results) => {
                    results.data.data.forEach((value, index) => {
                        this.gymClasses.push(value);
                    });
                })
                .catch((error) => {
                    console.log(error);
                }).finally(() => {
                //Perform action in always
            });

            // get excluded calendar date
            axios.get('/admin/excluded-calendar-dates/' + this.id)
                .then(({data}) => {
                    //Perform Success Action
                    this.form = data;
                })
                .catch((error) => {
                    // display error message
                    this.$alertHandler.showAlert(error.response.data.message || error.message, error.response.status);
                }).finally(() => {
                //Perform action in always
            });
        },
        methods:{
            submitForm() {
                axios.patch('/admin/excluded-calendar-dates/' + this.id, this.form)
                    .then((result) => {
                        //Perform Success Action
                        this.$router.push({ name: 'ShowExcludedCalendarDates' });

                        // display success message
                        this.$alertHandler.showAlert('Excluded calendar dates updated successfully', result.status);
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
