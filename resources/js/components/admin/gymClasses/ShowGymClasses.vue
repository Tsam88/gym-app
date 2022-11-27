<template>
    <div>
        <div v-if="!isLoading" class="mb-3">
            <h1 class="h1 d-inline align-middle">Τμήματα</h1>
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
                    <th scope="col">Περιγραφή</th>
                    <th scope="col">Δάσκαλος</th>
                    <th class="text-center" scope="col">Αριθμός μαθητών</th>
                    <th class="text-center" scope="col">Delete</th>
                </tr>
                </thead>

                <tbody>
                <tr v-for="gymClass in gymClasses" @click="updateGymClass(gymClass.id, $event)">
                    <th scope="row">{{ gymClass.id }}</th>
                    <td>{{ gymClass.name }}</td>
                    <td>{{ gymClass.description }}</td>
                    <td>{{ gymClass.teacher }}</td>
                    <td class="text-center">{{ gymClass.number_of_students }} </td>
                    <td class="text-center">
                        <b-button @click="deleteGymClass(gymClass)" class="btn btn-primary danger-button-color-wave">
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
                gymClasses: [],
                isLoading: true,
            }
        },
        mounted() {
            this.getGymClasses();
        },
        methods:{
            getGymClasses() {
                this.gymClasses = [];

                axios.get('/admin/gym-classes', this.form)
                    .then((results) => {
                        results.data.data.forEach((value, index) => {
                            this.gymClasses.push(value);
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
            updateGymClass(gymClassId, event) {
                if (["button", "svg", "path"].includes(event.target.tagName.toLowerCase())) {
                    return;
                }

                this.$router.push({ name: 'UpdateGymClasses', params: { id: gymClassId } })
            },
            deleteGymClass(gymClass) {
                if (confirm('Every reservation in a future date for this gym class is going to be declined and users will receive an email for declined class! Are you sure, you want to delete this gym class (' + gymClass.name + ')?' )) {
                    axios.delete('/admin/gym-classes/' + gymClass.id)
                        .then((result) => {
                            //Perform Success Action
                            // loader
                            this.isLoading = true;

                            // display success message
                            this.$alertHandler.showAlert('Gym class deleted successfully', result.status);
                        })
                        .catch((error) => {
                            // display error message
                            this.$alertHandler.showAlert(error.response.data.message || error.message, error.response.status);
                        }).finally(() => {
                        //Perform action in always
                    });

                    this.getGymClasses();
                }
            }
        }
    }
</script>
