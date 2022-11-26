<template>
    <div>
        <div v-if="!isLoading" class="mb-3">
            <h1 class="h1 d-inline align-middle">Excluded Calendar Dates</h1>
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
                    <th class="text-center" scope="col">Start date</th>
                    <th class="text-center" scope="col">End date</th>
                    <th class="text-center" scope="col">Extend Subscription</th>
                    <th class="text-center" scope="col">Delete</th>
                </tr>
                </thead>

                <tbody>
                <tr v-for="excludedCalendarDate in excludedCalendarDates" @click="updateExcludedCalendarDate(excludedCalendarDate.id, $event)">
                    <th scope="row">{{ excludedCalendarDate.id }}</th>
                    <td class="text-center">{{ excludedCalendarDate.start_date }} </td>
                    <td class="text-center">{{ excludedCalendarDate.end_date }}</td>
                    <td>
                        <label class="form-check d-flex justify-content-center">
                            <input class="form-check-input wave-check-input-disabled" type="checkbox" value="" :checked="excludedCalendarDate.extend_subscription == true" disabled>
                        </label>
                    </td>
                    <td class="text-center">
                        <b-button @click="deleteExcludedCalendarDate(excludedCalendarDate)" class="btn btn-primary danger-button-color-wave">
                            <font-awesome-icon icon="fa-regular fa-trash-can"/>
                        </b-button>
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
                excludedCalendarDates: [],
                isLoading: true,
            }
        },
        mounted() {
            this.getExcludedCalendarDates();
        },
        methods:{
            getExcludedCalendarDates() {
                this.excludedCalendarDates = [];

                axios.get('/admin/excluded-calendar-dates', this.form)
                    .then((results) => {
                        results.data.data.forEach((value, index) => {
                            this.excludedCalendarDates.push(value);
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
            updateExcludedCalendarDate(excludedCalendarDateId, event) {
                if (["button", "svg", "path"].includes(event.target.tagName.toLowerCase())) {
                    return;
                }

                this.$router.push({ name: 'UpdateExcludedCalendarDates', params: { id: excludedCalendarDateId } })
            },
            deleteExcludedCalendarDate(excludedCalendarDate) {
                    if (confirm('Every pending reservation that is included in these excluded dates is going to be declined and users will receive an email for declined class! Are you sure, you want to delete these excluded calendar dates (' + excludedCalendarDate.start_date + ' - ' + excludedCalendarDate.end_date + ')?')) {
                    axios.delete('/admin/excluded-calendar-dates/' + excludedCalendarDate.id)
                        .then((result) => {
                            //Perform Success Action
                            // loader
                            this.isLoading = true;

                            // display success message
                            this.$alertHandler.showAlert('Excluded calendar dates deleted successfully', result.status);
                        })
                        .catch((error) => {
                            // display error message
                            this.$alertHandler.showAlert(error.response.data.message || error.message, error.response.status);
                        }).finally(() => {
                        //Perform action in always
                    });

                    this.getExcludedCalendarDates();
                }
            }
        }
    }
</script>
