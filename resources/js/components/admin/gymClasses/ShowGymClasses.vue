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
                </tr>
                </thead>

                <tbody>
                <tr v-for="gymClass in gymClasses" @click="updateGymClass(gymClass.id)">
                    <th scope="row">{{ gymClass.id }}</th>
                    <td>{{ gymClass.name }}</td>
                    <td>{{ gymClass.description }}</td>
                    <td>{{ gymClass.teacher }}</td>
                    <td class="text-center">{{ gymClass.number_of_students }} </td>
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
        methods:{
            updateGymClass(gymClassId) {
                this.$router.push({ name: 'UpdateGymClasses', params: { id: gymClassId } })
            }
        }
    }
</script>
