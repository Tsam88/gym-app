<template>
    <div>
        <div class="mb-3">
            <h1 class="h1 d-inline align-middle">Τμήματα</h1>
        </div>

        <div class="table-responsive">
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
                gymClasses: []
            }
        },
        mounted() {
            axios.get('/admin/gym-classes', this.form)
                .then((results) => {
                    results.data.data.forEach((value, index) => {
                        this.gymClasses.push(value);
                    });
                })
                .catch((error) => {
                    // error.response.status Check status code
                    // for each errors -> display
                    console.log(error);
                    // console.log(error.response.data.errors['name'][0]);
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
