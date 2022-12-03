<template>

    <div>
        <div class="mb-3">
            <h1 class="h1 d-inline align-middle">Επεξεργασία Τμήματος</h1>
        </div>

        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-body">

                        <form @submit.prevent="submitForm">
                            <div class="form-group">

                                <div class="mb-3">
                                    <label for="name">Όνομα</label>
                                    <input v-model="form.name" id="name" name="name" type="text" class="form-control" placeholder="Όνομα" required>
                                </div>

                                <div class="my-3">
                                    <label for="description">Περιγραφή</label>
                                    <input v-model="form.description" id="description" name="description" type="text" class="form-control" placeholder="Περιγραφή">
                                </div>

                                <div class="my-3">
                                    <label for="teacher">Δάσκαλος</label>
                                    <input v-model="form.teacher" id="teacher" name="teacher" type="text" class="form-control" placeholder="Δάσκαλος">
                                </div>

                                <div class="my-3">
                                    <label for="number_of_months">Αριθμός μαθητών</label>
                                    <input v-model="form.number_of_students" id="number_of_months" name="number_of_months" type="number" min="1" step="1" class="form-control" placeholder="Αριθμός μαθητών" required>
                                </div>

                                <div class="my-3">
                                    <label for="group_dates">Ημέρες και ώρες</label>
                                    <div v-for="(week_day, index) in form.week_days" id="group_dates" class="form-group wave-group-dates mx-0 mb-3 row">
                                        <div class="mt-2 col-sm-12 col-lg-4">
                                            <label :for="'day'+index">Ημέρα</label>
                                            <select v-model="week_day.day" :id="'day'+index" :name="'day'+index" class="form-select mb-3" required>
                                                <option value="" hidden>Επιλογή ημέρας</option>
                                                <option value=MONDAY>Δευτέρα</option>
                                                <option value=TUESDAY>Τρίτη</option>
                                                <option value=WEDNESDAY>Τετάρτη</option>
                                                <option value=THURSDAY>Πέμπτη</option>
                                                <option value=FRIDAY>Παρασκευή</option>
                                                <option value=SATURDAY>Σάββατο</option>
                                                <option value=SUNDAY>Κυριακή</option>
                                            </select>
                                        </div>

                                        <div class="my-2 col-sm-12 col-lg-4">
                                            <label :for="'start_time'+index">Ώρα έναρξης</label>
                                            <input v-model="week_day.start_time" :id="'start_time'+index" :name="'start_time'+index" type="time" class="form-control" placeholder="Ώρα έναρξης" required>
                                        </div>

                                        <div class="my-2 col-sm-12 col-lg-4">
                                            <label :for="'end_time'+index">Ώρα λήξης</label>
                                            <input v-model="week_day.end_time" :id="'end_time'+index" :name="'end_time'+index" type="time" class="form-control" placeholder="Ώρα λήξης" required>
                                        </div>

                                        <div class="mb-2">
                                            <b-button @click="removeDate(index)" class="btn btn-primary danger-button-color-wave float-right" :disabled="form.week_days.length === 1">
                                                Αφαίρεση
                                            </b-button>
                                        </div>
                                    </div>
                                </div>

                                <div class="">
                                    <b-button @click="addDate" class="btn btn-primary button-color-wave float-right">Πρόσθεσε ημερομηνία</b-button>
                                </div>

                                <div class="mt-6">
                                    <b-button class="btn btn-primary button-color-wave" type="submit" variant="primary">Αποθήκευση</b-button>
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
                    name: null,
                    description: null,
                    teacher: null,
                    number_of_students: 0,
                    week_days: []
                },
                id: this.$route.params.id,
            }
        },
        mounted() {
            axios.get('/admin/gym-classes/' + this.id)
                .then(({data}) => {
                    //Perform Success Action
                    this.form.name = data.name;
                    this.form.description = data.description;
                    this.form.teacher = data.teacher;
                    this.form.number_of_students = data.number_of_students;

                    data.week_days.forEach((value, index) => {
                        this.form.week_days.push(value);
                    });
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
                if (confirm('Every reservation for deleted or updated class dates (date or time) are going to be declined and students with reservation on an old class date will receive an email for declined class! Are you sure?')) {
                    this.form.number_of_students = parseInt(this.form.number_of_students);

                    axios.patch('/admin/gym-classes/' + this.id, this.form)
                        .then((result) => {
                            //Perform Success Action
                            this.$router.push({ name: 'ShowGymClasses' });

                            // display success message
                            this.$alertHandler.showAlert('Class updated successfully', result.status);
                        })
                        .catch((error) => {
                            console.log(error);
                            // display error message
                            this.$alertHandler.showAlert(error.response.data.message || error.message, error.response.status);
                        }).finally(() => {
                        //Perform action in always
                    });
                }
            },
            addDate() {
                this.form.week_days.push({day:"", start_time:null, end_time:null}); // what to push unto the rows array?
            },
            removeDate(index) {
                if (this.form.week_days.length > 1) {
                    this.form.week_days.splice(index, 1)
                }
            }
        }
    }
</script>
